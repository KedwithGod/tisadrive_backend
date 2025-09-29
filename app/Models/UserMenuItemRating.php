<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMenuItemRating extends Model
{
    use HasFactory;

    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'user_menu_items_rating';

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'menu_item_id', 'user_id', 'store_id','rating'];
}
