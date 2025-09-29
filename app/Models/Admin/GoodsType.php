<?php

namespace App\Models\Admin;

use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\GoodsTypeTranslation;

class GoodsType extends Model
{
    use HasActive;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'goods_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'goods_type_name', 'goods_types_for', 'active',
    ];

   
    public function goodsTypeTranslationWords(){
        return $this->hasMany(GoodsTypeTranslation::class, 'goods_type_id', 'id');
    }
}
