<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificationData;
    public $auth_user;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $notificationData
     */
    public function __construct($auth_user, $notificationData)
    {
        $this->auth_user = $auth_user;
        $this->notificationData = $notificationData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $app_name = get_settings('app_name');
        return $this->subject($this->notificationData['email_subject'])
                    ->view('emails.referralMessage',['app_name' => $app_name]) // Blade template for email
                    ->with([
                        'auth_user' => $this->auth_user,
                        'notificationData' => $this->notificationData,
                    ]);
    }
}
