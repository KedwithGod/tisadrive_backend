<?php

namespace App\Models;
use Storage;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use App\Models\FoodDelivery\MenuItems;
use App\Models\FoodDelivery\Stores;
use App\Models\Admin\ServiceLocation;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\DeleteOldFiles;

class BannerMessage extends Model {
	
	use HasActive, UuidModel,DeleteOldFiles;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'banner_messages';

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
		 'banner_message','title','url','image','store_id','menu_item_id','banner_type','service_location_id','status'
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
    public function menu_item()
    {
        return $this->belongsTo(MenuItems::class, 'menu_item_id', 'id');
    }
      public function storeDetail()
    {
        return $this->belongsTo(Stores::class, 'store_id', 'id');
    }
    public function service_locations()
    {
        return $this->belongsTo(ServiceLocation::class, 'service_location_id', 'id');
    }
    public function getServiceLocationNameAttribute()
    {
        return $this->service_locations->name?$this->service_locations->name:$this->null;
    }
}
