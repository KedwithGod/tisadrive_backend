<?php

namespace App\Models\Admin;

use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class FleetDocument extends Model
{
    use UuidModel,SoftDeletes,HasActive;

    protected $fillable = [
        'fleet_id','name','image','expiry_date','document_id','document_status',
        'comment','identify_number','back_image'
    ];

    public function fleet(){
        return $this->belongsTo(Fleet::class,'fleet_id','id');
    }

    public function getImageAttribute($value){

        // return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
        $relativePath = file_path($this->uploadPath(), $value);
        return url('storage/' . $relativePath);
    }
    /**
     * Accessor: Get the full file path for the back image.
     */
    public function getBackImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        $relativePath = file_path($this->uploadPath(), $value);
        return url('storage/' . $relativePath);
        // return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
    }    
    public function uploadPath()
    {
        if (!$this->fleet()->exists()) {
            return null;
        }

        return folder_merge(config('base.fleets.upload.images.path'), $this->fleet->id);
    }
}
