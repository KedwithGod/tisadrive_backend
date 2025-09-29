<?php

namespace App\Models\Admin;

use Carbon\Carbon;
use App\Base\Uuid\UuidModel;
use App\Models\Admin\Driver;
use App\Models\Method;
use App\Models\Payment\BankInfo;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverBankInfo extends Model
{
    use HasActive, UuidModel;

    protected $keyType = 'string';
    
    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_bank_infos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'driver_id', 'method_id','field_id','value'
    ];

    protected $appends = [
    ];

    public $includes = [
        'driver'
    ];

    /**
     * Relationship: A document belongs to a driver.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id')->withTrashed();
    }



    /**
    * The Document that the bankInfo belongs to.
    * @tested
    *
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    */
    public function method()
    {
        return $this->belongsTo(Method::class, 'method_id', 'id');
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
        if(auth()->user()){
            $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        }else{
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }
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
       if(auth()->user()){
            $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        }else{
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }
        return Carbon::parse($this->updated_at)->setTimezone($timezone)->format('jS M h:i A');
    }
}
