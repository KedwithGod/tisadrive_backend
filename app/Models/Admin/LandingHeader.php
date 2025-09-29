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

class LandingHeader extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;
    // ,SoftDeletes;

    protected $table = 'landing_headers';

    protected $fillable = [
            'header_logo',
            'home',
            'driver',
            'aboutus',
            'user',
            'contact',
            'book_now_btn',
            'footer_logo',
            'footer_para',
            'quick_links',
            'compliance',
            'privacy',
            'terms',
            'dmv',
            'user_app',
            'user_play',
            'user_play_link',
            'user_apple',
            'user_apple_link',
            'driver_app',
            'driver_play',
            'driver_play_link',
            'driver_apple',
            'driver_apple_link',
            'copy_rights',
            'fb_link',
            'linkdin_link',
            'x_link',
            'insta_link',
            'locale',
            'language',
            'direction',

        
    ];

 
    protected $appends = [
        

    ];
  

    public function getHeaderAttribute($value)
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