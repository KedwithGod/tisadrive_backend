<?php

namespace App\Models\Master;
use Storage;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\DeleteOldFiles;

class MobileTheme extends Model {
    
    use HasActive, UuidModel,DeleteOldFiles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mobile_themes';

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
         'theme'
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

}
