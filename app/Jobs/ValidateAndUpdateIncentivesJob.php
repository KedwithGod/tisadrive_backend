<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Admin\Driver;
use App\Models\Admin\Incentive;
use App\Models\Admin\DriverLevelUp;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Payment\DriverIncentiveHistory;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Request\Request as RequestRequest;
use Carbon\Carbon;

class ValidateAndUpdateIncentivesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request_detail;


    /**
     * Create a new job instance.
     */
    public function __construct($request_detail)
    {
        $this->request_detail = $request_detail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $driver = Driver::whereId($this->request_detail->driver_id)->first();
        
        $currentDate = Carbon::today();

        $daily_incentive = [];
        $weekly_incentive = [];

        $signed_type = $driver->driverVehicleTypeDetail()->where('signed_vehicle',true)->first()->vehicle_type;
        
        $driver_completed_rides_today = RequestRequest::whereDate('trip_start_time', $currentDate)
                        ->whereHas('zoneType',function($typeQuery) use ($signed_type) {
                            $typeQuery->where('type_id',$signed_type);
                        })
                        ->where('driver_id', $driver->id)
                        ->where('is_completed', true)
                        ->count();
        
        if ($driver_completed_rides_today) {

            $driver_incentive_exists = Incentive::where('mode', 'daily')
                ->whereHas('zoneTypeDetail',function($typeQuery) use ($signed_type) {
                    $typeQuery->where('type_id',$signed_type);
                })
                ->where('ride_count', '<=', $driver_completed_rides_today)
                ->pluck('ride_count')
                ->map(fn($count) => (int)$count)
                ->toArray();
            
            $driver_credited_incentive = DriverIncentiveHistory::where('driver_id', $driver->id)
                ->where('mode', 'daily')
                ->whereDate('date', $currentDate)
                ->pluck('ride_count')
                ->map(fn($count) => (int)$count)
                ->toArray();
            
            $daily_incentive = array_diff($driver_incentive_exists,$driver_credited_incentive);
        
        }
        // Calculate the most recent completed Sunday
        $mostRecentSunday = $currentDate->copy()->startOfWeek()->subDay(); 
        
        // Query to get the completed rides count
        $driver_completed_rides_this_week = RequestRequest::where('driver_id', $driver->id)
            ->where('is_completed', true)
            ->whereHas('zoneType',function($typeQuery) use ($signed_type) {
                $typeQuery->where('type_id',$signed_type);
            })
            ->whereDate('trip_start_time', '>=', $mostRecentSunday)
            ->whereDate('trip_start_time', '<=', $currentDate)
            ->count();
        
        if ($driver_completed_rides_this_week) {

            $driver_weekly_incentive_exists = Incentive::where('mode', 'weekly')
                ->whereHas('zoneTypeDetail',function($typeQuery) use ($signed_type) {
                    $typeQuery->where('type_id',$signed_type);
                })
                ->where('ride_count', '<=', $driver_completed_rides_this_week)
                ->pluck('ride_count')
                ->map(fn($count) => (int)$count)
                ->toArray();
            
            $driver_weekly_incentive_credited = DriverIncentiveHistory::where('driver_id', $driver->id)
                ->where('mode', 'weekly')
                ->whereDate('created_at', '>=', $mostRecentSunday)
                ->whereDate('created_at', '<=', $currentDate)
                ->pluck('ride_count')
                ->map(fn($count) => (int)$count)
                ->toArray();
            
            $weekly_incentive = array_diff($driver_weekly_incentive_exists,$driver_weekly_incentive_credited);
            
        }
        $driver_wallet = DriverWallet::where('user_id', $driver->id)->first();
        
        if(count($daily_incentive)>0 || count($weekly_incentive)>0)
        {
            foreach ($daily_incentive as $ride_count) {
                $incentive = Incentive::where('mode', 'daily')
                    ->whereHas('zoneTypeDetail',function($typeQuery) use ($signed_type) {
                        $typeQuery->where('type_id',$signed_type);
                    })
                    ->where('ride_count', $ride_count)->first();
            
                if ($incentive) {
                    DriverIncentiveHistory::create([
                        'driver_id' => $driver->id,
                        'amount' => $incentive->amount,
                        'mode' => 'daily',
                        'ride_count' => $ride_count,
                        'date' => $currentDate,
                    ]);
            
                    $driver_wallet->amount_added += $incentive->amount;
                    $driver_wallet->amount_balance += $incentive->amount;
                    $driver_wallet->save();
            
                    $driver->driverWalletHistory()->create([
                        'amount' => $incentive->amount,
                        'transaction_id' => str_random(6),
                        'remarks' => WalletRemarks::INCENTIVE_AMOUNT,
                        'is_credit' => true,
                    ]);
                }
            }
            
            foreach ($weekly_incentive as $ride_count) {
                $incentive = Incentive::where('mode', 'weekly')
                    ->whereHas('zoneTypeDetail',function($typeQuery) use ($signed_type) {
                        $typeQuery->where('type_id',$signed_type);
                    })
                    ->where('ride_count', $ride_count)->first();
            
                if ($incentive) {
                    DriverIncentiveHistory::create([
                        'driver_id' => $driver->id,
                        'amount' => $incentive->amount,
                        'mode' => 'weekly',
                        'ride_count' => $ride_count,
                        'date' => $currentDate,
                    ]);
            
                    $driver_wallet->amount_added += $incentive->amount;
                    $driver_wallet->amount_balance += $incentive->amount;
                    $driver_wallet->save();
            
                    $driver->driverWalletHistory()->create([
                        'amount' => $incentive->amount,
                        'transaction_id' => str_random(6),
                        'remarks' => WalletRemarks::WEEKLY_INCENTIVE_AMOUNT,
                        'is_credit' => true,
                    ]);
                }
            }
            // $title = custom_trans('daily_incentive_title');
            // $body = custom_trans('daily_incentive_notify_body');
        
            // dispatch(new SendPushNotification($driver,$title,$body));
        
            if(count($daily_incentive)>0){
                $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Daily Incentive') // Match the correct topic
                ->first();
        
            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $driver->user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($driver, $title, $body));
                }
            }
        
            if(count($weekly_incentive)>0){
                
                $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Weekly Incentive') // Match the correct topic
                ->first();
        
            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $driver->user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($driver, $title, $body));
                }
            }
            
        
        }
        
        
    }
}
