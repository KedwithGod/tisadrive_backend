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

class LandingHome extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;
    // ,SoftDeletes;

    protected $table = 'landing_homes';

    protected $fillable = [
        'hero_title','hero_user_link_android','hero_user_link_apple','hero_driver_link_android',
        'hero_driver_link_apple','feature_heading','feature_para','feature_sub_heading_1','feature_sub_para_1','feature_sub_heading_2','feature_sub_para_2','feature_sub_heading_3',
        'feature_sub_para_3','feature_sub_heading_4','feature_sub_para_4','service_heading_1','service_heading_2','service_para','services','service_img','box_img_1',
        'box_para_1','box_img_2','box_para_2','box_img_3','box_para_3','about_title_1',
        'about_title_2','about_img','about_para','about_lists','drive_heading','drive_title_1','drive_para_1','drive_title_2','drive_para_2','drive_title_3','drive_para_3',
        'service_area_img','service_area_title','service_area_para','locale','language','direction'

        
    ];

 
    protected $appends = [
        

    ];
  

    public function getHomeAttribute($value)
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