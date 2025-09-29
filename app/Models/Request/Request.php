<?php

namespace App\Models\Request;

use Carbon\Carbon;
use App\Models\User;
use App\Base\Uuid\UuidModel;
use App\Models\Admin\Driver;
use App\Models\Admin\ServiceLocation;
use App\Models\Admin\ZoneType;
use App\Models\Admin\UserDetails;
use App\Models\Request\AdHocUser;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Models\Admin\CancellationReason;
use App\Models\Master\PackageType;
use App\Models\Admin\Owner;
use App\Models\Admin\Fleet;
use App\Models\Admin\GoodsType;
use App\Models\Payment\RewardHistory;
use App\Models\Payment\RewardPoint;
use App\Models\Admin\Promo;
use App\Models\Request\RequestDriver;
use App\Models\CartItems; 
use App\Models\FoodDelivery\Stores;
use App\Models\Admin\ZoneTypePackage;

class Request extends Model
{
    use UuidModel,SearchableTrait,HasActiveCompanyKey;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['request_number','is_later','user_id','driver_id','trip_start_time','arrived_at','accepted_at','completed_at','cancelled_at','is_driver_started','is_driver_arrived',
    'is_trip_start','is_completed','is_cancelled','reason','cancel_method','total_distance', 'is_pet_available','is_luggage_available',
    'total_time','payment_opt','is_paid','user_rated','driver_rated','promo_id','timezone','unit','is_without_destination',
    'if_dispatch','zone_type_id','requested_currency_code','custom_reason','attempt_for_schedule','web_booking',
    'service_location_id','company_key','dispatcher_id','book_for_other_contact','book_for_other','additional_charges_reason','additional_charges_amount',
    'ride_otp','is_rental','rental_package_id','is_out_station','is_round_trip','return_time','request_eta_amount','is_surge_applied',
    'owner_id','fleet_id','transport_type','goods_type_id','goods_type_quantity','requested_currency_symbol','payment_intent_id',
    'is_redeem','offerred_ride_fare','accepted_ride_fare','is_bid_ride','is_multiple_vehicles','no_of_vehicles','is_trip_meter',
    'is_my_rider','order_id','store_id','poly_line','assign_method','is_manual','is_airport','is_parcel','paid_at','booked_by','instant_ride','card_token','discounted_total','parcel_type'];

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
        'user_profile','vehicle_type_name','pick_lat','pick_lng','drop_lat','drop_lng','pick_address','drop_address',
        'converted_trip_start_time','converted_arrived_at','converted_accepted_at','converted_completed_at',
        'converted_cancelled_at','converted_created_at','converted_updated_at','converted_return_time','vehicle_type_image','vehicle_type_id',
        'user_name','driver_name','ride_fare','user_rating','driver_rating','converted_trip_start_time_date',
        'trip_status','trip_payment','payment_option'
    ];
    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'driverDetail','userDetail','requestBill'
    ];

    public $sortable = ['trip_start_time', 'created_at', 'updated_at'];

    public function getPaymentOptionAttribute()
    {
        if ($this->payment_opt==0) {
            return 'Online';
        } elseif ($this->payment_opt==1) {
            return 'Cash';
        } else {
            return 'Wallet';
        }
    }
    public function getTripStatusAttribute()
    {
        if ($this->is_completed) {
            return 'Completed';
        } elseif ($this->is_cancelled) {
            return 'Cancelled';
        } else {
            return 'On Trip';
        }
    }
    public function getTripPaymentAttribute()
    {
        if ($this->is_paid) {
            return 'Paid';
        } else {
            return 'Not Paid';
        }
    }
    /**
     * The Request place associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestPlace()
    {
        return $this->hasOne(RequestPlace::class, 'request_id', 'id');
    } 
  
    public function requestRating()
    {
        return $this->hasMany(RequestRating::class, 'request_id','id');
    }
    public function requestRewardPoint()
    {
        return $this->hasMany(RewardPoint::class, 'user_id','id');
    }
    /**
     * The Request Adhoc user associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function adHocuserDetail()
    {
        return $this->hasOne(AdHocUser::class, 'request_id', 'id');
    }
    /**
     * The Request Bill associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestBill()
    {
        return $this->hasOne(RequestBill::class, 'request_id', 'id');
    }
    /**
     * The Request Bill associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestMultiBillDetail()
    {
        return $this->hasMany(RequestBill::class, 'request_id', 'id');
    }
    /**
     * The Request Bill associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestEtaDetail()
    {
        return $this->hasOne(RequestEta::class, 'request_id', 'id');
    }
    
    /**
     * The Request Bill associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestBillDetail()
    {
        return $this->hasOne(RequestBill::class, 'request_id', 'id');
    }
    /**
     * The Request meta associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestMeta()
    {
        return $this->hasMany(RequestMeta::class, 'request_id', 'id');
    }
    public function requestDriverDetail()
    {
        return $this->hasMany(RequestDriver::class, 'request_id', 'id');
    }
    public function rentalPackage()
    {
        return $this->belongsTo(PackageType::class, 'rental_package_id', 'id');
    }

    public function driverDetail()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id')->withTrashed();
    }
    public function ownerDetail()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id')->withTrashed();
    }
    public function fleetDetail()
    {
        return $this->belongsTo(Fleet::class, 'fleet_id', 'id')->withTrashed();
    }
    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
   public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id', 'id');
    }   
    public function zoneType()
    {
        return $this->belongsTo(ZoneType::class, 'zone_type_id', 'id')->withTrashed();
    }

    /**
     * The Request place associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestCancellationFee()
    {
        return $this->hasOne(RequestCancellationFee::class, 'request_id', 'id');
    }

    public function zoneTypePackage()
    {
        return $this->belongsTo(ZoneTypePackage::class, 'zone_type_id', 'id');
    }

    public function getUserRatingAttribute()
    {
        if (!$this->requestRating()->where('user_rating',true)->exists()) {
            return null;
        }
        $user_rating = $this->requestRating()->where('user_rating',true)->first();

        return $user_rating ? $user_rating->rating : 0;
    }
    public function getDriverRatingAttribute()
    {
        if (!$this->requestRating()->where('driver_rating',true)->exists()) {
            return null;
        }
        $user_rating = $this->requestRating()->where('driver_rating',true)->first();
        return $user_rating->rating;
    }
    public function getUserProfileAttribute()
    {
        if (!$this->userDetail()->exists()) {
            return null;
        }
        return $this->userDetail->profile_picture;
    }

    /**
    * Get request's pickup latitude.
    *
    * @return string
    */
    public function getPickLatAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->pick_lat;
    }
    /**
    * Get request's pickup longitude.
    *
    * @return string
    */
    public function getPickLngAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->pick_lng;
    }
    /**
    * Get request's drop latitude.
    *
    * @return string
    */
    public function getDropLatAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->drop_lat;
    }

    /**
    * Get request's drop longitude.
    *
    * @return string
    */
    public function getDropLngAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->drop_lng;
    }
    /**
    * Get request's pickup address.
    *
    * @return string
    */
    public function getPickAddressAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->pick_address;
    }
    /**
    * Get request's drop address.
    *
    * @return string
    */
    public function getDropAddressAttribute()
    {
        if (!$this->requestPlace()->exists()) {
            return null;
        }
        return $this->requestPlace->drop_address;
    }
    /**
    * Get vehicle type's name.
    *
    * @return string
    */
    public function getVehicleTypeNameAttribute()
    {
        if ($this->zoneType == null) {
            return null;
        }
        if (!$this->zoneType->vehicleType()->exists()) {
            return null;
        }
        return $this->zoneType->vehicleType->name;
    }

    /**
    * Get vehicle type's name.
    *
    * @return string
    */
    public function getVehicleTypeIdAttribute()
    {
        if ($this->zoneType == null) {
            return null;
        }
        if (!$this->zoneType->vehicleType()->exists()) {
            return null;
        }
        return $this->zoneType->vehicleType->id;
    }
        /**
         * Get formatted and converted timezone of user's Trip start time in "dd/mm/yyyy" format.
         * @return string
         */
        public function getConvertedTripStartTimeDateAttribute()
        {
            if ($this->trip_start_time == null) {
                return null;
            }
            $timezone = $this->serviceLocationDetail->timezone ?: env('SYSTEM_DEFAULT_TIMEZONE');
            return Carbon::parse($this->trip_start_time)->setTimezone($timezone)->format('d/m/Y');
        }

        /**
         * Get formatted and converted timezone of user's arrived at in "dd/mm/yyyy" format.
         * @return string
         */
        public function getConvertedArrivedAtDateAttribute()
        {
            if ($this->arrived_at == null) {
                return null;
            }
            $timezone = $this->serviceLocationDetail->timezone ?: env('SYSTEM_DEFAULT_TIMEZONE');
            return Carbon::parse($this->arrived_at)->setTimezone($timezone)->format('d/m/Y');
        }

        /**
         * Get formatted and converted timezone of user's accepted at in "dd/mm/yyyy" format.
         * @return string
         */
        public function getConvertedAcceptedAtDateAttribute()
        {
            if ($this->accepted_at == null) {
                return null;
            }
            $timezone = $this->serviceLocationDetail->timezone ?: env('SYSTEM_DEFAULT_TIMEZONE');
            return Carbon::parse($this->accepted_at)->setTimezone($timezone)->format('d/m/Y');
        }

        /**
         * Get formatted and converted timezone of user's completed_at at in "dd/mm/yyyy" format.
         * @return string
         */
        public function getConvertedCompletedAtDateAttribute()
        {
            if ($this->completed_at == null) {
                return null;
            }
            $timezone = $this->serviceLocationDetail->timezone ?: env('SYSTEM_DEFAULT_TIMEZONE');
            return Carbon::parse($this->completed_at)->setTimezone($timezone)->format('d/m/Y');
        }

        /**
         * Get formatted and converted timezone of user's cancelled at in "dd/mm/yyyy" format.
         * @return string
         */
        public function getConvertedCancelledAtDateAttribute()
        {
            if ($this->cancelled_at == null) {
                return null;
            }
            $timezone = $this->serviceLocationDetail->timezone ?: env('SYSTEM_DEFAULT_TIMEZONE');
            return Carbon::parse($this->cancelled_at)->setTimezone($timezone)->format('d/m/Y');
        }

        /**
         * Get formatted and converted timezone of user's created at in "dd/mm/yyyy" format.
         * @return string
         */
        public function getConvertedCreatedAtDateAttribute()
        {
            if ($this->created_at == null) {
                return null;
            }
            $timezone = $this->serviceLocationDetail->timezone ?: env('SYSTEM_DEFAULT_TIMEZONE');
            return Carbon::parse($this->created_at)->setTimezone($timezone)->format('d/m/Y');
        }
    
     /**
    * Get vehicle type's name.
    *
    * @return string
    */
    public function getVehicleTypeImageAttribute()
    {
        if ($this->zoneType == null) {
            return null;
        }
        if (!$this->zoneType->vehicleType()->exists()) {
            return null;
        }
        return $this->zoneType->vehicleType->icon;
    }

    /**
    * Get formated and converted timezone of user's Trip start time.
    * @return string
    */
    public function getConvertedReturnTimeAttribute()
    {
        if ($this->return_time==null) {
            return null;
        }
        $timezone = $this->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->return_time)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's Trip start time.
    * @return string
    */
    public function getConvertedTripStartTimeAttribute()
    {
        if ($this->trip_start_time==null) {
            return null;
        }
        $timezone = $this->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->trip_start_time)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's arrived at.
    * @return string
    */
    public function getConvertedArrivedAtAttribute()
    {
        if ($this->arrived_at==null) {
            return null;
        }
        $timezone = $this->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->arrived_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's accepted at.
    * @return string
    */
    public function getConvertedAcceptedAtAttribute()
    {
        if ($this->accepted_at==null) {
            return null;
        }
        $timezone = $this->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->accepted_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's completed_at at.
    * @return string
    */
    public function getConvertedCompletedAtAttribute()
    {
        if ($this->completed_at==null) {
            return null;
        }
        $timezone = $this->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->completed_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's cancelled at.
    * @return string
    */
    public function getConvertedCancelledAtAttribute()
    {
        if ($this->cancelled_at==null) {
            return null;
        }
        $timezone = $this->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->cancelled_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's created at.
    * @return string
    */
    public function getConvertedCreatedAtAttribute()
    {
        if ($this->created_at==null) {
            return null;
        }
        $timezone = $this->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's created at.
    * @return string
    */
    public function getConvertedUpdatedAtAttribute()
    {
        if ($this->updated_at==null) {
            return null;
        }
        $timezone = $this->serviceLocationDetail->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->updated_at)->setTimezone($timezone)->format('jS M h:i A');
    }

    public function getRequestUnitAttribute()
    {
        if ($this->unit == '1') {
            return 'Km';
        } else {
            return 'Miles';
        }
    }

    public function getCurrencyAttribute()
    {
 
        return get_settings('currency_symbol');
    }

    protected $searchable = [
        'columns' => [
            'requests.request_number' => 20,
            'users.name' => 20,
            'drivers.name' => 20,
            'users.mobile' => 20,
            'drivers.mobile' => 20,
            'promo.code' => 20,
        ],
        'joins' => [
            'users' => ['requests.user_id','users.id'],
            'drivers' => ['requests.driver_id','drivers.id'],
            'promo' => ['requests.promo_id','promo.id'],

        ],
    ];

     /**
    * The Request Chat associated with the request's id.
    *
    * @return \Illuminate\Database\Eloquent\Relations\hasMany
    */
    public function requestChat()
    {
        return $this->hasMany(Chat::class, 'request_id', 'id');
    }

    public function serviceLocationDetail(){
        return $this->belongsTo(ServiceLocation::class,'service_location_id','id');
    }

    public function cancelReason()
    {
         return $this->hasOne(CancellationReason::class, 'id', 'reason');
       
    }
    public function goodsTypeDetail(){
        return $this->belongsTo(GoodsType::class,'goods_type_id','id');
    }

    /**
     * The Request Stops associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestStops()
    {
        return $this->hasMany(RequestStop::class, 'request_id', 'id');
    }

    /**
     * The Request proof associated with the request's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function requestProofs()
    {
        return $this->hasMany(RequestDeliveryProof::class, 'request_id', 'id');
    }
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'request_drivers', 'request_id', 'driver_id');
    }

    public function driverRejectedRequestDetail()
    {
        return $this->hasMany(DriverRejectedRequest::class, 'request_id', 'id')->orderBy('created_at');
    }

    public function requestMultipleDriverDetail()
    {
        return $this->requestDriverDetail()->where('driver_id',auth()->user()->driver->id)->first();
    }  
    public function orderDetail()
    {
        return $this->belongsTo(CartItems::class, 'order_id', 'id');
    }
    public function storeDetail()
    {
        return $this->belongsTo(Stores::class, 'store_id', 'id');
    }
    public function getUserNameAttribute()
    {
        return $this->userDetail ? $this->userDetail->name : ($this->adHocuserDetail ? $this->adHocuserDetail->name : null);
    }
    public function getDriverNameAttribute()
    {
        return $this->driverDetail ? $this->driverDetail->name : null;
    }
    public function getRideFareAttribute()
    {
        return $this->requestBill ? $this->requestBill->total_amount : null;
    }
}
