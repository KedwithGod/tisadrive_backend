<?php

namespace App\Models\Payment;

use Carbon\Carbon;
use App\Models\Admin\Driver;
use App\Base\Uuid\UuidModel;
use Illuminate\Database\Eloquent\Model;

class DriverIncentiveHistory extends Model
{
    use UuidModel;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_incentive_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'driver_id','amount','mode','date','ride_count'
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [

    ];
    /**
     * The attributes that can be used for sorting with query string filtering.
     *
     * @var array
     */
    public $sortable = [
        'created_at',
    ];
    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [

    ];
    /**
    * The user wallet that the user_id belongs to.
    * @tested
    *
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    */
    public function driverDetail()
    {
        return $this->belongsTo(Driver::class, 'reffered_driver', 'id')->withTrashed();
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
