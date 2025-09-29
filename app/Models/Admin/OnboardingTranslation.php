<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;


class OnboardingTranslation extends Model
{
    use HasActive;
    protected $table = 'onboarding_screen_translations';

    protected $fillable = ['onboarding_screen_id','title','description','locale'];

  
      public function language()
    {
        return $this->belongsTo(Language::class, 'code', 'locale');
    }
}
