<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use App\Models\User;
use App\Models\Admin\Driver;
use App\Models\Request\Request as RequestModel;
use App\Models\Request\RequestBill;
use Carbon\Carbon;
use App\Models\Admin\GoodsType;
use App\Models\Admin\Zone;
use App\Models\Admin\VehicleType;
use App\Models\ThirdPartySetting;
use App\Base\Filters\Admin\RequestFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Transformers\Requests\TripRequestTransformer;
use App\Models\Admin\ServiceLocation;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Admin\Setting;
use App\Models\Admin\InvoiceConfiguration;
use Illuminate\Support\Facades\Mail;
use App\Mail\RideLaterMail;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Admin\PackageType;
use App\Models\Admin\ZoneTypePackagePrice;
use App\Jobs\Mails\SendUserRideLaterMailNotification;
use App\Http\Controllers\Api\V1\Payment\Stripe\StripeController;

class DispatcherController extends StripeController
{

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    function index() {

// dd($cancelledtrips);
        $currency_code = get_settings('currency_code');
        $currency_symbol = get_settings('currency_symbol');


        $firebaseConfig = (object) [
            'apiKey' => get_firebase_settings('firebase_api_key'),
            'authDomain' => get_firebase_settings('firebase_auth_domain'),
            'databaseURL' => get_firebase_settings('firebase_database_url'),
            'projectId' => get_firebase_settings('firebase_project_id'),
            'storageBucket' => get_firebase_settings('firebase_storage_bucket'),
            'messagingSenderId' => get_firebase_settings('firebase_messaging_sender_id'),
            'appId' => get_firebase_settings('firebase_app_id'),
        ];
        return Inertia::render('dispatch-new/index', [
            'currency_code' => $currency_code,
            'currencySymbol' => $currency_symbol,
            'firebaseConfig' => $firebaseConfig,

        ]);
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
            return Inertia::render('dispatch-new/godseye-open',['firebaseSettings'=>$firebaseSettings,
            'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
            'service_location'=>$service_location,'vehicle_type'=>$vehicle_type]);    
          }else{
            return Inertia::render('dispatch-new/godseye',['firebaseSettings'=>$firebaseSettings,
            'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
            'service_location'=>$service_location,'vehicle_type'=>$vehicle_type,'map_key'=>$map_key]);
          }


    }

    public function bookride() {

        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();

        $firebaseSettings = [
            'firebase_api_key' => get_firebase_settings('firebase_api_key'),
            'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
            'firebase_database_url' => get_firebase_settings('firebase_database_url'),
            'firebase_project_id' => get_firebase_settings('firebase_project_id'),
            'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
            'firebase_app_id' => get_firebase_settings('firebase_app_id'),
        ];

        // dd($map_key);

        $default_dial_code = $default_country->dial_code;
        $default_flag = $default_country->flag;

        // @TODO check if the rental is enabled or not
        $ride_type_for_ride = ['regular','rental'];

        $goods_types = GoodsType::active()->get();

        $transport_settings = Setting::where('category', 'trip_settings')
        ->pluck('value', 'name')
        ->toArray();

        $schedule_a_ride = (double) $transport_settings['user_can_make_a_ride_after_x_miniutes'];

        $settings = Setting::where('category', 'customization_settings')
        ->pluck('value', 'name')
        ->toArray(); 

        $enabled_modules = $settings['enable_modules_for_applications'] ?? 'taxi';       
        
        $transport_type_regular = [];
        
        if ($enabled_modules === 'both') {
            $transport_type_regular = ['taxi', 'delivery'];
        } else {
            $transport_type_regular = [$enabled_modules];
        }


        $package_taxi = ZoneTypePackagePrice::active()->whereHas('zoneType',function($query) {
            $query->where('transport_type','taxi')->orWhere('transport_type','both');
        })->exists();

        $package_delivery = ZoneTypePackagePrice::active()->whereHas('zoneType',function($query) {
            $query->where('transport_type','delivery')->orWhere('transport_type','both');
        })->exists();

        $rental_taxi = $settings['show_taxi_rental_ride_feature'];
        $rental_delivery = $settings['show_delivery_rental_ride_feature'];

        // Initialize the transport type array
        $transport_type_rental = [];
        if ($rental_taxi == 1 && $package_taxi) {
            $transport_type_rental[] = 'taxi';
        }

        if ($rental_delivery == 1 && $package_delivery) {
            $transport_type_rental[] = 'delivery';
        }

        

        $outstation_taxi = $settings['show_outstation_ride_feature'];
        $outstation_delivery = $settings['show_delivery_outstation_ride_feature'];

        $transport_type_outstation = [];

        
        if ($outstation_taxi == 1) {              
            $transport_type_outstation[] = 'taxi';
        }

        
        if ( $outstation_delivery == 1) {
            $transport_type_outstation[] = 'delivery';
        }

        // preference
        $pet_preference = $settings['enable_pet_preference_for_user'] == '1';
        $luggage_preference = $settings['enable_luggage_preference_for_user'] == '1';
        // $map_type = get_map_settings('map_type');
        $map_type = get_map_settings('map_type');
        $enable_ride_without_destination = get_settings('show_ride_without_destination') == '1';

        if($map_type=="open_street_map")
        {

            
            return Inertia::render('dispatch-new/open-dispatch',['countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,'default_flag'=>$default_flag,
            'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
            'firebaseSettings'=>$firebaseSettings,'enable_ride_without_destination'=>$enable_ride_without_destination,
            'ride_type_for_ride'=>$ride_type_for_ride,'goodsTypes'=>$goods_types,
            'transport_type_outstation' => $transport_type_outstation,'schedule_a_ride'=>$schedule_a_ride,
            'transport_type_rental' =>$transport_type_rental, 'transport_type_regular' =>$transport_type_regular,
            'is_pet_available' => $pet_preference, 'is_luggage_available' => $luggage_preference
            ]);   

        }else{
            $map_key = get_map_settings('google_map_key');

            $default_location = (object)[
                "lat"=> (float) get_settings('default_latitude'),
                "lng"=> (float) get_settings('default_longitude'),
            ];

            return Inertia::render('dispatch-new/dispatch',['countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,'default_flag'=>$default_flag,
            'baseUrl'=>route('landing.index'),'default_location'=>$default_location,
            'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
            'firebaseSettings'=>$firebaseSettings,'enable_ride_without_destination'=>$enable_ride_without_destination,
            'ride_type_for_ride'=>$ride_type_for_ride,'goodsTypes'=>$goods_types,'map_key'=>$map_key,
            'transport_type_outstation' => $transport_type_outstation,'schedule_a_ride'=>$schedule_a_ride,
            'transport_type_rental' =>$transport_type_rental, 'transport_type_regular' =>$transport_type_regular,
            'is_pet_available' => $pet_preference, 'is_luggage_available' => $luggage_preference
            ]);

    
         }
    }

    /**
     * Fetch User Detail
     * 
     * 
     * */
    public function fetchUserIfExists()
    {
        $mobile = request()->mobile;

        //belongsTorole('user')->
        $user = User::where('mobile',$mobile)->first();
        
        return $this->respondSuccess($user);


    }

    //ride request

    public function rideRequest() {
        $ongoing = RequestModel::with('userDetail','driverDetail')
            ->where('is_cancelled', false)
            ->where('is_completed', false)
            ->orderBy('created_at','DESC')->pluck('id');
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();

        $firebaseConfig = (object) [
            'apiKey' => $settings['firebase_api_key'],
            'authDomain' => $settings['firebase_auth_domain'],
            'databaseURL' => $settings['firebase_database_url'],
            'projectId' => $settings['firebase_project_id'],
            'storageBucket' => $settings['firebase_storage_bucket'],
            'messagingSenderId' => $settings['firebase_messaging_sender_id'],
            'appId' => $settings['firebase_app_id'],
        ];
        return Inertia::render('dispatch-new/rides_request/index',[
            'zones' => Zone::active()->get(),
            'types' => VehicleType::active()->get(),
            'ongoing_rides' => $ongoing,
            'firebaseConfig' => $firebaseConfig,
            'enable_outstation' => get_settings('show_outstation_ride_feature') || get_settings('show_delivery_outstation_ride_feature'),
        ]);
    }

    //trip request list

    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = RequestModel::where('requests.transport_type', 'taxi')
            ->with('userDetail', 'driverDetail')
            ->orderBy('created_at', 'DESC');
    
        if (auth()->user()->hasRole('owner')) {
            // Retrieve the specific owner associated with the authenticated user
            $owner = auth()->user()->owner;
            $query->where('owner_id', $owner->id)->orWhere('booked_by', auth()->user()->id);
        }
    
        $results = $queryFilter->builder($query)
            ->customFilter(new RequestFilter)
            ->paginate();
    
        $data = [
            'results' => $results->items(),
            'paginator' => $results,
        ];
        
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        
        return response($json, 200)
            ->header('Content-Type', 'application/json');
    }

    //view details

    public function viewDetails(RequestModel $requestmodel) {
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();
        $onsearch = $requestmodel->on_search;
        $requestmodel = fractal($requestmodel, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail','requestBill','rejectedDrivers'])->toArray();
        $requestmodel['data']['onSearch'] = $onsearch;
        $firebaseConfig = (object) [
            'apiKey' => $settings['firebase_api_key'],
            'authDomain' => $settings['firebase_auth_domain'],
            'databaseURL' => $settings['firebase_database_url'],
            'projectId' => $settings['firebase_project_id'],
            'storageBucket' => $settings['firebase_storage_bucket'],
            'messagingSenderId' => $settings['firebase_messaging_sender_id'],
            'appId' => $settings['firebase_app_id'],
        ];
        if(get_map_settings('map_type') == "open_street_map"){
            return Inertia::render('dispatch-new/rides_request/open-view',
                [
                    'request' => $requestmodel['data'],
                    'service_location'=>null,
                    'app_for' => env('APP_FOR'),
                    'pick_icon'=>asset('image/map/pickup.png'),
                    'drop_icon'=>asset('image/map/drop.png'),
                    'firebaseConfig'=>$firebaseConfig,
                ]);
        }
        $googleMapKey = get_map_settings('google_map_key'); // Retrieve the Google Map API key
        return Inertia::render('dispatch-new/rides_request/view',
            [
                'request' => $requestmodel['data'],
                'service_location'=>null,
                'baseUrl'=>route('landing.index'),
                'app_for' => env('APP_FOR'),
                'googleMapKey'=>$googleMapKey,
                'firebaseConfig'=>$firebaseConfig,
            ]
        );
    }

    public function driverFind(Driver $driver,Request $request)
    {
        $request = RequestModel::find($request->request_id);
        return response()->json([
            'successMessage' => 'Driver Found successfully',
            'driver' => $driver,
            'current_time' => get_converted_time(now(),$request->timezone),
        ]);
    }
    public function cancelRide(RequestModel $requestmodel) {
        $update_parms['is_cancelled'] = true;
        $update_parms['cancelled_at'] = date('Y-m-d H:i:s');
        $update_parms['cancel_method'] = 0;
        if($requestmodel->driver_id){
            Driver::where('id',$requestmodel->driver_id)->update(['available'=>true]);
        }
        if($requestmodel->driverDetail)
        {
            $notifiable_driver = $requestmodel->driverDetail->user;

             $notification = \DB::table('notification_channels')
                ->where('topics', 'Trip Cancelled By System') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $notifiable_driver->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
                    dispatch(new SendPushNotification($notifiable_driver, $title, $body));
                }
        }
        if($requestmodel->payment_intent_id){
            $this->cancel($requestmodel->payment_intent_id);
        }
        $this->database->getReference('requests/' . $requestmodel->id)->update(['is_cancelled' => true, 'cancelled_by_user' => true]);
        $this->database->getReference('requests/' . $requestmodel->id)->remove();
        $this->database->getReference('SOS/' . $requestmodel->id)->remove();
        $this->database->getReference('request-meta/' . $requestmodel->id)->remove();
        $requestmodel->update($update_parms);
        $requestmodel->requestMeta()->delete();

        return response()->json([
            'successMessage' => 'Trip Cancelled successfully',
            'request' => $requestmodel,
        ]);
    }
    public function sosDetail(RequestModel $request)
    {
        if($request->is_cancelled || $request->is_completed) {
            return response()->json(['message'=>'Invalid SOS'],422);
        }
        $result = json_decode(fractal($request, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail'])->toJson());
        return response()->json([
            'successMessage' => 'Ride Found successfully',
            'request' => $result->data,
            'current_time' => get_converted_time(now(),$request->timezone),
        ]);
    }

    //ongoing Request

    public function ongoingRequest()
    {
        $ongoing = RequestModel::with('userDetail','driverDetail')
            ->where('is_cancelled', false)
            ->where('is_completed', false)
            ->orderBy('created_at','DESC')->get();
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();

        $firebaseConfig = (object) [
            'apiKey' => $settings['firebase_api_key'],
            'authDomain' => $settings['firebase_auth_domain'],
            'databaseURL' => $settings['firebase_database_url'],
            'projectId' => $settings['firebase_project_id'],
            'storageBucket' => $settings['firebase_storage_bucket'],
            'messagingSenderId' => $settings['firebase_messaging_sender_id'],
            'appId' => $settings['firebase_app_id'],
        ];
        return Inertia::render('dispatch-new/ongoing_rides/index',[
            'zones' => Zone::active()->get(),
            'types' => VehicleType::active()->get(),
            'ongoing_rides' => $ongoing,
            'firebaseConfig' => $firebaseConfig,
        ]);
    }

    public function ongoingRideDetail(RequestModel $request)
    {
        $items = fractal($request, new TripRequestTransformer)->toArray();
        return response()->json([
            'result' => $items['data'],
            'current_time' => get_converted_time(now(),$request->timezone),
        ]);
    }
    public function assignView(RequestModel $request)
    {
        if($request->is_cancelled || $request->driver_id){
            return redirect('rides-request/view/'.$request->id);
        }
        $firebaseSettings = [
            'firebase_api_key' => get_firebase_settings('firebase_api_key'),
            'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
            'firebase_database_url' => get_firebase_settings('firebase_database_url'),
            'firebase_project_id' => get_firebase_settings('firebase_project_id'),
            'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
            'firebase_app_id' => get_firebase_settings('firebase_app_id'),
        ];
        $item = fractal($request, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail'])->toJson();
        $request = json_decode($item);
        if(get_map_settings('map_type') == 'open_street_map'){
            return Inertia::render('dispatch-new/ongoing_rides/assign-open',[
                'result' => $request->data,
                'app_for' => env('APP_FOR'),
                'firebaseSettings' => $firebaseSettings,
            ]);
        }
        $map_key = get_map_settings('google_map_key');
        return Inertia::render('dispatch-new/ongoing_rides/assign',[
            'map_key' => $map_key,
            'app_for' => env('APP_FOR'),
            'result' => $request->data,
            'firebaseSettings' => $firebaseSettings,
        ]);
    }
    public function assignDriver(RequestModel $requestmodel,Request $request) {
        $assigned = $requestmodel->is_cancelled || $requestmodel->is_completed || $requestmodel->driver_id || $requestmodel->requestMeta()->exists();
        if($assigned) {
            return response()->json(['status'=>false,'message'=>'Cannot Assign Request']);
        }
        $request->validate([
            'driver_id'  => 'required' 
        ]);
        $driver = Driver::find($request->driver_id);
        
        if(!$driver) {
            return response()->json(['status'=>false,'message'=>'Cannot Assign Driver']);
        }
        $selected_drivers["user_id"] = $requestmodel->user_id;
        $selected_drivers["driver_id"] = $driver->id;
        $selected_drivers["active"] = 1;
        $selected_drivers["assign_method"] = 1;
        $selected_drivers["created_at"] = date('Y-m-d H:i:s');
        $selected_drivers["updated_at"] = date('Y-m-d H:i:s');

        $requestmodel->requestMeta()->create($selected_drivers);
        $this->database->getReference('request-meta/'.$requestmodel->id)
                    ->set([
                            'driver_id'=>$driver->id,
                            'request_id'=>$requestmodel->id,
                            'user_id'=>$requestmodel->user_id,
                            'active'=>1,
                            'transport_type'=>"taxi",
                            'updated_at'=> Database::SERVER_TIMESTAMP
                        ]);
        $requestmodel->update(['assign_method'=>1, 'accepted_ride_fare'=>$requestmodel->offerred_ride_fare,'is_bid_ride'=>false]);


        $notifable_driver = $driver->user;

        // $title = custom_trans('new_request_title',[],$notifable_driver->lang);
        // $body = custom_trans('new_request_body',[],$notifable_driver->lang);
        // $push_data = ['title' => $title,'message' => $body,'push_type'=>'meta-request'];

        // dispatch(new SendPushNotification($notifable_driver,$title,$body,$push_data));

        $user = $requestmodel->userDetail;
        if ($requestmodel->is_later) {
            dispatch(new SendUserRideLaterMailNotification($user));
        }

        $notification = \DB::table('notification_channels')
            ->where('topics', 'User Ride Later') // Match the correct topic
            ->first();

        //   send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $notifable_driver->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }
            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
        $push_data = ['title' => $title,'message' => $body,'push_type'=>'meta-request'];
                    dispatch(new SendPushNotification($notifable_driver,$title,$body,$push_data));
                }
        return response()->json(['status'=>true,'message'=>'Assigned Successfully']);
    }

    //download invoice

    public function downloadInvoice(RequestModel $requestmodel, Request $request)
    {
        $requestmodel = fractal($requestmodel, new TripRequestTransformer)->parseIncludes(['userDetail', 'driverDetail', 'requestBill', 'rejectedDrivers'])->toArray();
        $logo = Setting::where('name', 'logo')->first();
        $invoice_configuration = ThirdPartySetting::where('module', 'mail_config')->pluck('value', 'name')->toArray();
        try {
            if ($request->invoice_type === "user") {
                $data = $requestmodel;
                $img = $logo;
                $invoice = $invoice_configuration;
                // dd($invoice);
                // Format completed_at for the view
                $data['formatted_completed_at'] = isset($requestmodel['completed_at']) 
                ? Carbon::parse($requestmodel['completed_at'])
                    ->setTimezone(env('SYSTEM_DEFAULT_TIMEZONE', 'Asia/Kolkata'))
                    ->format('M j, Y - h:i A') 
                : null;

                // Return user invoice Blade view
                return view('emails.invoice', compact('data','logo','invoice'));
            } elseif ($request->invoice_type === "driver") {
                $data = $requestmodel;
                $img = $logo;
                $invoice = $invoice_configuration;

                // Return driver invoice Blade view
                return view('emails.driver_invoice', compact('data','logo','invoice'));
            }

            // Handle invalid invoice type
            return response()->json(['error' => 'Invalid invoice type'], 400);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => 'Failed to generate invoice: ' . $e->getMessage()], 500);
        }
    }

    public function advanced()
    {
            

        $service_location = ServiceLocation::where('active', true)->get(['id', 'name']);
        $vehicle_type = VehicleType::where('active', true)->get(['id', 'name']);

        $map_key = get_map_settings('google_map_key');
        

        $firebaseSettings = [
            'firebase_api_key' => get_firebase_settings('firebase_api_key'),
            'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
            'firebase_database_url' => get_firebase_settings('firebase_database_url'),
            'firebase_project_id' => get_firebase_settings('firebase_project_id'),
            'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
            'firebase_app_id' => get_firebase_settings('firebase_app_id'),
        ];

        $map_key = get_map_settings('google_map_key');

        $default_location = (object)[
            "lat"=> (float) get_settings('default_latitude'),
            "lng"=> (float) get_settings('default_longitude'),
        ];

        return Inertia::render('pages/landing/user-web/advanced-booking',
        [
            'firebaseSettings'=>$firebaseSettings,
            'baseUrl'=>route('landing.index'),'default_location'=>$default_location,
            'service_location'=>$service_location,'vehicle_type'=>$vehicle_type,'map_key'=>$map_key
        ]);
    }
}
