<?php

namespace App\Jobs;

use App\Jobs\NotifyViaMqtt;
use App\Jobs\NotifyViaSocket;
use Illuminate\Bus\Queueable;
use App\Models\Request\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Base\Constants\Masters\PushEnums;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Transformers\Requests\CronTripRequestTransformer;
use App\Jobs\Notifications\SendPushNotification;
use Kreait\Firebase\Contract\Database;

class NoDriverFoundNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $requestids;
    
    public $database;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestids, Database $database)
    {
        $this->requestids = $requestids;
        $this->database = $database;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $app_for = config('app.app_for');

        foreach ($this->requestids as $key => $request_id) {
            $request_detail = Request::find($request_id);
            $request_detail->update(['is_cancelled'=>true,'cancel_method'=>0,'cancelled_at'=>date('Y-m-d H:i:s')]);
            
        if($app_for == 'bidding')
        {
            $this->database->getReference('bid-meta/'.$request_detail->id)->remove();
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
