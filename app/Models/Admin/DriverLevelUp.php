<?php

namespace App\Models\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Base\Uuid\UuidModel;
use App\Models\Master\CarMake;
use App\Models\Master\CarModel;
use App\Models\Request\Request;
use App\Models\Traits\HasActive;
use App\Models\Payment\DriverWallet;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\DriverAvailability;
use App\Models\Payment\DriverWalletHistory;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Models\Payment\WalletWithdrawalRequest;
use App\Models\Payment\DriverSubscription;
use App\Models\Request\DriverRejectedRequest;
use App\Models\Traits\HasActiveCompanyKey;
use App\Models\Admin\SubscriptionDetail;
use App\Models\Admin\ServiceLocation;
use Illuminate\Support\Facades\Storage;

class DriverLevelUp extends Model
{
    use HasActive,UuidModel,SearchableTrait,HasActiveCompanyKey;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_level_ups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','level','reward_type',
        'reward','is_min_ride_complete','min_ride_count',
        'ride_points','is_min_ride_amount_complete','min_ride_amount',
        'amount_points','image','zone_type_id',
        ];
    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
    ];

 
    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
    ];


    /**
     * The driver associated with the user's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function driverDetail()
    {
        return $this->hasOne(DriverDetail::class, 'driver_id', 'id');
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
     * Get the Profile image full file path.
     *
     * @param string $value
     * @return string
     */
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // return asset(file_path('storage/'.$this->uploadPath(), $value));

        return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
    }
    /**
     * The default file upload path.
     *
     * @return string|null
     */
    public function uploadPath()
    {
        return config('base.driver.upload.levels.path');
    }

    public function zoneTypeDetail()
    {
        return $this->belongsTo(ZoneType::class, 'zone_type_id', 'id');
    } 
    
}
