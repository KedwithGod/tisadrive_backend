<?php

namespace App\Models\Admin;

use Carbon\Carbon;
use App\Base\Uuid\UuidModel;
use App\Models\Admin\Owner;
use App\Models\Payment\BankInfo;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class OwnerBankInfo extends Model
{
    use HasActive, UuidModel;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'owner_bank_infos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_id', 'method_id','field_id','value'

    ];

    protected $appends = [
    ];

    public $includes = [
        'owner'
    ];

    /**
     * Relationship: A document belongs to a driver.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id')->withTrashed();
    }


    /**
    * Get the is_identify_number_exists.
    *
    * @param string $value
    * @return string
    */
    public function getIdentifyNumberKeyAttribute()
    {
        if (!$this->bankInfo()->exists()) {
            return null;
        }
        return $this->bankInfo->branch_identification_code;
    }
    /**
    * The Document that the bankInfo belongs to.
    * @tested
    *
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    */
    public function bankInfo()
    {
        return $this->belongsTo(BankInfo::class, 'bank_info_id', 'id');
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

    public function getExpiryDateAttribute($value)
    {
        if ($value==null) {
            return null;
        }
        // if(auth()->user()){
        //     $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        // }else{
        //     $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        // }

            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');

        return Carbon::parse($value)->setTimezone($timezone)->format('Y-m-d');
    }
}
