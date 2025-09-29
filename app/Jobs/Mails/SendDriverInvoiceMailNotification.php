<?php

namespace App\Jobs\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Mail\DriverInvoiceMail;
use App\Models\Admin\Setting;
use App\Models\Admin\InvoiceConfiguration;
use App\Models\ThirdPartySetting;
use Illuminate\Support\Facades\Log;

class SendDriverInvoiceMailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $driver;
    protected $data;
    protected $logo;
    protected $invoice;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($driver,$data, $logo, $invoice)
    {
        $this->driver = $driver; 
        $this->data = $data;
        $this->logo = $logo;
        $this->invoice = $invoice;     
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $notification = \DB::table('notification_channels')
                ->where('topics', 'Invoice For End of the Ride Driver') // Match the correct topic
                ->first();
        
                // dd($notification);
        
                if ($notification && $notification->mail == 1) {

                    $userLang = $driver->lang ?? 'en';
            
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
            
                    $notificationData = [
                    'email_subject' =>  $translation->email_subject ?? $notification->email_subject,
                    'mail_body' => str_replace('{name}', $this->driver->name, $translation->mail_body ?? $notification->mail_body),
                    'banner_img' => $notification->banner_img,
                    'logo_img' => $notification->logo_img,
                    'button_name' => $translation->button_name ?? $notification->button_name,
                    'show_button' => $notification->show_button,
                    'show_img' => $notification->show_img,
                    'show_fbicon' => $notification->show_fbicon,
                    'show_instaicon' => $notification->show_instaicon,
                    'show_twittericon' => $notification->show_twittericon,
                    'show_linkedinicon' => $notification->show_linkedinicon,
                    'button_url' => $notification->button_url,
                    'footer' => json_decode($notification->footer, true),            
                    'footer_content' =>$translation->footer_content ?? $notification->footer_content,
                    'footer_copyrights' =>$translation->footer_copyrights ?? $notification->footer_copyrights,

                ];
                    Mail::to($this->driver->email)->send(new DriverInvoiceMail($this->driver, $notificationData, $this->data, $this->logo, $this->invoice ));
                }

    }
}
