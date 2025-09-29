<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;
use App\Base\Uuid\UuidModel;
use App\Models\Admin\Subscription;

class SubscriptionDiscount extends Model
{
    use HasActive;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_discount';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscription_id','first_n_drivers','payable_amount',
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'subscriptionDetail',
    ];

    public function subscriptionDetail(){
        return $this->belongsTo(Subscription::class,'id','subscription_id');
    }
}
