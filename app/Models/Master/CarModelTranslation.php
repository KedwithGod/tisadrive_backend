<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;


class CarModelTranslation extends Model
{
    use HasActive;
    protected $table = 'car_model_translations';

    protected $fillable = ['car_model_id','name','locale'];

     public function CarModel()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id', 'id');
    }
      public function language()
    {
        return $this->belongsTo(Language::class, 'code', 'locale');
    }
}
