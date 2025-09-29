<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Driver;
use Carbon\Carbon;

class DriverRejectedRequest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_rejected_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['request_id','driver_id','is_after_accept','reason','custom_reason'];

    protected $appends = [ 'converted_created_at_date'];
    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [

    ];

    public function drivers()
    {
        return $this->belongsTo(Driver::class,  'driver_id', 'id')->withTrashed();
    }

    public function getConvertedCreatedAtDateAttribute()
    {
        if ($this->created_at == null) {
            return null;
        }
        $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M h:i A');
    }


}
