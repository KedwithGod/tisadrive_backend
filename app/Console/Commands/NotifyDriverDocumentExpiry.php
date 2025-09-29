<?php

namespace App\Console\Commands;

use App\Base\Constants\Masters\DriverDocumentStatus;
use App\Mail\Driver\DriverDocumentExpiryMail;
use App\Models\Admin\DriverDocument;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
class NotifyDriverDocumentExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:document:expires';

    protected $database;


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Mail to Driver regards Document Expires';

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
        $driverDocuments = DriverDocument::whereDate('expiry_date', '<=', Carbon::today()->toDateString())->get();
        foreach ($driverDocuments as $doc) {
            $docExpiryDate = $doc->getOriginal('expiry_date');

                    
                    if($doc->driver->approve){
                        $doc->driver->update([
                        'approve' => false,
                        'reason' => "Document Expired",
                    ]);
                    $doc->update([
                    'document_status' => DriverDocumentStatus::EXPIRED_AND_DECLINED
                    ]);

                    $notifable_driver = $doc->driver->user;

                    // $title = custom_trans('document_expired_title',[],$notifable_driver->lang);
                    // $body = custom_trans('document_expired_body',[],$notifable_driver->lang);

                    // dispatch(new SendPushNotification($notifable_driver,$title,$body));

                    $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Document Expired') // Match the correct topic
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

                    $this->database->getReference('drivers/driver_'.$doc->driver->id)->update(['approve'=>0,'updated_at'=> Database::SERVER_TIMESTAMP]);

                    $this->info('Declined successfully');
                
                    }
                    
        }

        $this->info('Command run successfully');
    }
}
