<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificationData;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $notificationData
     */
    public function __construct($user, $notificationData)
    {
        $this->user = $user;
        $this->notificationData = $notificationData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->notificationData['email_subject'])
                    ->view('emails.usersWelcomeMessage') // Blade template for email
                    ->with([
                        'user' => $this->user,
                        'notificationData' => $this->notificationData,
                    ]);
    }
}
