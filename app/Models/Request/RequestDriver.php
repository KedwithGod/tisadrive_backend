<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Driver;
use Carbon\Carbon;

class RequestDriver extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_drivers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['request_id','driver_id','is_cancelled','offerred_ride_fare','accepted_ride_fare','trip_start_time','arrived_at','accepted_at','completed_at','cancelled_at','is_driver_started','is_driver_arrived','is_trip_start','is_completed','drop_lat','drop_lng','drop_address','is_paid','total_distance','total_time','user_rated','driver_rated','payment_confirmed_by_driver','is_later','reason','custom_reason','cancel_method'
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'driverDetail','requestDetail'
    ];

    public $sortable = ['trip_start_time', 'created_at', 'updated_at'];


    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
        'pick_lat','pick_lng','drop_lat','drop_lng','pick_address','drop_address','converted_trip_start_time','converted_arrived_at','converted_accepted_at','converted_completed_at','converted_cancelled_at','converted_created_at','converted_updated_at'
    ];

    public function requestDetail()
    {
        return $this->belongsTo(Request::class, 'request_id', 'id');
    }
    public function driverDetail()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id')->withTrashed();
    }
    public function requestPlace()
    {
        return $this->hasOne(RequestPlace::class, 'request_id', 'id');
    } 
    /**
    * Get formated and converted timezone of user's Trip start time.
    * @return string
    */
    public function getConvertedTripStartTimeAttribute()
    {
        if ($this->trip_start_time==null) {
            return null;
        }
        $timezone = $this->requestDetail->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->trip_start_time)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's arrived at.
    * @return string
    */
    public function getConvertedArrivedAtAttribute()
    {
        if ($this->arrived_at==null) {
            return null;
        }
        $timezone = $this->requestDetail->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->arrived_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's accepted at.
    * @return string
    */
    public function getConvertedAcceptedAtAttribute()
    {
        if ($this->accepted_at==null) {
            
            return null;
        }
        $timezone = $this->requestDetail->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->accepted_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's completed_at at.
    * @return string
    */
    public function getConvertedCompletedAtAttribute()
    {
        if ($this->completed_at==null) {
            return null;
        }
        $timezone = $this->requestDetail->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->completed_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's cancelled at.
    * @return string
    */
    public function getConvertedCancelledAtAttribute()
    {
        if ($this->cancelled_at==null) {
            return null;
        }
        $timezone = $this->requestDetail->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->cancelled_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's created at.
    * @return string
    */
    public function getConvertedCreatedAtAttribute()
    {
        if ($this->created_at==null) {
            return null;
        }
        $timezone = $this->requestDetail->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's created at.
    * @return string
    */
    public function getConvertedUpdatedAtAttribute()
    {
        if ($this->updated_at==null) {
            return null;
        }
        $timezone = $this->requestDetail->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->updated_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get request's pickup latitude.
    *
    * @return string
    */
    public function getPickLatAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->pick_lat;
    }
    /**
    * Get request's pickup longitude.
    *
    * @return string
    */
    public function getPickLngAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->pick_lng;
    }
    /**
    * Get request's drop latitude.
    *
    * @return string
    */
    public function getDropLatAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->drop_lat;
    }
    /**
    * Get request's drop longitude.
    *
    * @return string
    */
    public function getDropLngAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->drop_lng;
    }
    /**
    * Get request's pickup address.
    *
    * @return string
    */
    public function getPickAddressAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->pick_address;
    }
    /**
    * Get request's drop address.
    *
    * @return string
    */
    public function getDropAddressAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->drop_address;
    }


}
