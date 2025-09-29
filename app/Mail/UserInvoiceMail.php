<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Base\Constants\Setting\Settings;

class UserInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificationData;
    public $user;
    public $data;
    public $logo;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $notificationData
     */
    public function __construct($user, $notificationData, $data, $logo, $invoice)
    {
        $this->user = $user;
        $this->notificationData = $notificationData;
        $this->data = $data;
        $this->logo = $logo;
        $this->invoice = $invoice;
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
                    ->view('emails.userInvoiceMessage',['app_name' => $app_name]) // Blade template for email
                    ->with([
                        'user' => $this->user,
                        'notificationData' => $this->notificationData,
                        'data' => $this->data,
                        'logo' => $this->logo,
                        'invoice' => $this->invoice,
                    ]);
    }
}
