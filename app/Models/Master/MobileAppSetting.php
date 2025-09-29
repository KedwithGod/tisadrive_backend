<?php

namespace App\Models\Master;
use Storage;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\DeleteOldFiles;

class MobileAppSetting extends Model {
    
    use HasActive, UuidModel,DeleteOldFiles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mobile_app_settings';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'name','mobile_menu_icon','service_type','active','order_by'
    ];
    /**
     * The attributes that have files that should be auto deleted on updating or deleting.
     *
     * @var array
     */
    public $deletableFiles = [
        'mobile_menu_icon'
    ];


    /**
     * The attributes that can be used for sorting with query string filtering.
     *
     * @var array
     */
    public $sortable = [
        'name',
    ];

    public function getMobileMenuIconAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
    }

    /**
     * The default file upload path.
     *
     * @return string|null
     */
    public function uploadPath()
    {
        return config('base.mobile.upload.images.path');
    }
}
