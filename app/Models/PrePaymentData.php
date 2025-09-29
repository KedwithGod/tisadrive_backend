<?php

namespace App\Models;

use App\Base\Uuid\UuidModel;
use App\Models\Admin\PromoUser;
use App\Models\Admin\PromoCodeUser;
use App\Models\Traits\HasActive;
use App\Models\Admin\ServiceLocation;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class PrePaymentData extends Model
{
    use UuidModel,HasActive,SearchableTrait;

    protected $table = 'pre_payment_datas';

    protected $fillable = [
        'driver_id','request_id','card_number','amount','driver_commission','payment_opt'
    ]; 
    




}
