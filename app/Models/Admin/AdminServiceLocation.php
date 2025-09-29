<?php

namespace App\Models\Admin;

use App\Models\Admin\AdminDetail;
use App\Models\Traits\HasActive;
use App\Models\Admin\ServiceLocation;
use Illuminate\Database\Eloquent\Model;

class AdminServiceLocation extends Model
{
    use HasActive;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_service_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id', 'name','service_location_id'
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
        'service_location_name'
    ];



    public function serviceLocationDetail()
    {
        return $this->belongsTo(ServiceLocation::class, 'service_location_id', 'id');
    }

    /**
    * Get Service location's name
    *
    * @return string
    */
    public function getServiceLocationNameAttribute()
    {
        if (!$this->serviceLocationDetail()->exists()) {
            return null;
        }
        return $this->serviceLocationDetail->name;
    }

    protected $searchable = [
        'columns' => [
            'admin_service_locations.ame' => 20,
            'service_locations.name'=> 20,
        ],
        'joins' => [
            'service_locations' => ['admin_service_locations.service_location_id','service_locations.id'],
        ],
    ];
}
