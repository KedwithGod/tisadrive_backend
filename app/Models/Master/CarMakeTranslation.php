<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;


class CarMakeTranslation extends Model
{
    use HasActive;
    protected $table = 'car_make_translations';

    protected $fillable = ['car_make_id','name','locale'];

     public function CarMake()
    {
        return $this->belongsTo(CarMake::class, 'car_make_id', 'id');
    }
      public function language()
    {
        return $this->belongsTo(Language::class, 'code', 'locale');
    }
}
