<?php

namespace App\Console\Commands;

use App\Models\Admin\Driver;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Jobs\Notifications\SendPushNotification;
use Log;

class OfflineUnAvailableDrivers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offline:drivers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Offline Un Available Drivers';

    protected $database;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Database $database)
    {
        parent::__construct();
        $this->database = $database;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current_timestamp = Carbon::now()->timestamp;
        $conditional_timestamp = Carbon::now()->subMinutes(15);

        $one_hr_conditional_time = Carbon::now()->subMinutes(10);

        $drivers = $this->database->getReference('drivers')->orderByChild('is_active')->equalTo(1)->getValue();
        foreach ($drivers as $key => $driver) {
            $driver_updated_at = Carbon::createFromTimestamp($driver['updated_at'] / 1000);
            
                $mysql_driver = Driver::where('id', $driver['id'])->first();
                // Check if the driver is on trip
                if($mysql_driver && $mysql_driver->requestDetail()->where('is_completed',false)->where('is_cancelled',false)->exists()){
                    goto end;
                }

            if($one_hr_conditional_time > $driver_updated_at){
                goto make_offline;
            }

            
            
            if ($conditional_timestamp > $driver_updated_at) {
                
                $this->info("some-drivers-are-there");

                if ($mysql_driver){
                    $notifable_driver = $mysql_driver->user;
                    // $title = custom_trans('reminder_push_title',[],$notifable_driver->lang);
                    // $body = custom_trans('reminder_push_body',[],$notifable_driver->lang);

                    // dispatch(new SendPushNotification($notifable_driver,$title,$body));

                     $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Ride Remainder') // Match the correct topic
                ->first();

            //    send push notification 
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
                    dispatch(new SendPushNotification($notifable_driver, $title, $body));
                }

                }
                
                
                make_offline:

                // Get last online record
                if ($mysql_driver && $mysql_driver->driverAvailabilities()->exists()) {

                    $updatable_offline_date_time = Carbon::createFromTimestamp($driver['updated_at']/1000);

                    $availability = $mysql_driver->driverAvailabilities()->where('is_online', true)->orderBy('online_at', 'desc')->first();
                    $created_params['duration'] = 0;
                    if ($availability) {
                        $last_online_date_time = Carbon::parse($availability->online_at);
                        $last_offline_date = Carbon::parse($updatable_offline_date_time);
                        $duration = $last_offline_date->diffInMinutes($last_online_date_time);
                        $created_params['duration'] = $availability->duration+$duration;
                        $availability->update(['is_online'=>false,'offline_at'=>$updatable_offline_date_time,'duration'=>$availability->duration+$duration]);

                    }else{
                        $created_params['duration'] = 0;  
                        $created_params['is_online'] = false;
                        $created_params['online_at'] = $updatable_offline_date_time;
                        $created_params['offline_at'] = $updatable_offline_date_time;
                        $mysql_driver->driverAvailabilities()->create($created_params);

                    }
                    
                    $mysql_driver->active = 0;
                    $mysql_driver->save();

                    $this->database->getReference('drivers/'.'driver_'.$driver['id'])->update(['is_active'=>0,'updated_at'=> Database::SERVER_TIMESTAMP]);

                }elseif ($mysql_driver) {
                    $updatable_offline_date_time = Carbon::createFromTimestamp($driver['updated_at']/1000);
                    $mysql_driver->active = 0;
                    $mysql_driver->save();
                    $created_params['duration'] = 0;  
                    $created_params['is_online'] = false;
                    $created_params['online_at'] = $updatable_offline_date_time;
                    $created_params['offline_at'] = $updatable_offline_date_time;
                    $mysql_driver->driverAvailabilities()->create($created_params);

                    $this->database->getReference('drivers/'.'driver_'.$driver['id'])->update(['is_active'=>0,'updated_at'=> Database::SERVER_TIMESTAMP]);

                }{}

                if(!$driver['is_available']){

                    if($mysql_driver){
                        $mysql_driver->active = 0;
                        $mysql_driver->save();
                    }
                    $this->database->getReference('drivers/'.'driver_'.$driver['id'])->update(['is_active'=>0,'updated_at'=> Database::SERVER_TIMESTAMP]);
                }

                end:
                
                
                if(!$mysql_driver){
                    $this->database->getReference('drivers/'.'driver_'.$driver['id'])->remove();
                }
            }

        $this->info("no-drivers-found");

        }

        $this->info("success");
    }
}
