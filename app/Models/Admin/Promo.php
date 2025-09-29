<?php

namespace App\Models\Admin;

use App\Base\Uuid\UuidModel;
use App\Models\Admin\PromoUser;
use App\Models\Admin\PromoCodeUser;
use App\Models\Traits\HasActive;
use App\Models\Admin\ServiceLocation;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Promo extends Model
{
    use UuidModel,HasActive,SearchableTrait;

    protected $table = 'promo';

    protected $fillable = [
        'module','code','service_location_id','minimum_trip_amount','maximum_discount_amount','discount_percent','total_uses','uses_per_user','from','to','active','transport_type','service_ids','user_ids','user_specific'
    ]; 
    

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'promoUsers','serviceLocation'
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
            'promo.code' => 20,
        ],

    ];

    public function getFromDateAttribute()
    {
        return now()->parse($this->from)->toDateString();
    }

    public function getToDateAttribute()
    {
        return now()->parse($this->to)->toDateString();
    }
    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class, 'service_location_id', 'id');
    }

    public function promoCodeUsers()
    {
        return $this->hasMany(PromoCodeUser::class, 'promo_code_id', 'id');
    }
    public function promoUsers()
    {
        return $this->hasMany(PromoUser::class, 'promo_code_id', 'id');
    }
}
