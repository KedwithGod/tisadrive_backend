<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;
use App\Models\FoodDelivery\MenuItems;

class CartItemdetails extends Model
{
    use HasFactory,HasActive;

      protected $table = 'cart_item_details';

      protected $fillable = ['cart_id','menu_item_id','quantity','base_price','price','discount','discount_type','variations','status']; 

      protected $includes = ['MenuDetails','CartDetails'];

      public function MenuDetails()
      {
          return $this->hasOne(MenuItems::class, 'id', 'menu_item_id');
      }
      public function CartDetails()
      {
          return $this->belongsTo(CartItems::class, 'cart_id', 'id');
      }
}
