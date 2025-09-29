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

class WebDriver extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;
    // ,SoftDeletes;

    protected $table = 'web_drivers';

    protected $fillable = [
        'hero_title','hero_img','driver_heading_1','driver_heading_2','driver_img_1','driver_title_1',
        'driver_para_1','driver_img_2','driver_title_2','driver_para_2','driver_img_3','driver_title_3','driver_para_3','how_it_work_heading',
        'how_it_work_title_1','how_it_work_para_1','how_it_work_img_1','how_it_work_title_2','how_it_work_para_2','how_it_work_img_2','how_it_work_title_3','how_it_work_para_3','how_it_work_img_3','how_it_work_title_4',
        'how_it_work_para_4','how_it_work_img_4','how_it_work_title_5','how_it_work_para_5','how_it_work_img_5','how_it_work_title_6','how_it_work_para_6','how_it_work_img_6','how_it_work_title_7',
        'how_it_work_para_7','how_it_work_img_7','req_heading','req_title','req_list_1','req_list_2','req_list_3',
        'req_list_4','req_list_5','req_img','vechile_req_title','vechile_req_list_1','vechile_req_list_2',
        'vechile_req_list_3','vechile_req_list_4','vechile_req_list_5','vechile_req_img','doc_req_title','doc_req_list_1',
        'doc_req_list_2','doc_req_list_3','doc_req_list_4','doc_req_img',

        
    ];

    protected $appends = [
        'hero_img','driver_img_1','driver_img_2','driver_img_3','req_img','vechile_req_img','doc_req_img','how_it_work_img_1',
        'how_it_work_img_2','how_it_work_img_3','how_it_work_img_4','how_it_work_img_5','how_it_work_img_6','how_it_work_img_7',

    ];

    public function getDriverAttribute($value)
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
