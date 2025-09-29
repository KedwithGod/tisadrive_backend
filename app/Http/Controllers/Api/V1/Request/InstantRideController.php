<?php

namespace App\Http\Controllers\Api\V1\Request;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Models\Admin\Driver;
use App\Models\Admin\ZoneType;
use App\Models\Request\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Request\RequestMeta;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\V1\BaseController;
use App\Transformers\User\EtaTransformer;
use App\Http\Requests\Request\CreateTripRequest;
use App\Transformers\Requests\TripRequestTransformer;
use App\Base\Constants\Setting\Settings;
use App\Models\User;

/**
 * @group Driver-trips-apis
 *
 * APIs for Driver-trips apis
 */
class InstantRideController extends BaseController
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
    * Create Instant Ride
    * @bodyParam pick_lat double required pikup lat of the user
    * @bodyParam pick_lng double required pikup lng of the user
    * @bodyParam drop_lat double required drop lat of the user
    * @bodyParam drop_lng double required drop lng of the user
    * @bodyParam pick_address string required pickup address of the trip request
    * @bodyParam drop_address string required drop address of the trip request
    * @bodyParam request_eta_amount double required Expected price for the ride
    * @bodyParam name string required customer name for the request
    * @bodyParam mobile number required customer contact number for the request
    * 
    * @responseFile responses/requests/instant-ride.json
    *
    */
    public function createRequest(CreateTripRequest $request)
    {
        
        $zone_detail = find_zone($request->input('pick_lat'), $request->input('pick_lng'));
        $unit = $zone_detail->unit;
        
        // Get last request's request_number
        // $request_number = $this->request->orderBy('created_at', 'DESC')->pluck('request_number')->first();
        // if ($request_number) {
        //     $request_number = explode('_', $request_number);
        //     $request_number = $request_number[1]?:000000;
        // } else {
        //     $request_number = 000000;
        // }
        // // Generate request number
        // $request_number = 'REQ_'.sprintf("%06d", $request_number+1);

        $current_timestamp = Carbon::now()->timestamp.rand(0, 99);

        $request_number = 'REQ_'.$current_timestamp;

        $service_location = $zone_detail->serviceLocation;

        $currency_code = $service_location->currency_code;
        
        $currency_symbol = $service_location->currency_symbol;

        $type_id = auth()->user()->driver->vehicle_type;
       
        if($type_id==null){

            $type_id = auth()->user()->driver->driverVehicleTypeDetail()->pluck('vehicle_type')->first();
        }     
        $zone_type_id = $zone_detail->zoneType()->where('type_id',$type_id)->pluck('id')->first();

        if(!$zone_type_id){
            $this->throwCustomException('Your Vehicle Type is not associated with this zone');
        }

        $zone_type_detail = $zone_detail->zoneType()->where('type_id',$type_id)->first();

        $eta_result = fractal($zone_type_detail, new EtaTransformer);

            
        $eta_result =json_decode($eta_result->toJson());


        $request_params = [
            'request_number'=>$request_number,
            'driver_id'=>auth()->user()->driver->id,
            'zone_type_id'=>$zone_type_id,
            'payment_opt'=>'1',
            'unit'=>$unit,
            'requested_currency_code'=>$currency_code,
            'requested_currency_symbol'=>$currency_symbol,
            'service_location_id'=>$service_location->id,
            'accepted_at'=>date('Y-m-d H:i:s'),
            'is_driver_started'=>true,
            'is_driver_arrived'=>true,
            'arrived_at'=>date('Y-m-d H:i:s'),
            'is_trip_start'=>true,
            'trip_start_time'=>date('Y-m-d H:i:s'),
            'instant_ride'=>true,
            // 'transport_type'=>'taxi',
            'on_search'=>false,
            'total_time'=>$eta_result->data->time,
            'total_distance'=>$eta_result->data->distance,
            
        ];
        $app_for = config('app.app_for');

        if($app_for!='taxi' || $app_for!='delivery')
         {
            $request_params['transport_type']='taxi';
            
         }
         if($request->has('request_eta_amount') && $request->request_eta_amount){

           $request_params['request_eta_amount'] = $request->request_eta_amount;

        }

        $request_params['company_key'] = auth()->user()->company_key;


        $user_exists = User::belongsToRole('user')->where('mobile', $request->mobile)->exists();


        $country_id = auth()->user()->country;
 
 
         if(!$user_exists)
         {
            $userDetail =  User::create([
                 'name'=>$request->name,
                 'mobile'=>$request->mobile,
                 'country'=>$country_id,
             ]);
         }else{
            $userDetail = User::belongsToRole('user')->where('mobile',  $request->mobile)->first();
         }

         $request_params['user_id'] = $userDetail->id;

         if(auth()->user()->driver->owner_id!=null)
         {
 
             $request_params['owner_id'] = auth()->user()->driver->owner_id;
 
             $request_params['fleet_id'] = auth()->user()->driver->fleet_id;
         }

        $request_detail = $this->request->create($request_params);
        // Log::info("Instant Ride Created");  
        // Log::info($request_detail);        




        auth()->user()->driver->update(['available'=>false]);



        // $ad_hoc_user_params['name'] = $request->name;
        // $ad_hoc_user_params['mobile'] = $request->mobile;

        // // Store ad hoc user detail of this request
        // $request_detail->adHocuserDetail()->create($ad_hoc_user_params);

        // request place detail params
        $request_place_params = [
            'pick_lat'=>$request->pick_lat,
            'pick_lng'=>$request->pick_lng,
            'drop_lat'=>$request->drop_lat,
            'drop_lng'=>$request->drop_lng,
            'pick_address'=>$request->pick_address,
            'drop_address'=>$request->drop_address];

        // store request place details
        $request_detail->requestPlace()->create($request_place_params);
        
        $request_result =  fractal($request_detail->fresh(), new TripRequestTransformer)->parseIncludes('userDetail');

        return $this->respondSuccess($request_result, 'created_instant_ride_successfully');
    }


     /**
    * Create Delivery Instant Ride
    * @bodyParam pick_lat double required pikup lat of the user
    * @bodyParam pick_lng double required pikup lng of the user
    * @bodyParam drop_lat double required drop lat of the user
    * @bodyParam drop_lng double required drop lng of the user
    * @bodyParam pick_address string required pickup address of the trip request
    * @bodyParam drop_address string required drop address of the trip request
    * @bodyParam name string required customer name for the request
    * @bodyParam mobile string required customer name for the request
    * @bodyParam request_eta_amount double required Estimated cost of the ride
    * @bodyParam goods_type_id integer reuired goods type selected by user
    * @bodyParam goods_type_quantity string quantity of goods type
    * 
    * @responseFile responses/requests/instant-ride.json
    *
    */

    public function createDeliveryRequest(CreateTripRequest $request)
    {

        // Log::info("cretae delivery instant ride");
        $zone_detail = find_zone($request->input('pick_lat'), $request->input('pick_lng'));
        $unit = $zone_detail->unit;
        
        // Get last request's request_number
        // $request_number = $this->request->orderBy('created_at', 'DESC')->pluck('request_number')->first();
        // if ($request_number) {
        //     $request_number = explode('_', $request_number);
        //     $request_number = $request_number[1]?:000000;
        // } else {
        //     $request_number = 000000;
        // }
        // // Generate request number
        // $request_number = 'REQ_'.sprintf("%06d", $request_number+1);

        $current_timestamp = Carbon::now()->timestamp.rand(0, 99);

        $request_number = 'REQ_'.$current_timestamp;

        $service_location = $zone_detail->serviceLocation;

        $currency_code = $service_location->currency_code;
        
        $currency_symbol = $service_location->currency_symbol;

        $type_id = auth()->user()->driver->vehicle_type;
       
        if($type_id==null){

            $type_id = auth()->user()->driver->driverVehicleTypeDetail()->pluck('vehicle_type')->first();
        }     
        $zone_type_id = $zone_detail->zoneType()->where('type_id',$type_id)->pluck('id')->first();

        if(!$zone_type_id){
            $this->throwCustomException('Your Vehicle Type is not associated with this zone');
        }


        $zone_type_detail = $zone_detail->zoneType()->where('type_id',$type_id)->first();

        $eta_result = fractal($zone_type_detail, new EtaTransformer);

            
        $eta_result =json_decode($eta_result->toJson());

        $request_params = [
            'request_number'=>$request_number,
            'driver_id'=>auth()->user()->driver->id,
            'zone_type_id'=>$zone_type_id,
            'payment_opt'=>'1',
            'unit'=>$unit,
            'requested_currency_code'=>$currency_code,
            'requested_currency_symbol'=>$currency_symbol,
            'service_location_id'=>$service_location->id,
            'accepted_at'=>date('Y-m-d H:i:s'),
            'is_driver_started'=>true,
            'is_driver_arrived'=>true,
            'arrived_at'=>date('Y-m-d H:i:s'),
            'is_trip_start'=>true,
            'trip_start_time'=>date('Y-m-d H:i:s'),
            'instant_ride'=>true,
            'on_search'=>false,
            'goods_type_id'=>$request->goods_type_id,
            'goods_type_quantity'=>$request->goods_type_quantity,
            'total_time'=>$eta_result->data->time,
            'total_distance'=>$eta_result->data->distance,
        ];


        $app_for = config('app.app_for');

        if($app_for!='taxi' || $app_for!='delivery')
         {
            $request_params['transport_type']='delivery';
            
         }

        if($request->has('request_eta_amount') && $request->request_eta_amount){

           $request_params['request_eta_amount'] = $request->request_eta_amount;

        }

        $request_params['company_key'] = auth()->user()->company_key;


        auth()->user()->driver->update(['available'=>false]);

        $user_exists = User::belongsToRole('user')->where('mobile', $request->mobile)->exists();


        $country_id = auth()->user()->country;
 
 
         if(!$user_exists)
         {
            $userDetail =  User::create([
                 'name'=>$request->name,
                 'mobile'=>$request->mobile,
                 'country'=>$country_id,
             ]);
         }else{
            $userDetail = User::belongsToRole('user')->where('mobile',  $request->mobile)->first();
         }

         $request_params['user_id'] = $userDetail->id;


         if(auth()->user()->driver->owner_id!=null)
         {
             // Log::info("--------Accepted_Fleet_driver--------");
 
             // Log::info(auth()->user()->driver);
             $request_params['owner_id'] = auth()->user()->driver->owner_id;
 
             $request_params['fleet_id'] = auth()->user()->driver->fleet_id;
         }


         $request_detail = $this->request->create($request_params);
         // Log::info("Instant Ride Created");  
         // Log::info($request_detail);        
         
        // request place detail params
        $request_place_params = [
            'pick_lat'=>$request->pick_lat,
            'pick_lng'=>$request->pick_lng,
            'drop_lat'=>$request->drop_lat,
            'drop_lng'=>$request->drop_lng,
            'pick_address'=>$request->pick_address,
            'drop_address'=>$request->drop_address];

        // store request place details
        $request_detail->requestPlace()->create($request_place_params);
        
        $request_result =  fractal($request_detail->fresh(), new TripRequestTransformer)->parseIncludes('userDetail');

        return $this->respondSuccess($request_result, 'created_instant_delivery_ride_successfully');
    }


}
