<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FleetDriverApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificationData;
    public $fleet;

    /**
     * Create a new message instance.
     *
     * @param $fleet
     * @param $notificationData
     */
    public function __construct($fleet, $notificationData)
    {
        $this->fleet = $fleet;
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
                    ->view('emails.driverApprovedMessage',['app_name' => $app_name]) // Blade template for email
                    ->with([
                        'fleet' => $this->fleet,
                        'notificationData' => $this->notificationData,
                    ]);
    }
}
