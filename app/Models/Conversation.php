<?php

namespace App\Models;

use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Nicolaslopezj\Searchable\SearchableTrait;



class Conversation extends Model
{
    use UuidModel, HasActive, HasActiveCompanyKey, SearchableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conversations';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'admin_id', 'subject', 'is_closed'
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
    ];

    protected $appends = ['profile_picture','role'];


    public function messages()
    {
        return $this->hasMany(Message::class,'conversation_id','id');
    }

    public function userDetail(){
        return $this->belongsTo(User::class,'user_id','id')->withTrashed();
    } 

    public function getProfilePictureAttribute()
    {
        $user = $this->userDetail;

        $profile_picture = $user->profile_picture;

        return $profile_picture;
    }
    
    public function getRoleAttribute()
    {
        $user = $this->userDetail;

        if($user){
            return $user->role_name;
        }else{
            return null;
        }
    }

}
