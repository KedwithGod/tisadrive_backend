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

class WebHeader extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;
    // ,SoftDeletes;

    protected $table = 'web_headers';

    protected $fillable = [
        'theme_color','logo','fevicon','footer_bg_color','footer_logo','footer_about_para',
        'footer_insta_link','footer_twitter_link','footer_fb_link','footer_linkdin_link',
        'footer_user_playstore','footer_user_appstore','footer_driver_playstore',
        'footer_driver_appstore','footer_copy_rights',
       
        
        
    ];

    protected $appends = [
        'logo','fevicon','footer_logo',
        
    ];

    public function getHeaderImgAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
    }

    public function uploadPath()
    {
        
        return config('base.website.upload.images.path');
    }


}
