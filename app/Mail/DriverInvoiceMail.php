<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Base\Constants\Setting\Settings;

class DriverInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificationData;
    public $driver;    
    public $data;
    public $logo;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @param $driver
     * @param $notificationData
     */
    public function __construct($driver, $notificationData, $data, $logo, $invoice)
    {
        $this->driver = $driver;
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
                    ->view('emails.driverInvoiceMessage',['app_name' => $app_name]) // Blade template for email
                    ->with([
                        'driver' => $this->driver,
                        'notificationData' => $this->notificationData,
                        'data' => $this->data,
                        'logo' => $this->logo,
                        'invoice' => $this->invoice,
                    ]);
    }
}
