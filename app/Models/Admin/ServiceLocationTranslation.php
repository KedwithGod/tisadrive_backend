<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;


class ServiceLocationTranslation extends Model
{
    use HasActive;
    protected $table = 'service_location_translations';

    protected $fillable = ['service_location_id','name','locale'];

  
      public function language()
    {
        return $this->belongsTo(Language::class, 'code', 'locale');
    }
}
