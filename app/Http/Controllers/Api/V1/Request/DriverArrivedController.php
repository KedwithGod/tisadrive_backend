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
use Kreait\Firebase\Contract\Database;

/**
 * @group Driver-trips-apis
 *
 * APIs for Driver-trips apis
 */
class DriverArrivedController extends BaseController
{
    protected $request;

    public function __construct(RequestModel $request,Database $database)
    {
        $this->request = $request;
        $this->database = $database;

    }
    /**
    * Driver Arrived
    * @bodyParam request_id uuid required id request
    * @response {
    *     "success": true,
    *     "message": "driver_arrived"
    * }
    */
    public function arrivedRequest(Request $request)
    {
        // Validate Request id
        $request->validate([
        'request_id' => 'required|exists:requests,id',
        ]);
        // Get Request Detail
        $request_detail = $this->request->where('id', $request->input('request_id'))->first();
        // Validate Trip request data
        $this->validateRequest($request_detail);
        // Update the Request detail with arrival state
        $request_detail->update(['is_driver_arrived'=>true,'arrived_at'=>date('Y-m-d H:i:s')]);
        if ($request_detail->if_dispatch) {
            goto dispatch_notify;
        }
        // Send Push notification to the user
        $user = User::find($request_detail->user_id);
        // $title = custom_trans('driver_arrived_title');
        // $body = custom_trans('driver_arrived_body');

        // $title = custom_trans('driver_arrived_title', [], $user->lang);
        // $body = custom_trans('driver_arrived_body', [], $user->lang);

        $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('driverDetail');

        $pus_request_detail = $request_result->toJson();
        $push_data = ['notification_enum'=>PushEnums::DRIVER_ARRIVED,'result'=>(string)$pus_request_detail];

        // dispatch(new SendPushNotification($user,$title,$body));

        $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Arrived') // Match the correct topic
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
        dispatch_notify:

        
        $this->database->getReference('bid-meta/'.$request->id)->remove();
        
        return $this->respondSuccess(null, 'driver_arrived');
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

        if ($request_detail->is_driver_arrived) {
            $this->throwCustomException('arrived already');
        }

        if ($request_detail->is_completed) {
            $this->throwCustomException('request completed already');
        }
        if ($request_detail->is_cancelled) {
            $this->throwCustomException('request cancelled');
        }
    }
}
