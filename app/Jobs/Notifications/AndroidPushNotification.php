<?php

namespace App\Jobs\Notifications;

use Illuminate\Mail\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;


class AndroidPushNotification extends Notification implements ShouldQueue
{
    use Queueable,InteractsWithQueue,SerializesModels,Dispatchable;

     /**
     * The title of the notification.
     *
     * @var string
     */
    protected $title;

    /**
     * The body of the notification.
     *
     * @var string
     */
    protected $body;

    /**
     * The image URL of the notification.
     *
     * @var string|null
     */
    protected $image;

    /**
     * Additional data for the notification.
     *
     * @var array|null
     */
    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $body
     * @param array|null $data
     * @param string|null $image
     */
    public function __construct($title, $body, $data = null, $image = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data ?? []; // Default to an empty array if null
        $this->image = $image ?? null; // Default image if not provided
    }

    /**
     * Notification delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Build the FCM notification message.
     *
     * @param mixed $notifiable
     * @return FcmMessage
     */
    public function toFcm($notifiable): FcmMessage
    {
        $notification = new FcmNotification(
            title: $this->title,
            body: $this->body,
            image: $this->image
        );

        return (new FcmMessage(notification: $notification))
            ->data($this->data);
    }
}
