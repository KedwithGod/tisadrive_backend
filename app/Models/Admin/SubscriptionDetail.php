<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;
use App\Base\Uuid\UuidModel;
use App\Models\Admin\Subscription;
use App\Models\Admin\Driver;
use Carbon\Carbon;

class SubscriptionDetail extends Model
{
    use HasActive, UuidModel;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscription_id','driver_id','transaction_id','amount','payment_opt','subscription_type','expired_at','active','amount',
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'subscription','driverDetail'
    ];

    public function subscription(){
        return $this->belongsTo(Subscription::class,'subscription_id','id');
    }
    public function driverDetail(){
        return $this->belongsTo(Driver::class,'driver_id','id')->withTrashed();
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
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M');
    }
    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedExpiredAtAttribute()
    {
        if ($this->expired_at==null||!auth()->user()) {
            return null;
        }
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->expired_at)->setTimezone($timezone)->format('jS M');
    }

}
