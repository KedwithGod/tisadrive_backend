<?php

namespace App\Models;

use App\Models\FoodDelivery\Stores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreRating extends Model
{
    use HasFactory;

    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'store_rating';

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id', 'driver_id', 'store_id','request_id','order_id','rating','comments','optional_comments'
	];

	public function storeratings() {
        return $this->hasOne(Stores::class,'store_id','id');
    }


}
