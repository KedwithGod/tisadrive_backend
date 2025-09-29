<?php

namespace App\Models\Admin;

use Carbon\Carbon;
use App\Models\Traits\HasActive;
use App\Base\Services\OTP\CanSendOTP;
use App\Models\Traits\DeleteOldFiles;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\UserAccessScopeTrait;
use Illuminate\Database\Eloquent\Model;
use App\Base\Uuid\UuidModel;


class Incentive extends Model 
{
    use CanSendOTP,
    DeleteOldFiles,
    HasActive,
    Notifiable,
    UserAccessScopeTrait,
    UuidModel;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'incentives';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ride_count', 'amount','mode','zone_type_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that have files that should be auto deleted on updating or deleting.
     *
     * @var array
     */
    public $deletableFiles = [
    ];

    /**
     * The attributes that can be used for sorting with query string filtering.
     *
     * @var array
     */
    public $sortable = [
        
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

    ];

    public function zoneTypeDetail()
    {
        return $this->belongsTo(ZoneType::class, 'zone_type_id', 'id');
    } 
}
