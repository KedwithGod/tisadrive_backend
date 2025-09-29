<?php

namespace App\Models\Admin;

use Carnon\Carbon;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class DriverLevelDetail extends Model
{
    use HasActive,UuidModel,SearchableTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_level_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'driver_id','level','level_id',
        'amount_rewarded','ride_rewarded',
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
     * The driver associated with the driver's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function driverDetail()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id')->withTrashed();
    }
    /**
     * The driver associated with the level id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function levelDetail()
    {
        return $this->hasOne(DriverLevelUp::class, 'id', 'level_id');
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

}
