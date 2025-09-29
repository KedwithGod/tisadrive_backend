<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use App\Models\User;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\V1\BaseController;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Payment\UserWalletHistory;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;
use Illuminate\Support\Facades\Storage;
use App\Base\Filters\Master\CommonMasterFilter;
use Carbon\Carbon;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Models\Request\Request as RequestModel;
use App\Models\Admin\Driver;
use App\Models\ThirdPartySetting;
use App\Transformers\Requests\TripRequestTransformer;
use App\Models\Admin\GoodsType;
use Illuminate\Support\Facades\Validator; 
use App\Models\Admin\ZoneType;
use App\Helpers\Rides\StoreEtaDetailForRideHelper;
use App\Helpers\Rides\FetchDriversFromFirebaseHelpers;
use App\Transformers\User\EtaTransformer;
use Kreait\Firebase\Contract\Database;
use App\Base\Constants\Masters\PushEnums;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\RideLaterMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Models\Support\SupportTicketTitle;
use App\Models\Support\SupportTicket;
use App\Base\Filters\Support\TicketFilter;
use App\Models\Support\SupportTicketMultiFile;
use App\Models\Support\SupportTicketMessage;
use App\Models\Admin\ServiceLocation;


class UserWebBookingController extends Controller
{
    use FetchDriversFromFirebaseHelpers,StoreEtaDetailForRideHelper;


    protected $imageUploader;
   
    protected $request;

    protected $database;

    protected $user;

    public function __construct(ImageUploaderContract $imageUploader, RequestModel $request,User $user,Database $database, SupportTicketMultiFile $multifile)
    {
        $this->imageUploader = $imageUploader;
        $this->request = $request;
        $this->user = $user;  
        $this->database = $database;
        $this->multifile = $multifile;
      
    }

    public function booking()
    {
        $user = Auth::user();

        $ride_type_for_ride = ['regular','rental'];
       
        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();

        $goods_types = GoodsType::active()->get();

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

        // dd($map_key);

        $default_dial_code = $default_country->dial_code;
       
        $default_flag = $default_country->flag;


        $app_for = config('app.app_for');

        if($app_for=='taxi' || $app_for=='delivery')
        {
            $transport_type_for_ride[] = $app_for;
        }else{

            $transport_type_for_ride = ['taxi','delivery'];

        }

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
        
        if(get_map_settings('map_type') == 'open_street_map') {
            return Inertia::render('pages/landing/user-web/open-booking',
                [
                    'countries'=>$result['data'],
                    'user'=>$user,
                    'default_dial_code'=>$default_dial_code,'default_flag'=>$default_flag,
                    'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
                    'transport_type_for_ride'=>$transport_type_for_ride,'firebaseSettings'=>$firebaseSettings,
                    'ride_type_for_ride'=>$ride_type_for_ride,'goodsTypes'=>$goods_types, 'transport_type_regular' =>$transport_type_regular,
                ]);
        } else {

            $default_location = (object)[
                "lat"=> (float) get_settings('default_latitude'),
                "lng"=> (float) get_settings('default_longitude'),
            ];
            return Inertia::render('pages/landing/user-web/booking',
                [
                    'countries'=>$result['data'],
                    'baseUrl'=>route('landing.index'),'default_location'=>$default_location,
                    'user'=>$user,'map_key'=>$map_key,
                    'default_lat'=>get_settings('default_latitude'),'default_lng'=>get_settings('default_longitude'),
                    'default_dial_code'=>$default_dial_code,'default_flag'=>$default_flag,
                    'transport_type_for_ride'=>$transport_type_for_ride,'firebaseSettings'=>$firebaseSettings,
                    'ride_type_for_ride'=>$ride_type_for_ride,'goodsTypes'=>$goods_types, 'transport_type_regular' =>$transport_type_regular,
                ]);
        }
    }

    public function profile() 
    {

        $user = Auth::user(); // Assuming user is authenticated

        return Inertia::render('pages/landing/user-web/profile', [
            'user' => $user
        ]);
    }
        // Store or Update user profile
        public function updateProfile(Request $request)
        {
            $request->validate([
                'mobile' => 'required',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'gender' => 'nullable|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
            ]);
    
            $user = Auth::user(); // Get the authenticated user
    
            // Update the user details
            $updated_params =  $request->validate([
                'name' => 'required',
                'mobile' => 'required',
                'email' => 'required',
                'gender' => 'required',
            ]);

            // $updated_params = $request->except('profile_picture');
            $password = Hash::make($request->password);
            $updated_params['password'] = $password;
            // dd($updated_params);

            if ($uploadedFile = $request->file('profile_picture')) {
                $updated_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                    ->saveProfilePicture();
            }
            $user = auth()->user()->update($updated_params);
            // $user->update($updated_params);
    // dd($updated_params);
            return redirect()->back()->with('successMessage', 'Profile updated successfully!');
        }
        
        public function getCurrentUser()
            {
                $user = auth()->user();

                return response()->json([
                    'id' => $user->id,
                    'profile_picture' => asset('storage/uploads/user/profile-picture/' . $user->profile_picture), // Ensure this returns the full URL
                ]);
            }

        // public function logout(Request $request)
        //     {

        // if(auth()->user()->hasRole('owner')) {
        //     $redirect = 'owner-login';

        // }else if (auth()->user()->hasRole('user')) {
        //     $redirect = 'user-login';

        // }else{

        //     $redirect = 'login/admin';
        // }

        //         Auth::logout();
        //         $request->session()->invalidate();
        //         $request->session()->regenerateToken();

        //         return redirect($redirect); // Redirect to the login page
        //     }

        public function logout(Request $request)
        {
            // Fetch dynamic login URLs from settings table
            $admin_url = 'login/' . Setting::where('name', 'admin_login')->value('value');
            $owner_url = 'login/' . Setting::where('name', 'owner_login')->value('value');
            $user_url = 'login/' . Setting::where('name', 'user_login')->value('value');

            // Determine redirect URL based on user role
            if (auth()->user()->hasRole('owner')) {
                $redirect = $owner_url;
            } else if (auth()->user()->hasRole('user')) {
                $redirect = $user_url;
            }else {
                $redirect = $admin_url;
            }

            // Perform logout operations
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to the dynamically determined login URL
            return redirect($redirect);
        }
            

    public function history(Request $request )
    {
        $user = Auth::user();
        $currency = $user->countryDetail()->pluck('currency_symbol')->first();
        // $requestmodel = RequestModel::where('user_id', auth()->user()->id)->get();
        $requestmodel = RequestModel::where('user_id', auth()->user()->id)
            ->with('driverDetail') // Include the driver relationship
           ;
        return Inertia::render('pages/landing/user-web/history', [
            'requestmodel' => $requestmodel,
            'user' => $user,
            'currency' => $currency
        ]);
    }
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = RequestModel::where('user_id', auth()->user()->id)
        ->with('driverDetail');
        // dd($query->get());

           // Check if a status filter is applied
    if ($request->has('status') && !empty($request->status)) {
        switch ($request->status) {
            case '_completed':
                $query->where('is_completed', true);
                break;
            case '_cancelled':
                $query->where('is_cancelled', true);
                break;
            case '_upcoming':
                $query->where('is_completed', false)
                      ->where('is_cancelled', false);
                break;
        }
    }


        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();
// dd($results);
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function viewDetails(RequestModel $requestmodel) {
        $user = Auth::user();
        $requestmodel->stops = null;
        $rejected_drivers = $requestmodel->driverRejectedRequestDetail()->with('drivers')->get();
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();
        $requestmodel = fractal($requestmodel, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail','requestBill'])->toArray();
        $settings_supportTicket = Setting::where('category', 'customization_settings')
        ->pluck('value', 'name')
        ->toArray();     
        $supportTicket = $settings_supportTicket['enable_support_ticket_feature'] == '1';

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

            return Inertia::render('pages/landing/user-web/historyView-open',[
                'request' => $requestmodel['data'],
                'rejected_drivers' => $rejected_drivers,
                'service_location'=>null,
                'pick_icon'=>asset('image/map/pickup.png'),
                'drop_icon'=>asset('image/map/drop.png'),
                'stop_icon'=>asset('image/map/stop.png'),
                'firebaseConfig'=>$firebaseConfig,
                'user' => $user,
                'supportTicket' =>$supportTicket
            ]);
        }

        $googleMapKey = get_map_settings('google_map_key'); // Retrieve the Google Map API key
        return Inertia::render('pages/landing/user-web/historyView',
                    [
                        'request' => $requestmodel['data'],
                        'rejected_drivers' => $rejected_drivers,
                        'service_location'=>null,
                        'pick_icon'=>asset('image/map/pickup.png'),
                        'drop_icon'=>asset('image/map/drop.png'),
                        'stop_icon'=>asset('image/map/stop.png'),
                        'googleMapKey'=>$googleMapKey,
                        'firebaseConfig'=>$firebaseConfig,
                        'user' => $user,
                        'supportTicket' =>$supportTicket
                    ]
        );
    }

    public function createRequest(Request $request)
    {
        // dd($request->all());

        $rules = [
            'pick_lat'  => 'required',
            'pick_lng'  => 'required',
            'drop_lat'  =>'sometimes|required',
            'drop_lng'  =>'sometimes|required',
            'vehicle_type'=>'sometimes|required|exists:zone_types,id',
            'payment_opt'=>'sometimes|required|in:0,1,2',
            'pick_address'=>'required',
            'drop_address'=>'sometimes|required',
            'is_later'=>'sometimes|required|in:1,0',
        ];
        // Create a new validator instance
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Validation failed
            $errors = $validator->errors()->all();
            return response()->json(['status'=>false,"message"=>$errors]);
            
        }         
        /**
        * Validate payment option is available.
        * if card payment choosen, then we need to check if the user has added thier card.
        * if the paymenr opt is wallet, need to check the if the wallet has enough money to make the trip request
        * Check if thge user created a trip and waiting for a driver to accept. if it is we need to cancel the exists trip and create new one
        * Find the zone using the pickup coordinates & get the nearest drivers
        * create request along with place details
        * assing driver to the trip depends the assignment method
        * send emails and sms & push notifications to the user& drivers as well.
        */
        // dd($request->all());
        // Validate payment option is available.
        if ($request->has('is_later') && $request->is_later) {
            return $this->createRideLater($request);
        }

        $country_data = Country::where('dial_code',$request->country)->first();

        // @TODO
        // get type id
        $zone_type_detail = ZoneType::where('id', $request->vehicle_type)->first();
        $type_id = $zone_type_detail->type_id;

        // Get currency code of Request
        $service_location = $zone_type_detail->zone->serviceLocation;
        $currency_code = $service_location->currency_code;
        $currency_symbol = $service_location->currency_symbol;
        $eta_result = fractal($zone_type_detail, new EtaTransformer);

        $eta_result =json_decode($eta_result->toJson());
        // fetch unit from zone
        $unit = $zone_type_detail->zone->unit;
        // Fetch user detail
        $user_detail = auth()->user();

        $current_timestamp = Carbon::now()->timestamp.rand(0, 99);

        $request_number = 'REQ_'.$current_timestamp;

        $request_params = [
            'request_number'=>$request_number,
            'zone_type_id'=>$request->vehicle_type,
            'if_dispatch'=>false,
            'web_booking'=>true,
            'user_id'=>$user_detail->id,
            'payment_opt'=>$request->payment_opt,
            'unit'=>$unit,
            'transport_type'=>$request->transport_type,
            'requested_currency_code'=>$currency_code,
            'requested_currency_symbol'=>$currency_symbol,
            'service_location_id'=>$service_location->id
        ];

        $request_params['assign_method'] = $request->assign_method;
        $request_params['request_eta_amount'] = $eta_result->data->total;

        if($request->has('rental_package_id') && $request->rental_package_id)
        {

            $request_params['is_rental'] = true; 

            $request_params['rental_package_id'] = $request->rental_package_id;
        }
        if($request->has('goods_type_id') && $request->goods_type_id)
        {
            $request_params['goods_type_id'] = $request->goods_type_id; 
            $request_params['goods_type_quantity'] = $request->goods_type_quantity;
        }
          // store request place details
          $user = $this->user->belongsToRole('user')
                        ->where('mobile', $request->mobile)
                        ->first();
                        // dd($user);
          if($user!=null)
          {
            if($user->ride_otp==null)
            {
                $user->ride_otp=rand(1111, 9999);
                $user->save();
            }   
         }
                     

          if(!$user)
          {
            $request_params1['name'] = $request->name;
            $request_params1['mobile'] = $request->mobile;
            $request_params1['country'] = $country_data->id;
            $request_params1['ride_otp'] = rand(1111, 9999);
                      
            $user = $this->user->create($request_params1); 
             
            $user->attachRole('user');
          }  
          $request_params['user_id'] = $user->id; 
          $request_params['ride_otp'] = $user->ride_otp; 

        // store request details to db
        // DB::beginTransaction();
        // try {
            // Log::info("test1");
        $request_detail = $this->request->create($request_params);
       
        // request place detail params
        $request_place_params = [
            'pick_lat'=>$request->pick_lat,
            'pick_lng'=>$request->pick_lng,
            'drop_lat'=>$request->drop_lat,
            'drop_lng'=>$request->drop_lng,
            'pick_address'=>$request->pick_address,
            'drop_address'=>$request->drop_address];
      
        $request_detail->requestPlace()->create($request_place_params);

        $this->storeEta($request_detail,$eta_result);

        // Add Request detail to firebase database
         $this->database->getReference('requests/'.$request_detail->id)->update(['request_id'=>$request_detail->id,'request_number'=>$request_detail->request_number,'service_location_id'=>$service_location->id,'user_id'=>$request_detail->user_id,'trnasport_type'=>$request->trnasport_type,'pick_address'=>$request->pick_address,'drop_address'=>$request->drop_address,'assign_method'=>1,'active'=>1,'is_accept'=>0,'date'=>$request_detail->converted_created_at,'updated_at'=> Database::SERVER_TIMESTAMP]); 

        $selected_drivers = [];
        $notification_android = [];
        $notification_ios = [];
        $i = 0; 
        $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('userDetail');

        $mqtt_object = new \stdClass();
        $mqtt_object->success = true;
        $mqtt_object->success_message  = PushEnums::REQUEST_CREATED;
        $mqtt_object->result = $request_result; 
        DB::commit();
        // if($request->assign_method == 0)
        // {
            $nearest_drivers =  $this->fetchDriversFromFirebase($request_detail,$this->database);

            // Send Request to the nearest Drivers
             if ($nearest_drivers==null) {
                    goto no_drivers_available;
             } 
            no_drivers_available:
        // }


        return $this->respondSuccess($request_result, 'Request Created Successfully');
    }

  /**
    * Create Ride later trip
    */
    public function createRideLater($request)
    {
        /**
        * @TODO validate if the user has any trip with same time period
        *
        */
        // get type id
        $zone_type_detail = ZoneType::where('id', $request->vehicle_type)->first();
        $type_id = $zone_type_detail->type_id;

        // Get currency code of Request
        $service_location = $zone_type_detail->zone->serviceLocation;
        $currency_code = $service_location->currency_code;
        $currency_symbol = $service_location->currency_symbol;
        $trip_start_time = $request->trip_start_time;
        $secondcarbonDateTime = Carbon::parse($request->trip_start_time, $service_location->timezone)->setTimezone('UTC')->toDateTimeString();
        // $now = Carbon::now($service_location->timezone)->addHour(); 

        // fetch unit from zone
        $unit = $zone_type_detail->zone->unit;
        $eta_result = fractal($zone_type_detail, new EtaTransformer);

        $eta_result =json_decode($eta_result->toJson());

         // Calculate ETA
        // Fetch user detail
        $user_detail = auth()->user();
        // Generate request number
        $current_timestamp = Carbon::now()->timestamp.rand(0, 99);

        $request_number = 'REQ_'.$current_timestamp;

        // Convert trip start time as utc format
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        
        $trip_start_time = $secondcarbonDateTime; 
        $request_params = [
            'request_number'=>$request_number,
            'is_later'=>true,
            'zone_type_id'=>$request->vehicle_type,
            'trip_start_time'=>$trip_start_time,
            'user_id'=>$user_detail->id,
            'web_booking'=>true,
            'payment_opt'=>$request->payment_opt,
            'unit'=>$unit,
            'requested_currency_code'=>$currency_code,
            'requested_currency_symbol'=>$currency_symbol,
            'service_location_id'=>$service_location->id];

            if($request->has('request_eta_amount') && $request->request_eta_amount){
 
                $request_params['request_eta_amount'] = round($request->request_eta_amount, 2);
     
             }    
     
             if($request->has('rental_package_id') && $request->rental_package_id){
     
                 $request_params['is_rental'] = true; 
     
                 $request_params['rental_package_id'] = $request->rental_package_id;
             }
             if($request->has('goods_type_id') && $request->goods_type_id){
                 $request_params['goods_type_id'] = $request->goods_type_id; 
                 $request_params['goods_type_quantity'] = $request->goods_type_quantity;
             }

            $request_params['assign_method'] = $request->assign_method;
            $request_params['request_eta_amount'] = $eta_result->data->total;
            $user = $this->user->belongsToRole('user')
            ->where('mobile', $request->mobile)
            ->first();

            if($user->ride_otp==null)
            {
                $user->ride_otp=rand(1111, 9999);
                $user->save();
    
            }  
            if(!$user)
            {
              $country_data = Country::where('dial_code',$request->country)->first();
              $request_params1['name'] = $request->name;
              $request_params1['mobile'] = $request->mobile;
              $request_params1['country'] = $country_data->id;
              $request_params1['ride_otp'] = rand(1111, 9999);

              $user = $this->user->create($request_params1);  
              $user->attachRole('user');
            }  
            $request_params['user_id'] = $user->id; 
            $request_params['ride_otp'] = $user->ride_otp; 
          

        // store request details to db
        DB::beginTransaction();
        try {
            $request_detail = $this->request->create($request_params);
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

            // $ad_hoc_user_params['name'] = $request->name;
            // $ad_hoc_user_params['mobile'] = $request->mobile;


            $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('userDetail');
            // @TODO send sms & email to the user
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            Log::error('Error while Create new schedule request. Input params : ' . json_encode($request->all()));
            return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        }
        DB::commit();

        return $this->respondSuccess($request_result, 'Request Scheduled Successfully');
    }


    // Support Ticket Module

    public function getSupport()
    {
        // $ticket_title = SupportTicketTitle::active()->get();
        $user = Auth::user();
        return Inertia::render('pages/landing/user-web/support/get_support',['user' => $user]);
    }

    public function supportList(QueryFilterContract $queryFilter, Request $request)
    {
        $query = SupportTicket::query()->orderBy('created_at','DESC')->where("users_id",auth()->user()->id);
        $results = $queryFilter->builder($query)->customFilter(new TicketFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function createTicket(Request $request)
    {
        $app_for = env("APP_FOR");
        $titleType = $request->query('titleType');
        $request_id = $request->query('id');  
        $title = SupportTicketTitle::where('user_type', 'user')->get();
        $service_location = ServiceLocation::active()->get();
        $user = Auth::user();
    
        return Inertia::render('pages/landing/user-web/support/create_ticket', [
            'app_for' => $app_for,
            'title' => $title,
            'titleType' => $titleType, 
            'user' => $user,
            'service_location'=> $service_location,
            'request_id'=>$request_id
        ]);
    }
    

   public function store(Request $request)
    {      // if(env('APP_FOR') == 'demo'){
        //     return response()->json([
        //         'alertMessage' => 'You are not Authorized'
        //     ], 403);
        // }
        // dd($request->all());
         // Validate the incoming request
        $validated = $request->validate([
            'title_id' => 'required',
            'description' => 'required',
            'service_location_id' => 'required',
        ]);

        $created_params['title_id'] = $validated['title_id'];
        $created_params['service_location_id'] = $validated['service_location_id'];


        $support_type = SupportTicketTitle::where('id', $validated['title_id'])->value('title_type'); 
        $created_params['support_type'] = $support_type;

        $created_params['description'] = $validated['description'];
        $created_params['assign_to'] = null;
        $user = Auth::user();
        $created_params['users_id'] = $user->id;
        $request_detail = $user->requestDetail->first();
        if($support_type  == 'request'){
            $created_params['request_id'] = $request_detail->request_number;
        }
        else{
            $created_params['request_id'] = null;
        }

        $current_timestamp = Carbon::now()->timestamp.rand(0, 99);

        $created_params['ticket_id'] = 'TIC_'.$current_timestamp;

        $created_params['status'] = 1;
        
        // dd($created_params);
        
        $ticket = SupportTicket::create($created_params);
        // dd($request->hasFile('files'));

        // Store Multiple Files in MultiImages Table
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = $this->imageUploader->file($file)->saveSupportImage();

                SupportTicketMultiFile::create([
                    'ticket_id' => $ticket->id, 
                    'image_name' => $filename, // Store consistent filename
                ]);
            }
        }

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Title created successfully.',
            'ticket' => $ticket,
        ], 201);
    }

    public function viewTicketDetails(SupportTicket $supportTicket) {
        $user = Auth::user();
        // $reply_message = SupportTicketMessage::where('ticket_id', $supportTicket->id)->get();
        $reply_message = SupportTicketMessage::where('ticket_id', $supportTicket->id)
            ->orderBy('created_at', 'asc') // Ensure messages are sorted in ascending order
            ->get();
        $attachment = SupportTicketMultiFile::where('ticket_id', $supportTicket->id)->get();
        // dd($attachment); 
        return Inertia::render('pages/landing/user-web/support/view_ticket', ['supportTicket'=>$supportTicket,
        'user' => $user,'reply_message'=>$reply_message,'attachment' => $attachment
         ]);
    }

    public function replyMessage(Request $request, SupportTicket $supportTicket)
    {      // if(env('APP_FOR') == 'demo'){
        //     return response()->json([
        //         'alertMessage' => 'You are not Authorized'
        //     ], 403);
        // }
        // dd($request->all());
         // Validate the incoming request
        $validated = $request->validate([
            'message' => 'required',
        ]);

        $created_params['message'] = $validated['message'];
        $created_params['ticket_id'] = $supportTicket->id;
        $created_params['user_id'] = $supportTicket->users_id;
        $created_params['employee_id'] = $supportTicket->assign_to;
        $created_params['sender_id'] = $supportTicket->users_id;
        // dd($created_params);
       
        $reply_ticket = SupportTicketMessage::create($created_params);
        // dd($reply_ticket);


        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Title created successfully.',
            'reply_ticket' => $reply_ticket,
        ], 201);
    }

    

}
