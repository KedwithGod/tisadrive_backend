<?php

namespace App\Helpers\Rides;

use App\Models\Request\Request;
use App\Base\Constants\Masters\PushEnums;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Transformers\Requests\CronTripRequestTransformer;
use App\Jobs\Notifications\SendPushNotification;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Log;

trait NoDriversFoundHelper
{

    /**
     * Respond with drivers data.
     * Status code = 200
     *
     * @param mixed|null $data
     * @param string|null $message
     * @return \Illuminate\Http\JsonResponse
     */
    //
    protected function noDriverFound($requestids,$database)
    {

        $app_for = config('app.app_for');

        foreach ($requestids as $key => $request_id) {
            $request_detail = Request::find($request_id);
            $request_detail->update(['is_cancelled'=>true,'cancel_method'=>0,'cancelled_at'=>date('Y-m-d H:i:s')]);
            
        if($app_for == 'bidding')
        {
            $database->getReference('bid-meta/'.$request_detail->id)->remove();
        }
            $request_detail->fresh();
    
            // $title = custom_trans('no_driver_found_title',[],$request_detail->userDetail->lang);
            // $body = custom_trans('no_driver_found_body',[],$request_detail->userDetail->lang);
            Log::info('nodriverfound');

            if ($request_detail->userDetail()->exists()) {

                if($request_detail->userDetail->fcm_token){
                $user = $request_detail->userDetail;
                Log::info($user);

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

            }
        }
    }
}
