<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;
use App\Models\Admin\SubscriptionDetail;
use App\Models\Admin\VehicleType;
use App\Models\Admin\Zone;

class Subscription extends Model
{
    use HasActive;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscription_duration','active','amount','vehicle_type_id','name','description',
    ];

    protected $appends = ['vehicle_type_name'];
    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'subscriptionDetail','vehicleType'
    ];

    public function subscriptionDetail(){
        return $this->hasMany(SubscriptionDetail::class,'subscription_id','id');
    }
    public function vehicleTypeDetail() {
        return $this->hasOne(VehicleType::class,'id','vehicle_type_id');
    }
    public function getVehicleTypeNameAttribute() {
        return $this->vehicleTypeDetail ? $this->vehicleTypeDetail->name : null;
    }
}
