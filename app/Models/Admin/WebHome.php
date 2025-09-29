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

class WebHome extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;
    // ,SoftDeletes;

    protected $table = 'web_homes';

    protected $fillable = [
        'hero_title_1','hero_title_2','hero_img','hero_user_link_android','hero_user_link_apple','hero_driver_link_android',
        'hero_driver_link_apple','feature_heading_1','feature_heading_2','feature_sub_heading_1','feature_sub_para_1','feature_sub_heading_2','feature_sub_para_2','feature_sub_heading_3',
        'feature_sub_para_3','feature_sub_heading_4','feature_sub_para_4','service_heading_1','service_heading_2','service_para','service_1',
        'service_2','service_3','service_4','service_bg','service_video_link','slider_img_1',
        'slider_para_1','slider_img_2','slider_para_2','slider_img_3','slider_para_3','about_title_1',
        'about_title_2','about_img','about_para','about_list_1','about_list_2','about_list_3',
        'about_list_4','drive_heading','drive_img_1','drive_title_1','drive_para_1',
        'drive_img_2','drive_title_2','drive_para_2','drive_img_3','drive_title_3','drive_para_3',
        'service_area_img','service_area_title','service_area_para','service_area_imgs',

        
    ];

    protected $appends = [
        'hero_img','service_bg','slider_img_1','slider_img_2','slider_img_3','about_img','drive_img_1','drive_img_2',
        'drive_img_3','service_img','service_imgs',

    ];

    public function getHomeAttribute($value)
    {
        // if (empty($value)) {
        //     return null;
        // }

        return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
        
    }

    public function uploadPath()
    {
        
        // return folder_merge(config('base.types.upload.images.path')
        return config('base.website.upload.images.path');
    }


}
