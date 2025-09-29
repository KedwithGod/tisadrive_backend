<?php

namespace App\Models\Admin;

use Carbon\Carbon;
use App\Base\Uuid\UuidModel;
use App\Models\Admin\Driver;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverDocument extends Model
{
    use HasActive, UuidModel,SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'driver_id', 
        'document_id', 
        'image',
        'identify_number',
        'expiry_date',
        'document_status',
        'comment',
        'back_image'  // Ensure back_image is fillable
    ];

    protected $appends = [
        'document_name',
        'identify_number_key'
    ];

    public $includes = [
        'driver'
    ];

    /**
     * Relationship: A document belongs to a driver.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id')->withTrashed();
    }

    /**
     * Accessor: Get the full file path for the front image.
     */
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
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
        // return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));

        $relativePath = file_path($this->uploadPath(), $value);
        return url('storage/' . $relativePath);
    }
    /**
    * Get the Document's name.
    *
    * @param string $value
    * @return string
    */
    public function getDocumentNameAttribute()
    {
        if (!$this->driverNeededDocuments()->exists()) {
            return null;
        }
        return $this->driverNeededDocuments->name;
    }
    /**
    * Get the is_identify_number_exists.
    *
    * @param string $value
    * @return string
    */
    public function getIdentifyNumberKeyAttribute()
    {
        if (!$this->driverNeededDocuments()->exists()) {
            return null;
        }
        return $this->driverNeededDocuments->identify_number_locale_key;
    }
    /**
    * The Document that the DriverNeededDocuments belongs to.
    * @tested
    *
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    */
    public function driverNeededDocuments()
    {
        return $this->belongsTo(DriverNeededDocument::class, 'document_id', 'id');
    }

    /**
     * The default file upload path.
     *
     * @return string|null
     */
    public function uploadPath()
    {
        if (!$this->driver()->exists()) {
            return null;
        }
        return folder_merge(config('base.driver.upload.documents.path'), $this->driver->id);
    }
    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedCreatedAtAttribute()
    {
        if ($this->created_at==null||!auth()->user()) {
            return null;
        }
        if(auth()->user()){
            $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        }else{
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedUpdatedAtAttribute()
    {
        if ($this->updated_at==null||!auth()->user()) {
            return null;
        }
       if(auth()->user()){
            $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        }else{
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }
        return Carbon::parse($this->updated_at)->setTimezone($timezone)->format('jS M h:i A');
    }

    public function getExpiryDateAttribute($value)
    {
        if ($value==null) {
            return null;
        }
        // if(auth()->user()){
        //     $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        // }else{
        //     $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        // }

            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');

        return Carbon::parse($value)->setTimezone($timezone)->format('Y-m-d');
    }
}
