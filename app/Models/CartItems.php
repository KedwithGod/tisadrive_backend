<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Models\FoodDelivery\Stores;
use App\Models\Request\Request;
use App\Models\StoreRating;
use App\Models\FoodDelivery\UserRefunds; 

class CartItems extends Model
{
   use UuidModel,HasActive,SearchableTrait;

   protected $table = 'cart_items';

   protected $fillable = ['user_id','store_id','total_quantity','total_price','service_tax','discount_type','discount','package_price','convenience_fee','convenience_fee_type','cancellation_fee','is_booking_done','status','is_paid', 'base_price','delivery_fee','is_payout','otp','order_no','store_price']; 

   protected $append = ['converted_created_at'];

       /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'cartItemDetails','userDetail','storeRatingDetail'
    ];
    protected $searchable = [
        'columns' => [
            'cart_items.order_no' => 20,
            'users.name' => 20,
            'users.mobile' => 20 
        ],
        'joins' => [
            'users' => ['cart_items.user_id','users.id'],
        ],
    ];




    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function storeDetail()
    {
        return $this->belongsTo(Stores::class, 'store_id', 'id');
    }
    public function storeRatingDetail()
    {
        return $this->hasOne(StoreRating::class, 'order_id', 'id');
    }
    public function requestDetail()
    {
        return $this->hasOne(Request::class, 'order_id', 'id');
    }
      public function cartItemDetails()
    {
        return $this->hasMany(CartItemdetails::class, 'cart_id', 'id');
    }
    public function UserrefundDetails()
    {
        return $this->hasOne(UserRefunds::class, 'order_id', 'id');
    }
    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedCreatedAtAttribute()
    {
        // if ($this->created_at==null||!auth()->user()) {
        //     return null;
        // }
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        // return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M h:i A');
     return Carbon::parse($this->created_at)->setTimezone($timezone)->format('D, jS M Y h:i A');

    }
}
