<?php

namespace App\Models;

use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class Message extends Model
{
    use UuidModel, HasActive, HasActiveCompanyKey, SearchableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'messages';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conversation_id', 'sender_id', 'sender_type', 'content','unseen_count'
    ];
   
    protected $appends = [ 'converted_created_at'];


    public function conversation()
    {
        return $this->belongsTo(Conversation::class,'conversation_id','id');
    }

    public function userDetail(){
        return $this->belongsTo(User::class, 'sender_id', 'id')->withTrashed();
    } 

    public function getProfilePictureAttribute()
    {
        $profile_picture = $this->userDetail ? $this->userDetail->profile_picture : null;

        return $profile_picture;
    }
    /**
     * Get formated and converted timezone of user's created at.
     *
     * @param string $value
     * @return string
     */
    public function getConvertedCreatedAtAttribute()
    {
        if ($this->created_at==null||!auth()->user()) {
            return null;
        }
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('d M Y h:i A');
    }

}
