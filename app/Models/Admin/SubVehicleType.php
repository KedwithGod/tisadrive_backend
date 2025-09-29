<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\VehicleType;
class SubVehicleType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sub_vehicle_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vehicle_type_id','sub_vehicle_type_id'];


    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'driver'
    ];

    /**
    * The driver that the uploaded data belongs to.
    * @tested
    *
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    */
    public function driverDetail()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id')->withTrashed();
    }
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type', 'id')->withTrashed();
    }
   
}
