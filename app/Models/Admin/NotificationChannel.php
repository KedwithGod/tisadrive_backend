<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use App\Models\Admin\ServiceLocation;
use App\Models\Traits\HasActiveCompanyKey;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin\NotificationChannelTranslation;

class NotificationChannel extends Model
{
    use HasFactory,UuidModel,HasActive,HasActiveCompanyKey;
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_channels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
        'topics',
        'topics_content',
        'push_notification',
        'mail',
        'sms',
        'email_subject' ,
        'logo_img',
        'mail_body',
        'button_name',
        'button_url',
        'show_button',
        'banner_img',
        'show_img',
        'footer',
        'footer_content',
        'show_fbicon',
        'show_instaicon',
        'show_twittericon',
        'show_linkedinicon',
        'footer_copyrights'  ,
        'push_title',
        'push_body',
    ];


    public function notificationChannelTranslationWords(){
        return $this->hasMany(NotificationChannelTranslation::class, 'notification_channel_id', 'id');
    }
}
