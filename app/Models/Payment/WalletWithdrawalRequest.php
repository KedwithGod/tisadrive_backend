<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\Driver;
use App\Models\User;
use App\Models\Admin\Owner;

class WalletWithdrawalRequest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallet_withdrawal_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'driver_id', 'requested_amount', 'status','requested_currency','owner_id','payment_status'
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
        'converted_created_at','driver_name','owner_name',

    ];
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

    public function getDriverNameAttribute()
    {
        if ($this->driverDetail==null) {
            return null;
        }
        return Driver::where('id',$this->driver_id)->first()->name;
    }
    public function getOwnerNameAttribute()
    {
        if ($this->ownerDetail==null) {
            return null;
        }
        return Owner::where('id',$this->owner_id)->first()->name;
    }
    public function driverDetail()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id')->withTrashed();
    }

    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function ownerDetail()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id')->withTrashed();
    }

}
