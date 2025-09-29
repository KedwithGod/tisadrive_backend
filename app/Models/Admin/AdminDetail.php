<?php

namespace App\Models\Admin;

use App\Models\User;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use App\Models\Admin\ServiceLocation;
use App\Models\Admin\AdminServiceLocations;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Nicolaslopezj\Searchable\SearchableTrait;

class AdminDetail extends Model
{
    use HasActive, UuidModel,SearchableTrait,HasActiveCompanyKey;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'address', 'country','state',
        'city','pincode','email','mobile','user_id','created_by','service_location_id','active','category_type'
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [

    ];

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
        'profile_picture','service_location_name','role_name','user_status'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function serviceLocationDetail()
    {
        return $this->belongsTo(ServiceLocation::class, 'service_location_id', 'id')->withTrashed();
    }


    public function getUserNameAttribute()
    {
       if ($this->user()->exists()) {
            return $this->user->name;
        }
        return null;
    }
    public function getUserStatusAttribute()
    {
       if ($this->user()->exists()) {
            return $this->user->active;
        }
        return null;
    }
    public function getRoleNameAttribute()
    {
       if ($this->user()->exists()) 
       {
            return $this->user->roles[0]->name;
        }
        return null;
    }
    /**
    * Get Service location's name
    *
    * @return string
    */
    public function getServiceLocationNameAttribute()
    {
       if ($this->serviceLocationDetail()->exists()) {
            return $this->serviceLocationDetail->name;
        }
        return null;
    }
    /**
    * Get profile picture
    *
    * @return string
    */
    public function getProfilePictureAttribute()
    {
        if (!$this->user()->exists()) {
            return null;
        }
        return $this->user->profile_picture;
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
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedUpdatedAtAttribute()
    {
        if ($this->updated_at==null||!auth()->user()) {
            return null;
        }
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->updated_at)->setTimezone($timezone)->format('jS M h:i A');
    }

}
