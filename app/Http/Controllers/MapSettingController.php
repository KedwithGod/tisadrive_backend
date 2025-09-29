<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\ThirdPartySetting;
use Illuminate\Http\Request;
use App\Models\Admin\VehicleType;
use App\Models\Admin\ServiceLocation;
use App\Models\Request\Request as RequestModel;
use App\Models\Request\RequestPlace;
use Carbon\Carbon;

class MapSettingController extends Controller
{
    public function index() 
    {
        $settings = ThirdPartySetting::where('module', 'map')->pluck('value', 'name')->toArray();

        //   $map_type = get_map_settings('map_type');
    // dd($map_key);


        return Inertia::render('pages/map_settings/index', [
            'app_for'=>env('APP_FOR'),
            'settings' => $settings,
        ]);

    }
   
    public function update(Request $request) 
    {

        $settings = $request->only([
            'map_type',
            // 'enable_vase_map',
            // 'google_map_key_for_distance_matrix',
            // 'google_sheet_id',
            'google_map_key',]);
        
        foreach ($settings as $key => $setting) 
        {
            ThirdPartySetting::where('name' , $key )->where('module', 'map')->update(['value' => $setting]);                 
        }
  
    
        return response()->json(['message' => 'Map  Details updated successfully'], 201);
    }    
    public function heatmap(Request $request) 
    {

        $map_key = get_map_settings('google_map_key');

        // dd($map_key);

        // Calculate the date one week ago
        $oneWeekAgo = Carbon::now()->subWeek();

            $requestData = RequestPlace::whereBetween('created_at', [$oneWeekAgo, Carbon::now()])
                ->get();

                // dd($requestData);
        $map_type = get_map_settings('map_type');

        if($map_type=="open_street_map")
        {
        return Inertia::render('pages/map/openheatmap',[
        'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
        'requestData'=>$requestData, 'map_key'=>$map_key]);
        }else{
            return Inertia::render('pages/map/heatmap',[
                'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
                'requestData'=>$requestData, 'map_key'=>$map_key]);    
        }
    }

    public function godseye() 
    {

        $service_location = ServiceLocation::where('active', true)->get(['id', 'name']);
        $vehicle_type = VehicleType::where('active', true)->get(['id', 'name']);

        $map_key = get_map_settings('google_map_key');
        
        // dd($vehicle_type);


        $firebaseSettings = [
            'firebase_api_key' => get_firebase_settings('firebase_api_key'),
            'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
            'firebase_database_url' => get_firebase_settings('firebase_database_url'),
            'firebase_project_id' => get_firebase_settings('firebase_project_id'),
            'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
            'firebase_app_id' => get_firebase_settings('firebase_app_id'),
        ];

          $map_type = get_map_settings('map_type');
       
          if($map_type=="open_street_map")
          {
            return Inertia::render('pages/map/godseye-open',['firebaseSettings'=>$firebaseSettings,
            'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
            'service_location'=>$service_location,'vehicle_type'=>$vehicle_type]);    
          }else{
            
            $default_location = (object)[
                "lat"=> (float) get_settings('default_latitude'),
                "lng"=> (float) get_settings('default_longitude'),
            ];
            return Inertia::render('pages/map/godseye',[
                'firebaseSettings'=>$firebaseSettings,
                'baseUrl'=>route('landing.index'),'default_location'=>$default_location,
                'service_location'=>$service_location,'vehicle_type'=>$vehicle_type,'map_key'=>$map_key
            ]);
          }


    }


}
