<?php

namespace App\Http\Controllers\Api\V1\Request;

use App\Models\User;
use App\Jobs\NotifyViaMqtt;
use App\Models\Admin\Driver;
use Illuminate\Http\Request;
use App\Jobs\NotifyViaSocket;
use App\Models\Request\RequestMeta;
use App\Base\Constants\Masters\PushEnums;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Request\Request as RequestModel;
use App\Http\Requests\Request\AcceptRejectRequest;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Models\Request\DriverRejectedRequest;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
use Sk\Geohash\Geohash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Helpers\Rides\FetchDriversFromFirebaseHelpers;
use Carbon\Carbon;
use App\Http\Controllers\Api\V1\Payment\Stripe\StripeController;


/**
 * @group Driver-trips-apis
 *
 * APIs for Driver-trips apis
 */
class RequestAcceptRejectController extends StripeController
{
    use FetchDriversFromFirebaseHelpers;

    protected $request;

    protected $database;


    public function __construct(RequestModel $request,Database $database)
    {
        $this->request = $request;
        $this->database = $database;
    }

    /**
    * Driver Response for Trip Request
    * @bodyParam request_id uuid required id request
    * @bodyParam is_accept boolean required response of request i.e accept or reject. input should be 0 or 1.
    * @response {
    * "success": true,
    * "message": "success"}
    */
    public function respondRequest(AcceptRejectRequest $request)
    {

        /**
        * Get Request Detail
        * Validate the request i,e the request is already accepted by some one and it is a valid request for accept or reject state.
        * If is_accept is true then update the driver's id to the request detail.
        * And Update the driver's available state as false. And delete all meta driver records from request_meta table
        * Send the notification to the user with request detail.
        * If is_accept is false, then delete the driver record from request_meta table
        * And Send the request to next driver who is available in the request_meta table
        * If there is no driver available in request_meta table, then send notification with no driver available state to the user.
        */
        // Get Request Detail
        $request_detail = $this->request->where('id', $request->input('request_id'))->first();

        // Validate the request i,e the request is already accepted by some one and it is a valid request for accept or reject state.
        $this->validateRequestDetail($request_detail);
        $driver = auth()->user()->driver;

        // Delete Meta Driver From Firebase
        // $this->database->getReference('request-meta/'.$request_detail->id)->set(['driver_id'=>'','request_id'=>$request_detail->id,'user_id'=>$request_detail->user_id,'active'=>1,'updated_at'=> Database::SERVER_TIMESTAMP]);

        // $this->database->getReference('request-meta/'.$request_detail->id.'/'.$driver->id)->remove();




        if ($request->input('is_accept')) {

            $this->database->getReference('request-meta/'.$request_detail->id)->set(['driver_id'=>$driver->id,'request_id'=>$request_detail->id,'user_id'=>$request_detail->user_id,'active'=>1,'is_accepted'=>1,'profile_picture'=>auth()->user()->profile_picture,'name'=>auth()->user()->name,'rating'=>auth()->user()->rating,'updated_at'=> Database::SERVER_TIMESTAMP]); 
            $this->database->getReference('requests/'.$request_detail->id)->update(['is_accept' => 1]);

            $this->database->getReference('request-meta/'.$request_detail->id)->remove();

            // Update Driver to the trip request detail
            $updated_params = ['driver_id'=>$driver->id,
                                'accepted_at'=>date('Y-m-d H:i:s'),
                              ];
            if(!$request_detail->is_later) {
                $updated_params['is_driver_started'] = true;
            }else{
                $minutes_before_trip = get_settings('minimum_time_for_starting_trip_drivers_for_schedule_ride');
                $condition_time = [Carbon::parse($request_detail->trip_start_time),Carbon::parse($request_detail->trip_start_time)->subMinutes($minutes_before_trip+5)];
                $current_time = Carbon::now();
                if ($current_time->between($condition_time[0],$condition_time[1])) {
                    $updated_params['is_driver_started'] = true;
                }
            }

            if($driver->owner_id!=null)
            {
                // Log::info("--------Accepted_Fleet_driver--------");

                // Log::info($driver);
                $updated_params['owner_id'] = $driver->owner_id;

                $updated_params['fleet_id'] = $driver->fleet_id;
            }

            $request_detail->update($updated_params);
            $request_detail->fresh();
            // Delete all Meta records of the request
            $this->deleteMetaRecords($request);
            // Update the driver's available state as false
            $driver->available = false;

            $driver->save();
            $request_result =  fractal($request_detail, new TripRequestTransformer);
            $push_request_detail = $request_result->toJson();
            if ($request_detail->if_dispatch) {
                goto accet_dispatch_notify;
            }
            $user = User::find($request_detail->user_id);
            // $title = custom_trans('trip_accepted_title');
            // $body = custom_trans('trip_accepted_body');

            // $title = custom_trans('trip_accepted_title',[],$user->lang);
            // $body = custom_trans('trip_accepted_body',[],$user->lang);  
                  
            $push_data = ['notification_enum'=>PushEnums::TRIP_ACCEPTED_BY_DRIVER,'result'=>(string)$push_request_detail];
            // dispatch(new SendPushNotification($user,$title,$body));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'User Trip Request Accepted') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($user, $title, $body));
                }

             accet_dispatch_notify:
        // @TODO send sms,email & push notification with request detail
        } else {

            // Log::info("auto cancel");

            $this->database->getReference('request-meta/'.$request_detail->id)->set(['driver_id'=>$driver->id,'request_id'=>$request_detail->id,'user_id'=>$request_detail->user_id,'active'=>1,'is_accepted'=>0,'profile_picture'=>auth()->user()->profile_picture,'name'=>auth()->user()->name,'rating'=>auth()->user()->rating,'updated_at'=> Database::SERVER_TIMESTAMP]); 

            // Log::info('request-number');
            // Log::info($request_detail->request_number);
            // Log::info('----------');
            $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('userDetail');
            // Save Driver Reject Requests
            DriverRejectedRequest::create(['request_id'=>$request_detail->id,
                'driver_id'=>$driver->id]);

            $push_request_detail = $request_result->toJson();
            // Delete Driver record from meta table
            RequestMeta::where('request_id', $request->input('request_id'))->where('driver_id', $driver->id)->delete();

                 // Send Ride to the Nearest Next Driver
                $this->fetchDriversFromFirebase($request_detail,$this->database);

                goto end;

                // Cancell the request as automatic cancell state
                if($request_detail->payment_intent_id){
                    $this->cancel($request_detail->payment_intent_id);
                }
                $request_detail->update(['is_cancelled'=>true,'cancel_method'=>0,'cancelled_at'=>date('Y-m-d H:i:s')]);
                $this->database->getReference('bid-meta/'.$request_detail->id)->remove();
                $request_result =  fractal($request_detail, new TripRequestTransformer);
                $push_request_detail = $request_result->toJson();
                // Send push notification as no-driver-found to the user
                if ($request_detail->if_dispatch) {
                    goto dispatch_notify;
                }
                $user = User::find($request_detail->user_id);
                // $title = custom_trans('no_driver_found_title');
                // $body = custom_trans('no_driver_found_body');

                // $title = custom_trans('no_driver_found_title',[],$user->lang);
                // $body = custom_trans('no_driver_found_body',[],$user->lang);                
                // dispatch(new SendPushNotification($user,$title,$body));
                $push_data = ['notification_enum'=>PushEnums::NO_DRIVER_FOUND,'result'=>(string)$push_request_detail];
                dispatch_notify:
                no_drivers_available:

                $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver not Found') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($user, $title, $body));
                }

        }
        end:

         Artisan::call('assign_drivers:for_regular_rides');

        return $this->respondSuccess();
    }

    /**
    * Delete All Meta driver's records
    */
    public function deleteMetaRecords(Request $request)
    {
        RequestMeta::where('request_id', $request->input('request_id'))->delete();
    }

    /**
    * Validate the request detail
    */
    public function validateRequestDetail($request_detail)
    {
        if ($request_detail->is_driver_started && $request_detail->driver_id!=auth()->user()->driver->id) {
            $this->throwCustomException('request accepted by another driver');
        }

        if ($request_detail->is_completed) {
            $this->throwCustomException('request completed already');
        }
        if ($request_detail->is_cancelled) {
            $this->throwCustomException('request already cancelled');
        }
    }


}
