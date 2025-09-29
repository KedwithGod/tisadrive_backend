<?php

namespace App\Models\Master;

use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\DeleteOldFiles;
use Illuminate\Support\Facades\Storage;


class BannerImage extends Model {
    
    use HasActive, UuidModel,DeleteOldFiles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banner_images';

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
         'banner_message','title','url','image'
    ];
    /**
     * The attributes that have files that should be auto deleted on updating or deleting.
     *
     * @var array
     */
    public $deletableFiles = [
        'image'
    ];


    /**
     * The attributes that can be used for sorting with query string filtering.
     *
     * @var array
     */
    public $sortable = [
        'banner_message',
    ];

    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
        //  $relativePath = file_path($this->uploadPath(), $value);
        //     return url('storage/' . $relativePath);
        return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
    }

    /**
     * The default file upload path.
     *
     * @return string|null
     */
    public function uploadPath()
    {
        return config('base.bannerimage.upload.images.path');
    }
}
