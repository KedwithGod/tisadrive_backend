<?php

namespace App\Http\Controllers;
use App\Models\Admin\VehicleType;
use App\Models\Admin\Zone;
use Inertia\Inertia;
use App\Models\Admin\ZoneTypePrice;
use App\Models\Admin\ZoneType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\PriceFilter;
use App\Transformers\User\ZoneTypeTransformer;
use App\Transformers\ZoneTypePackagePriceTransformer;
use App\Models\Admin\PackageType;
use App\Models\Admin\ZoneTypePackage;
use App\Base\Filters\Master\CommonMasterFilter;

class SetPriceController extends Controller
{
    public function index() 
    {
        $zones = Zone::where('active', true)->get();
        $vehicleTypes = VehicleType::where('active', true)->get();
        $show_driver_level_feature = get_settings('show_driver_level_feature') == 1;
        $show_incentive_feature_for_driver = get_settings('show_incentive_feature_for_driver') == 1;
    
        return Inertia::render('pages/set_prices/index', [
            'zones' => $zones,
            'vehicleTypes' => $vehicleTypes,
            'show_driver_level_feature'=>$show_driver_level_feature,
            'show_incentive_feature_for_driver'=>$show_incentive_feature_for_driver,
        ]);
    }
    
    
    public function list(Request $request, QueryFilterContract $queryFilter)
    {
        // dd("djbfshdf");
        $query = ZoneType::orderBy('order_number','ASC');
        // dd($query->transport_type);
    
        $results = $queryFilter->builder($query)->customFilter(new PriceFilter)->paginate();
    
        $transformedData = fractal()
            ->collection($results)
            ->transformWith(new ZoneTypeTransformer())
            ->toArray();
    
        return response()->json([
            'results' => $transformedData['data'],
            'paginator' => $results,
        ]);
    }
    
    
 
    
    

    public function create() 
    {
        $zones = Zone::where('active', true)->get();
        return Inertia::render('pages/set_prices/create', ['zones' => $zones, 'zoneTypePrice' => null]);
    }

    public function fetchVehicleTypes()
    {
        // dd(request()->zone_type_id);
        
        $zone_id = request()->zone;
        $transportType = request()->transportType;

        $zone = Zone::whereId($zone_id)->first();        


        $ids = $zone->zoneType()->pluck('type_id')->toArray();

        // dd($ids);

        $currentVehicleTypeId = request()->input('zone_type_id');

        $vehicleTypes = VehicleType::where('id', $currentVehicleTypeId)->where('is_taxi', $transportType)->get();

    
        if($currentVehicleTypeId==null)
        {
            $vehicleTypes = VehicleType::whereNotIn('id', $ids)->where('is_taxi', $transportType)->get();
        }

   
        return response()->json(['results' => $vehicleTypes]);
    }
    

    public function store(Request $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // dd($request->all());
// dd($request->all());
$transportType = $request->transport_type;

if($transportType=='all')
{
    $transportType = 'both';
}
        // dd($request);
        $zone  = Zone::whereId($request->zone_id)->first();
        $payment = implode(',', $request->payment_type);
        // To save default type
        if ($request->transport_type == 'taxi')
        {
            if ($zone->default_vehicle_type == null) {
                $zone->default_vehicle_type = $request->zone_type_id;
                $zone->save();
            }
        }else{
            if ($zone->default_vehicle_type_for_delivery == null) {
                $zone->default_vehicle_type_for_delivery = $request->zone_type_id;
                $zone->save();
            }
        }
        $zoneType = $zone->zoneType()->create([
            'type_id' => $request->vehicle_type,
            'payment_type' => $payment,
            'transport_type' => $transportType,
            'admin_commision_type' => $request->admin_commision_type,
            'admin_commision' => $request->admin_commision,
            'admin_commission_type_from_driver' => $request->admin_commission_type_from_driver,
            'admin_commission_from_driver' => $request->admin_commission_from_driver,
            'admin_commission_type_for_owner' => $request->admin_commission_type_for_owner,
            'admin_commission_for_owner' => $request->admin_commission_for_owner,
            'airport_surge' => $request->airport_surge,
            'service_tax' => $request->service_tax,
            'order_number' => $request->order_number,
            'bill_status' => true,
            'support_airport_fee' => $request->support_airport_fee,
            'support_outstation' => $request->support_outstation,
            // 'minimum_trip_distane' => $request->minimum_trip_distane,
        ]);
// dd($zoneType);
        $vehiclePrice = $zoneType->zoneTypePrice()->create([
            'price_type' => 1,
            // 'cancellation_fee' => $request->cancellation_fee ? $request->cancellation_fee : 0.00,
            'base_price' => $request->base_price,
            'price_per_distance' => $request->price_per_distance,
            'base_distance' => $request->base_distance ? $request->base_distance : 0,
            'price_per_time' => $request->price_per_time ? $request->price_per_time : 0.00,
             'waiting_charge' => $request->waiting_charge ? $request->waiting_charge : 0.00,
            'free_waiting_time_in_mins_before_trip_start' =>  $request->free_waiting_time_in_mins_before_trip_start ? $request->free_waiting_time_in_mins_before_trip_start:0,
            'free_waiting_time_in_mins_after_trip_start' =>  $request->free_waiting_time_in_mins_after_trip_start ? $request->free_waiting_time_in_mins_after_trip_start:0,
            'outstation_base_price' => $request->outstation_base_price? $request->outstation_base_price : 0.00,
            'outstation_price_per_distance' => $request->outstation_price_per_distance ? $request->outstation_price_per_distance : 0,
            'outstation_base_distance' => $request->outstation_base_distance ? $request->outstation_base_distance : 0,
            'outstation_price_per_time' => $request->outstation_price_per_time ? $request->outstation_price_per_time : 0.00,
            'cancellation_fee_for_user' => $request->cancellation_fee_for_user,
            'cancellation_fee_for_driver' => $request->cancellation_fee_for_driver,
            'fee_goes_to' => $request->fee_goes_to,
            // 'driver_get_fee_percentage' => $request->driver_get_fee_percentage,
            // 'admin_get_fee_percentage' => $request->admin_get_fee_percentage,
        ]); 
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Vehicle Price created successfully.',
            'vehiclePrice' => $vehiclePrice,
        ], 201);
    }
    public function edit($id)
    {

        $zoneType = ZoneType::find($id);



        $zoneTypePrice = $zoneType->zoneTypePrice()->first();
        // dd($zoneType);

        $zones = Zone::whereActive(true)->get(); // Assuming you want to pass all zones for the select dropdown
        $vehicleTypes = VehicleType::whereActive(true)->get(); // Assuming you want to pass all vehicle types for the select dropdown
            // dd($vehicleTypes);

        return Inertia::render(
            'pages/set_prices/create',
            [
                'zoneTypePrice' => $zoneTypePrice,
                'zoneType' => $zoneType,
                'zones' => $zones,
                'vehicleTypes' => $vehicleTypes,
            ]);
    }

    public function update(Request $request, ZoneTypePrice $zoneTypePrice) 
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
    // dd($request->all());
        $transportType = $request->transport_type;

        $payment = implode(',', $request->payment_type);
        // To save default type
        $zoneTypePrice->zoneType()->update([       
            'type_id' => $request->vehicle_type,
            'payment_type' => $payment,
            'transport_type' => $transportType,
            'admin_commision_type' => $request->admin_commision_type,
            'admin_commision' => $request->admin_commision,
            'admin_commission_type_from_driver' => $request->admin_commission_type_from_driver,
            'admin_commission_from_driver' => $request->admin_commission_from_driver,
            'admin_commission_type_for_owner' => $request->admin_commission_type_for_owner,
            'admin_commission_for_owner' => $request->admin_commission_for_owner,
            'service_tax' => $request->service_tax,
            'airport_surge' => $request->airport_surge,
            'order_number' => $request->order_number,
            'bill_status' => true,
            'support_airport_fee' => $request->support_airport_fee,
            'support_outstation' => $request->support_outstation,
            // 'minimum_trip_distane' => $request->minimum_trip_distane,

        ]);
        // dd($zoneType);
        $vehiclePrice = $zoneTypePrice->update([
            'price_type' => 1,
            // 'cancellation_fee' => $request->cancellation_fee ? $request->cancellation_fee : 0.00,
            'base_price' => $request->base_price,
            'price_per_distance' => $request->price_per_distance,
            'base_distance' => $request->base_distance ? $request->base_distance : 0,
            'price_per_time' => $request->price_per_time ? $request->price_per_time : 0.00,
            'waiting_charge' => $request->waiting_charge ? $request->waiting_charge : 0.00,
            'free_waiting_time_in_mins_before_trip_start' =>  $request->free_waiting_time_in_mins_before_trip_start ? $request->free_waiting_time_in_mins_before_trip_start:0,
            'free_waiting_time_in_mins_after_trip_start' =>  $request->free_waiting_time_in_mins_after_trip_start ? $request->free_waiting_time_in_mins_after_trip_start:0,
            'outstation_base_price' => $request->outstation_base_price? $request->outstation_base_price : 0.00,
            'outstation_price_per_distance' => $request->outstation_price_per_distance ? $request->outstation_price_per_distance : 0,
            'outstation_base_distance' => $request->outstation_base_distance ? $request->outstation_base_distance : 0,
            'outstation_price_per_time' => $request->outstation_price_per_time ? $request->outstation_price_per_time : 0.00,
            'cancellation_fee_for_user' => $request->cancellation_fee_for_user ? $request->cancellation_fee_for_user : 0,
            'cancellation_fee_for_driver' => $request->cancellation_fee_for_driver ? $request->cancellation_fee_for_driver : 0,
            'fee_goes_to' => $request->fee_goes_to,
            // 'driver_get_fee_percentage' => $request->driver_get_fee_percentage ? $request->driver_get_fee_percentage : 0,
            // 'admin_get_fee_percentage' => $request->admin_get_fee_percentage ? $request->admin_get_fee_percentage : 0,
        ]); 

  
       // Optionally, return a response
        return response()->json([
            'successMessage' => 'Vehicle Price created successfully.',
            'vehiclePrice' => $vehiclePrice,
        ], 201);
    
    }
    public function destroy($id)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }

        $zoneTypePrice = ZoneTypePrice::where('zone_type_id', $id)->delete();

        $zoneType = ZoneType::where('id', $id)->delete();

        return response()->json([
            'successMessage' => 'Vehicle Price deleted successfully',
        ]);
    }  
    public function updateStatus(Request $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // ZoneTypePrice::where('zone_type_id', $request->id)->update(['active'=> $request->status]);
        ZoneType::where('id', $request->id)->update(['active'=> $request->status]);
        // dd($request->all());

        return response()->json([
            'successMessage' => 'Vehicle Price status updated successfully',
        ]);
    }
    public function packageIndex(ZoneType $zoneType)
    {

        $zoneTypePrice = ZoneTypePrice::where('zone_type_id', $zoneType->id)->first();
        // dd($zoneType);

        $zoneTypePackage = $zoneType->zoneTypePackages;

    
        
        return Inertia::render('pages/set_prices/packages/index', [
            'zoneTypePackage' => $zoneTypePackage,
            'zoneTypePrice' => $zoneTypePrice, // Pass the zoneTypePrice object
        ]);
    }
    
    // packageList
    public function packageList(Request $request, QueryFilterContract $queryFilter, ZoneTypePrice $zoneTypePrice)
    {
        // Ensure the relationship method is correctly defined on the ZoneTypePrice model
        $query = ZoneTypePackage::where('zone_type_id', $zoneTypePrice->zone_type_id); // Use the query builder directly
    
        // Apply the query filter and custom filter
        $filteredQuery = $queryFilter->builder($query)->customFilter(new CommonMasterFilter);
        
        // Paginate the results
        $results = $filteredQuery->paginate(); // Use per_page parameter from request or default to 15
        // dd($results->items());
        // Return the transformed data with pagination details
        return response()->json([
            'results' => $results->items(),
            'paginator' => [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'next_page_url' => $results->nextPageUrl(),
                'prev_page_url' => $results->previousPageUrl(),
            ],
        ]);
    }
    
    
    
    
    
    public function packageCreate(ZoneTypePrice $zoneTypePrice)
    {
        // dd($zoneTypePrice->zoneType->transport_type);
        $packageTypes = PackageType::where('transport_Type', $zoneTypePrice->zoneType->transport_type)
                                    ->orWhere('transport_Type', 'both')
                                    ->where('active', true)->get();
        $zoneTypePackage = $zoneTypePrice->zoneType->zoneTypePackage; 
        $zones = Zone::whereActive(true)->get(); // Assuming you want to pass all zones for the select dropdown  
        $zone_unit =  $zoneTypePrice->zoneType->zone->unit;                              

    // dd($zoneTypePackage);
        return Inertia::render('pages/set_prices/packages/create', [
            'zoneTypePrice' => $zoneTypePrice,
            'packageTypes' => $packageTypes,
            'zoneTypePackage' => $zoneTypePackage,
            'zone_unit' => $zone_unit,

        ]);
    }
    // packageStore

    public function packageStore(Request $request)
    {
        // dd($request->all());

        $created_params = $request->validate([
            'package_type_id' => 'required',
            'base_price' => 'required',
            'base_distance' => 'required',
            'distance_price_per_km' => 'required',
            'free_min' => 'required',
            'time_price_per_min' => 'required',
            'cancellation_fee' => 'required',
        ]);

        $zoneTypePrice = ZoneTypePrice::where('id', $request->zone_type_price_id)->first();

        
        
        $created_params['zone_type_id'] = $zoneTypePrice->zone_type_id;

        $created_params['cancellation_fee'] = $request->cancellation_fee;
        $created_params['free_distance'] = $request->base_distance;
        $created_params['free_min'] = 0;



        $packagePrice =  ZoneTypePackage::create($created_params);
        // dd($packagePrice);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Vehicle Price created successfully.',
            'packagePrice' => $packagePrice,
        ], 201);
    }
    public function packageEdit(ZoneTypePackage $zoneTypePackage)
    {
        $packageTypes = PackageType::where('transport_Type', $zoneTypePackage->zoneType->transport_type)
                                    ->orWhere('transport_Type', 'both')
                                    ->where('active', true)->get();
                                        
        $zoneTypePrice = $zoneTypePackage->zoneType->zoneTypePrice->first();
        $zone_unit =  $zoneTypePrice->zoneType->zone->unit; 
    // dd($zoneTypePrice);
        return Inertia::render('pages/set_prices/packages/create', [
            'zoneTypePrice' => $zoneTypePrice,
            'packageTypes' => $packageTypes,
            'zoneTypePackage' => $zoneTypePackage,
            'zone_unit' => $zone_unit,

        ]);
    }
    public function updatePackage(Request $request, ZoneTypePackage $zoneTypePackage) 
    {
// dd($zoneTypePackage);
        $updated_params = $request->validate([
            'package_type_id' => 'required',
            'base_price' => 'required',
            'base_distance' => 'required',
            'distance_price_per_km' => 'required',
            'free_min' => 'required',
            'time_price_per_min' => 'required',
        ]);
      

        $updated_params['zone_type_id'] = $zoneTypePackage->zone_type_id;
        $updated_params['zone_id'] = $zoneTypePackage->zoneType->zone_id;


        $zoneTypePackage->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Package Price  updated successfully.',
            'vehiclePrice' => $zoneTypePackage,
        ], 201);
    
    }
    public function destroyPackage(ZoneTypePackage $zoneTypePackage)
    {
        $zoneTypePackage->delete();

        return response()->json([
            'successMessage' => 'Package Price deleted successfully',
        ]);

    }
    public function updatePackageStatus(Request $request)
    {
        // dd($request->status);
        ZoneTypePackage::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Package Price status updated successfully',
        ]);
    }

    public function surge(ZoneType $zoneType)
    {

        $surge = [];
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($days as $key => $day) {
            $surge[$day] = $zoneType->zoneSurge()->where('day',$day)->get()->toArray();
        }

        return inertia('pages/set_prices/surge',['surge' => $surge,'zoneType'=>$zoneType]);
    } 
    public function updateSurge(ZoneType $zoneType,Request $request)
    {
        $zoneType->zoneSurge()->delete();
        foreach ($request->surge as $day => $surge_for_the_day) {
            foreach ($surge_for_the_day as $key => $surge) {
                if(count($surge) > 0){
                    $startTime = now()->parse($surge['start_time'])->toTimeString();
                    $endTime = now()->parse($surge['end_time'])->toTimeString();
        
                    $surge_data = [
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'value' => $surge['value'],
                        'day' => $day,
                    ];
                    $zoneType->zoneSurge()->create($surge_data);
                }
            }
        }
        $surge = [];
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($days as $key => $day) {
            $surge[$day] = $zoneType->zoneSurge()->where('day',$day)->get()->toArray();
        }
        return response()->json([
            'successMessage' => 'Set Price Surge updated successfully',
            'surge' => $surge,
        ],201);

    } 

}
