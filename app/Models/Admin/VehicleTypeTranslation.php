<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;


class VehicleTypeTranslation extends Model
{
    use HasActive;
    protected $table = 'vehicle_type_translations';

    protected $fillable = ['vehicle_type_id','name','locale'];

  
      public function language()
    {
        return $this->belongsTo(Language::class, 'code', 'locale');
    }
}
