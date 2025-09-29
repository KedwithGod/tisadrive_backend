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

class WebUser extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;
    // ,SoftDeletes;

    protected $table = 'web_users';

    protected $fillable = [
        'hero_title','hero_img','user_heading_1','user_heading_2','user_img_1','user_title_1',
        'user_para_1','user_img_2','user_title_2','user_para_2','user_img_3','user_title_3','user_para_3','how_it_work_heading',
        'how_it_work_title_1','how_it_work_para_1','how_it_work_img_1','how_it_work_title_2','how_it_work_para_2','how_it_work_img_2','how_it_work_title_3','how_it_work_para_3','how_it_work_img_3','how_it_work_title_4',
        'how_it_work_para_4','how_it_work_img_4','how_it_work_title_5','how_it_work_para_5','how_it_work_img_5','how_it_work_title_6','how_it_work_para_6','how_it_work_img_6',
        
    ];

    protected $appends = [
        'hero_img','user_img_1','user_img_2','user_img_3','how_it_work_img_1','how_it_work_img_2',
        'how_it_work_img_3','how_it_work_img_4','how_it_work_img_5','how_it_work_img_6',

    ];

    public function getUserAttribute($value)
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
