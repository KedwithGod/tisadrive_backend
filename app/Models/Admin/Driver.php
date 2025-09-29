<?php

namespace App\Models\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Master\CarMake;
use App\Models\Master\CarModel;
use App\Models\Request\Request;
use App\Models\Traits\HasActive;
use App\Models\Payment\RewardPoint;
use App\Models\Payment\DriverWallet;
use App\Models\Payment\RewardHistory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\DriverAvailability;
use App\Models\Payment\DriverWalletHistory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Models\Payment\WalletWithdrawalRequest;
use App\Models\Payment\DriverSubscription;
use App\Models\Request\DriverRejectedRequest;
use App\Models\Traits\HasActiveCompanyKey;
use App\Models\Admin\SubscriptionDetail;
use App\Models\Admin\ServiceLocation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;

class Driver extends Model
{
    use HasActive,SoftDeletes,SearchableTrait,HasActiveCompanyKey,Notifiable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'drivers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','owner_id','service_location_id', 'name','mobile',
        'email','address','state','city','country','postal_code','gender',
        'vehicle_type','car_make','car_model','car_color','car_number',
        'today_trip_count','total_accept','total_reject','acceptance_ratio',
        'last_trip_date','active','approve','available','reason','uuid','fleet_id',
        'vehicle_year','transport_type','custom_make','custom_model','my_route_lat',
        'my_route_lng','my_route_address','is_subscribed','is_on_commission','subscription_detail_id',
        'price_per_distance','driver_level_up_id'
        ];
    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
       'service_location_name', 'profile_picture','vehicle_type_name','car_make_name','car_model_name','rating','no_of_ratings','timezone','vehicle_type_image' , 'vehicle_type_icon_for','converted_created_at','mobile_number'
    ];

 
    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'driverDetail','requestDetail'
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'drivers.name' => 20,
            'drivers.email' => 20,
            'drivers.mobile' => 20,
        ],

    ];

    /**
    * Get the Profile image full file path.
    *
    * @param string $value
    * @return string
    */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    } 
    public function routeNotificationForFcm()
    {
        return $this->user->fcm_token;
    }

    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class, 'service_location_id', 'id')->withTrashed();
    }     
    public function getProfilePictureAttribute($value)
    {
        if ($this->user && $this->user->profile_picture) {
            $profilePictureUrl = $this->user->profile_picture;           
            // Replace 'uploads/user' with 'uploads/driver'
            $driverProfilePictureUrl = $profilePictureUrl;
            return $driverProfilePictureUrl;
        }
        return null;
    }
      
    public function getServiceLocationNameAttribute()
    {
        if($this->serviceLocation()->exists()){
            return $this->serviceLocation?$this->serviceLocation->name:null;            
        }else{

            return null;
        }
    }
    public function getTimezoneAttribute()
    {
        return $this->user?$this->user->timezone:env('SYSTEM_DEFAULT_TIMEZONE');
    }

    public function getVehicleTypeNameAttribute()
    {
        if ($this->vehicleType()->exists()){
            return $this->vehicleType->name;
        }elseif ($this->driverVehicleTypeDetail()->where('signed_vehicle',true)->exists()) {
            return $this->driverVehicleTypeDetail()->where('signed_vehicle',true)->first()->vehicleType->name;
        }else{
            return null;
        }
    }
    public function getVehicleTypeImageAttribute()
    {
        if ($this->vehicleType()->exists()){
            return $this->vehicleType->icon;
        }elseif ($this->driverVehicleTypeDetail()->exists()) {
            return $this->driverVehicleTypeDetail()->where('signed_vehicle',true)->first()->vehicleType->icon;
        }else{
            return null;
        }
    }
     public function getVehicleTypeIconForAttribute()
    {
        return $this->vehicleType?$this->vehicleType->icon_types_for:'taxi';
    }
    public function getCarMakeNameAttribute()
    {
        if($this->carMake()->exists()){
            return $this->carMake?$this->carMake->name:null;            
        }else{

            return $this->custom_make;
        }
    }
    public function getCarModelNameAttribute()
    {
        if($this->carModel()->exists()){
            return $this->carModel?$this->carModel->name:null;
        }else{
            return $this->custom_model;
        }
    }
    public function getRatingAttribute()
    {
        return $this->user?$this->user->rating:null;
    }
    public function getNoOfRatingsAttribute()
    {
        return $this->user?$this->user->no_of_ratings:null;
    }
    public function requestDetail()
    {
        return $this->hasMany(Request::class, 'driver_id', 'id');
    }
    public function rejectedRequestDetail()
    {
        return $this->hasMany(DriverRejectedRequest::class, 'driver_id', 'id');
    }
    public function subscriptions()
    {
        return $this->hasMany(DriverSubscription::class, 'driver_id', 'id');
    }
    public function bankInfoDetail()
    {
        return $this->hasMany(DriverBankInfo::class, 'driver_id', 'id');
    }
    public function currentRide(){

        return $this->requestDetail()->where('is_completed',false)->where('is_cancelled',false)->exists();
        
    }
    public function driverAvailabilities()
    {
        return $this->hasMany(DriverAvailability::class, 'driver_id', 'id');
    }

    /**
     * The driver that the user_id belongs to.
     * @tested
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */

   public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id')->withTrashed();
    }

    public function carMake()
    {
        return $this->belongsTo(CarMake::class, 'car_make', 'id');
    }

    public function fleetDetail()
    {
        return $this->belongsTo(Fleet::class, 'fleet_id', 'id');
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'car_model', 'id');
    }

    /**
     * The driver associated with the user's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function driverDetail()
    {
        return $this->hasOne(DriverDetail::class, 'driver_id', 'id');
    }

    /**
     * The driver document associated with the user's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function driverDocument()
    {
        return $this->hasMany(DriverDocument::class, 'driver_id', 'id');
    }
    /**
    * The driver wallet history associated with the driver's id.
    *
    * @return \Illuminate\Database\Eloquent\Relations\hasOne
    */
    public function driverWalletHistory()
    {
        return $this->hasMany(DriverWalletHistory::class, 'user_id', 'id');
    }

    public function driverWallet()
    {
        return $this->hasOne(DriverWallet::class, 'user_id', 'id');
    }
    public function driverPaymentWalletHistory()
    {
        return $this->hasMany(DriverWalletHistory::class, 'driver_id', 'id');
    }

    public function driverVehicleTypeDetail()
    {
        return $this->hasMany(DriverVehicleType::class, 'driver_id', 'id');
    }
    public function withdrawalRequestsHistory()
    {
        return $this->hasMany(WalletWithdrawalRequest::class, 'driver_id', 'id');
    }

    public function driverPaymentWallet()
    {
        return $this->hasOne(DriverWallet::class, 'driver_id', 'id');
    }
    public function vehicleType()
    {
        return $this->hasOne(VehicleType::class, 'id', 'vehicle_type')->withTrashed();
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

     public function privilegedVehicle()
    {
        return $this->hasMany(DriverPrivilegedVehicle::class, 'driver_id', 'id');
    }

    public function rating($user_id)
    {
        $rate=  User::where('id',$user_id)->first();
        return $rate->rating;
    }
    public function requests()
    {
       return $this->belongsToMany(Request::class, 'request_drivers','driver_id','request_id');
    } 

    public function enabledRoutes()
    {
        return $this->hasMany(DriverEnabledRoutes::class, 'driver_id', 'id');
    }
    public function subscriptionDetail()
    {
        return $this->hasMany(SubscriptionDetail::class, 'driver_id', 'id');
    }

    public function levelHistory()
    {
        return $this->hasMany(DriverLevelDetail::class, 'driver_id', 'id');
    }
    public function levelDetail()
    {
        return $this->hasOne(DriverLevelDetail::class, 'id', 'driver_level_up_id');
    }
    public function loyaltyPoint()
    {
        return $this->hasOne(RewardPoint::class, 'user_id', 'user_id');
    }
    public function loyaltyHistory()
    {
        return $this->hasMany(RewardHistory::class, 'user_id', 'user_id');
    }
    
    public function getMobileNumberAttribute() {
        return $this->user ? $this->user->mobile_number: $this->mobile;
    }
}
