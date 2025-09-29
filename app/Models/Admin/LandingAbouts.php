<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use App\Models\Traits\HasActiveCompanyKey;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LandingAbouts extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;

    protected $table = 'landing_abouts';

    protected $fillable = [
        'hero_title' ,
        'about_heading',
        'about_title',
        'about_para',
        'about_lists',
        'about_img',
        'ceo_name',
        'ceo_title',
        'signature',
        'ceo_para',
        'ceo_img',
        'vision_mision_heading',
        'vision_title',
        'vision_para',
        'mission_title',
        'mission_para',
        'team_title',
        'team_para', 
        'team_members',
        'testimonial_heading',
        'testimonial_content',
        'locale',
        'language',
        'direction'

        
    ];

 
    protected $appends = [
        

    ];
  

    public function getAboutusAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
        
    }

    public function uploadPath()
    {
        
        // return folder_merge(config('base.types.upload.images.path')
        return config('base.website.upload.images.path');
    }
}
