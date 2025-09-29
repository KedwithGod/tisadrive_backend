<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class LandingQuickLink extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;
    // ,SoftDeletes;

    protected $table = 'landing_quicklinks';

    protected $fillable = [
            'privacy_title',
            'privacy',
            'terms_title',
            'terms',
            'compliance_title',
            'compliance',
            'dmv_title',
            'dmv',
            'locale',
            'language',
            'direction',

        
    ];

 
    protected $appends = [
        

    ];
  


}