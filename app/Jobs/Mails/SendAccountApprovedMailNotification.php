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
use App\Mail\DriverApprovedMail;

class SendAccountApprovedMailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $driver;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Fetch the notification channel

        $notification = \DB::table('notification_channels')
        ->where('topics', 'Driver Account Approval') // Match the correct topic
        ->first();

        // if(!empty($user?->email)){
            if ($notification && $notification->mail == 1) {

                // Determine the user's language or default to 'en'
               $userLang = $this->driver->user->lang ?? 'en';

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
               'button_name' => $notification->button_name,
               'show_button' => $translation->button_name ?? $notification->button_name,
               'show_img' => $notification->show_img,
               'show_fbicon' => $notification->show_fbicon,
               'show_instaicon' => $notification->show_instaicon,
               'show_twittericon' => $notification->show_twittericon,
               'show_linkedinicon' => $notification->show_linkedinicon,
               'button_url' => $notification->button_url,
               'footer' => json_decode($notification->footer, true),            
              'footer_content' => $translation->footer_content ?? $notification->footer_content,
              'footer_copyrights' => $translation->footer_copyrights ?? $notification->footer_copyrights,
           ];
       
               // Send welcome email
               Mail::to($this->driver->email)->send(new DriverApprovedMail($this->driver, $notificationData));
           }
        // }

    }
}
