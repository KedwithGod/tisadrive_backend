<?php

namespace App\Models;

use App\Base\Slug\HasSlug;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Nicolaslopezj\Searchable\SearchableTrait;

class Country extends Model
{
    use HasActive,SearchableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countries';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'dial_code',
        'code',
        'active',
        'flag',
        'dial_min_length',
        'dial_max_length',
        'currency_name',
        'currency_code',
        'currency_symbol'
    ];

    protected $searchable = [
        'columns' => [
            'countries.dial_code' => 20,
            'countries.name' => 20,
            'countries.currency_code'=> 20,
            'countries.code'=> 20,
            'countries.currency_name'=> 20,
        ],
    ];
    /**
     * The attributes that can be used for sorting with query string filtering.
     *
     * @var array
     */
    public $sortable = [
        'name',
    ];

    /**
    * Get the Flag's full file path.
    *
    * @param string $value
    * @return string
    */
    public function getFlagAttribute($value)
    {
       if (empty($value)) {
            return null;
        }
        return Storage::disk('public')->url(file_path($this->uploadPath(), $value));
    }

    /**
     * The default file upload path.
     *
     * @return string|null
     */
    public function uploadPath()
    {
        return config('base.country.upload.flag.path');
    }

    /**
     * Get all the countries from the JSON file.
     *
     * @return array
     */
    public static function allJSON()
    {
        $route = dirname(dirname(__FILE__)) . '/Helpers/Countries/countries.json';
        return json_decode(file_get_contents($route), true);
    }
}
