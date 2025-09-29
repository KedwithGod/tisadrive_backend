<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Request\Request as RequestModel;
use App\Base\Filters\Admin\RequestFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin\Zone;
use App\Models\Admin\VehicleType;
use App\Models\Admin\Driver;
use App\Models\ThirdPartySetting;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Admin\Setting;
use App\Models\Admin\InvoiceConfiguration;

class DeliveryRequestController extends Controller
{
    // public function __construct(User $user,Database $database,RequestModel $requestmodel)
    // {
    //     $this->database = $database;
    //     $this->user = $user;
    //     $this->requestmodel = $requestmodel;
    // }
    public function ridesRequest() {
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
        return Inertia::render('pages/delivery_rides_request/index',[
            'zones' => Zone::active()->get(),
            'types' => VehicleType::active()->get(),
            'ongoing_rides' => $ongoing,
            'firebaseConfig' => $firebaseConfig,
            'enable_outstation' => get_settings('show_outstation_ride_feature') || get_settings('show_delivery_outstation_ride_feature'),
        ]);
    }

    public function viewDetails(RequestModel $requestmodel) {
        $requestmodel->stops = null;
        $rejected_drivers = $requestmodel->driverRejectedRequestDetail()->with('drivers')->get();
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();
        $requestmodel = fractal($requestmodel, new TripRequestTransformer)->toArray();
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
            return Inertia::render('pages/delivery_rides_request/open-view',
                [
                    'request' => $requestmodel['data'],
                    'service_location'=>null,
                    'pick_icon'=>asset('image/map/pickup.png'),
                    'drop_icon'=>asset('image/map/drop.png'),
                    'firebaseConfig'=>$firebaseConfig,
                ]);
        }
        $googleMapKey = get_map_settings('google_map_key'); // Retrieve the Google Map API key
        return Inertia::render('pages/delivery_rides_request/view',
                    [
                        'request' => $requestmodel['data'],
                        'rejected_drivers' => $rejected_drivers,
                        'service_location'=>null,
                        'pick_icon'=>asset('image/map/pickup.png'),
                        'drop_icon'=>asset('image/map/drop.png'),
                        'stop_icon'=>asset('image/map/stop.png'),
                        'googleMapKey'=>$googleMapKey,
                        'firebaseConfig'=>$firebaseConfig,
                    ]
        );
    }
    
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = RequestModel::where('requests.transport_type', 'delivery')
            ->with('userDetail', 'driverDetail')
            ->orderBy('created_at', 'DESC');
    
        if (auth()->user()->hasRole('owner')) {
            // Retrieve the specific owner associated with the authenticated user
            $owner = auth()->user()->owner;
            $query = RequestModel::where('owner_id', $owner->id)
                ->orWhere('booked_by', auth()->user()->id)
                ->with('userDetail', 'driverDetail')
                ->orderBy('created_at', 'DESC');
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
            // $title = custom_trans('trip_cancelled_by_user_title',[],$notifiable_driver->lang);
            // $body = custom_trans('trip_cancelled_by_user_body',[],$notifiable_driver->lang);
            // dispatch(new SendPushNotification($notifiable_driver,$title,$body));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Trip Cancelled') // Match the correct topic
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
        $requestmodel->update($update_parms);

        return response()->json([
            'successMessage' => 'Trip Cancelled successfully',
            'request' => $requestmodel,
        ]);
    }


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
}
