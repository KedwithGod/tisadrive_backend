<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Admin\VehicleType;
use Illuminate\Support\Facades\Log;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\VehicleTypeFilter;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin\SubVehicleType;
use App\Models\Setting;


class VehicleTypeController extends BaseController
{
    /**
     * The VehicleType model instance.
     *
     * @var \App\Models\Admin\VehicleType
     */
    protected $vehicle_type;

    protected $imageUploader;


    /**
     * VehicleTypeController constructor.
     *
     * @param \App\Models\Admin\VehicleType $vehicle_type
     */

    public function __construct(VehicleType $vehicle_type, ImageUploaderContract $imageUploader)
    {
        $this->vehicle_type = $vehicle_type;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        return Inertia::render('pages/vehicle_type/index');
    }


    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = VehicleType::with('vehicleTypeTranslationWords')->orderBy('created_at','DESC');
        // dd("ssss");
        $results = $queryFilter->builder($query)->customFilter(new VehicleTypeFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {
        $settings = Setting::where('category', 'customization_settings')
        ->pluck('value', 'name')
        ->toArray(); 

        $enabled_sub_vehicle_modules = $settings['enable_sub_vehicle_feature'];

        return Inertia::render('pages/vehicle_type/create',['serviceLocation' => null,'enabled_sub_vehicle_modules' => $enabled_sub_vehicle_modules]);
    }
    public function store(Request $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // dd($request->all());
         // Validate the incoming request
         $validated = $request->validate([
            'name' => 'required|unique:vehicle_types',
            'icon_types_for' => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'icon' => 'required',
        ]);

        $created_params = $request->only(['name','description','short_description','icon_types_for', 'trip_dispatch_type']);


        if ($uploadedFile = $request->file('icon')) {
            $created_params['icon'] = $this->imageUploader->file($uploadedFile)
                ->saveVehicleTypeImage();
        }
        $created_params['active'] = true;
        $created_params['is_taxi'] = $request->transport_type;

        // Map the form inputs to the database columns
        $created_params['capacity'] = $request->capacity;
        $created_params['size'] = $request->size;
        

        
        // dd($validated);

        if ($request->has('supported_vehicles')) {
            $created_params['supported_vehicles'] = implode(',', $request->supported_vehicles); // Convert array to string
        }
        // Create a sub vehicle type
        $vehicleType = VehicleType::create($created_params);
        if ($request->has('supported_vehicles')) {  /// [vt1,vt2]         
            
            $sub_vehicle_type_ids = $request->supported_vehicles; 
            $vehicle_type_id = $vehicleType->id ;
            
            foreach($sub_vehicle_type_ids as $sub_vehicle_type_id) {
                $vehicleType->subVehicleTypeDetail()->create([
                    'vehicle_type_id' => $vehicle_type_id,
                    'sub_vehicle_type_id' => $sub_vehicle_type_id,
                ]);
            }
        }

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Vehicle Type created successfully.',
            'vehicleType' => $vehicleType,
        ], 201);
    }
    public function edit($id)
    {

        $vehicleType = VehicleType::find($id);
        $vehicleType->sub_vehicle = $vehicleType->subVehicleTypeDetail()->pluck("sub_vehicle_type_id");
        $settings = Setting::where('category', 'customization_settings')
        ->pluck('value', 'name')
        ->toArray(); 

        $enabled_sub_vehicle_modules = $settings['enable_sub_vehicle_feature'];  
        // dd($vehicleType->sub_vehicle);
        return Inertia::render(
            'pages/vehicle_type/create',
            ['vehicleType' => $vehicleType,
            'enabled_sub_vehicle_modules' => $enabled_sub_vehicle_modules]
        );
    }
    public function update(Request $request, VehicleType $vehicle_type)
    {
// dd($request);
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }

         // Validate the incoming request
         $validated = $request->validate([
            'name' => 'required|unique:vehicle_types,name,'. $vehicle_type->id, 
            'icon_types_for' => 'required',
            'description' => 'required',
            'short_description' => 'required',
        ]);

        $updated_params = $request->only(['name','description','short_description','transport_type','icon_types_for','trip_dispatch_type']);
        // $updated_params['name'] = $validated['languageFields']['en'];

        if($request->hasFile('icon')){
                if ($vehicle_type->icon) {
                    Storage::delete('public/' . $vehicle_type->icon);
                }
                if ($uploadedFile = $request->file('icon')) {
                    $updated_params['icon'] = $this->imageUploader->file($uploadedFile)
                        ->saveVehicleTypeImage();
                    }
            }
        $updated_params['active'] = true;

        // Map the form inputs to the database columns
        $updated_params['capacity'] = $request->capacity;
        $updated_params['is_taxi'] = $request->transport_type;

        $updated_params['size'] = $request->size;
        

        if ($request->has('supported_vehicles')) {
            $created_params['supported_vehicles'] = implode(',', $request->supported_vehicles); // Convert array to string
        }


        $vehicle_type->update($updated_params);
        //  Create a sub vehicle type
         if ($request->has('supported_vehicles')) {  /// [vt1,vt2]         
            $vehicle_type->subVehicleTypeDetail()->delete();
             $sub_vehicle_type_ids = $request->supported_vehicles; 
             $vehicle_type_id = $vehicle_type->id ;
             
             foreach($sub_vehicle_type_ids as $sub_vehicle_type_id) {
                 $vehicle_type->subVehicleTypeDetail()->create([
                     'vehicle_type_id' => $vehicle_type_id,
                     'sub_vehicle_type_id' => $sub_vehicle_type_id,
                 ]);
             }
         }

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Vehicle Type updated successfully.',
            'vehicle_type' => $vehicle_type,
        ], 201);

    }
    public function destroy(VehicleType $vehicle_type)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        if($vehicle_type->zoneType()->exists()){
            $vehicle_type->zoneType()->delete();
        }
        $vehicle_type->delete();

        return response()->json([
            'successMessage' => 'Vehicle Type deleted successfully',
        ]);
    }   
    public function updateStatus(Request $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // dd($request->all());
        VehicleType::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Vehicle Type status updated successfully',
        ]);


    }

}
