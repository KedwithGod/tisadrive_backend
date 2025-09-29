<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;

class NotificationChannelTranslation extends Model
{
    use HasFactory, HasActive;
    protected $table = 'notification_channels_translations';

    protected $fillable = ['notification_channel_id','email_subject','mail_body','button_name','footer_content',
    'footer_copyrights' ,'push_title','push_body','locale'];

  
      public function language()
    {
        return $this->belongsTo(Language::class, 'code', 'locale');
    }
}
