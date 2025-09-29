<?php

namespace App\Http\Controllers\Api\V1\Request;

use App\Models\User;
use App\Jobs\NotifyViaMqtt;
use Illuminate\Http\Request;
use App\Jobs\NotifyViaSocket;
use App\Base\Constants\Masters\PushEnums;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Request\Request as RequestModel;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Jobs\Notifications\SendPushNotification;
use Log;


/**
 * @group Driver-trips-apis
 *
 * APIs for Driver-trips apis
 */
class DriverTripStartedController extends BaseController
{
    protected $request;

    public function __construct(RequestModel $request)
    {
        $this->request = $request;
    }

    /**
    * Driver Trip started
    * @bodyParam request_id uuid required id of request
    * @bodyParam pick_lat double required pikup lat of the user
    * @bodyParam pick_lng double required pikup lng of the user
    * @bodyParam ride_otp string optional otp of the trip request
    *
    * @response 
    * {
    *     "success": true,
    *     "message": "driver_trip_started"
    * }
    */
    public function tripStart(Request $request)
    {
        $request->validate([
        'request_id' => 'required|exists:requests,id',
        'pick_lat'  => 'required',
        'pick_lng'  => 'required',
        'ride_otp'=>'sometimes|required'
        ]);
        // Get Request Detail
        $request_detail = $this->request->where('id', $request->input('request_id'))->first();

        if($request->has('ride_otp')){

        if($request_detail->ride_otp != $request->ride_otp){

          $this->throwCustomException('provided otp is invalid');
        }

        }


        // Validate Trip request data
        $this->validateRequest($request_detail);
        // Update the Request detail with arrival state
        $update_params = ['is_trip_start'=>true,'trip_start_time'=>date('Y-m-d H:i:s')];
        if($request_detail->transport_type == 'delivery' && get_settings('show_delivery_ride_drop_otp_feature')=='1' && get_settings('show_delivery_ride_pick_otp_feature')=='1'){
            $update_params['ride_otp'] = rand(1111, 9999);
        }
        $request_detail->update($update_params);
        // Update pickup detail to the request place table
        $request_place = $request_detail->requestPlace;
        $request_place->pick_lat = $request->input('pick_lat');
        $request_place->pick_lng = $request->input('pick_lng');
        $request_place->save();
        if ($request_detail->if_dispatch) {
            goto dispatch_notify;
        }
        // Send Push notification to the user
        // $title = custom_trans('trip_started_title');
        // $body = custom_trans('trip_started_body');


        if($request_detail->user_id){
            $user = User::find($request_detail->user_id);
            // $title = custom_trans('trip_started_title',[],$user->lang);
            // $body = custom_trans('trip_started_body',[],$user->lang);        
            // dispatch(new SendPushNotification($user,$title,$body));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'User Trip Started') // Match the correct topic
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
        dispatch_notify:
        return $this->respondSuccess(null, 'driver_trip_started');
    }
    /**
     * Ready to pickup
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     * */
    public function readyToPickup(Request $request)
    {
        $request->validate([
        'request_id' => 'required|exists:requests,id',
        ]);

        $request_detail = $this->request->where('id', $request->input('request_id'))->first();
        $this->validateRequest($request_detail);

        $driver = auth()->user()->driver;

        $request_detail->update(['is_driver_started'=>true]);

        $driver->available = false;
        $driver->save();

        $user = User::find($request_detail->user_id);

        // $title = custom_trans('driver_is_on_the_way_to_pickup_title');
        // $body = custom_trans('driver_is_on_the_way_to_pickup_body');

        // $title = custom_trans('driver_is_on_the_way_to_pickup_title',[],$user->lang);
        // $body = custom_trans('driver_is_on_the_way_to_pickup_body',[],$user->lang);
            
        // dispatch(new SendPushNotification($user,$title,$body));

        $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver On the way to pickup') // Match the correct topic
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

        return $this->respondSuccess(null, 'driver_started_to_pickup');


    }

    /**
     * Validate Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateRequest($request_detail)
    {
        if ($request_detail->driver_id!=auth()->user()->driver->id) {
            $this->throwAuthorizationException();
        }

        if ($request_detail->is_trip_start) {
            $this->throwCustomException('trip started already');
        }

        if ($request_detail->is_completed) {
            $this->throwCustomException('request completed already');
        }
        if ($request_detail->is_cancelled) {
            $this->throwCustomException('request cancelled');
        }
    }
}
