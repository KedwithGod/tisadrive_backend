<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\ZoneTypePrice;
use App\Base\Uuid\UuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasActive;


class ZoneTypePackage extends Model
{
    use HasActive, UuidModel,SoftDeletes;

     protected $table = 'zone_type_package_prices';

      protected $fillable = [
        'zone_type_id','base_price','package_type_id','distance_price_per_km','time_price_per_min',
        'cancellation_fee','free_distance','free_min','zone_id'
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'zoneType','zoneType.zone'
    ];

    protected $appends = [
        'package_name'
    ];

    public function getPackageNameAttribute()
    {
        if (!$this->package()->exists()) {
            return null;
        }
        return $this->package->name;
    }

    public function package()
    {
        return $this->belongsTo(PackageType::class, 'package_type_id', 'id');
    }

    public function PackageName()
    {
    	return $this->hasOne(\App\Models\Master\PackageType::class, 'id','package_type_id');
    }
    /**
     * The zone type that belongs to.
     * @tested
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function zoneType()
    {
        return $this->belongsTo(ZoneType::class, 'zone_type_id', 'id');
    }

}
