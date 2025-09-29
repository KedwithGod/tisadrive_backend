<?php

namespace App\Models\Master;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasActiveCompanyKey;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Models\Admin\ZoneTypePackagePrice;

class PackageType extends Model
{
    //
     use HasActive,HasActiveCompanyKey,SearchableTrait;

      /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'package_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','transport_type','active','description','short_description'];

            /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'package_types.name' => 20,
            'package_types.transport_type' => 20,
        ],


    ];

    public function zoneTypePackage()
    {
        return $this->belongsTo(ZoneTypePackagePrice::class, 'id', 'package_type_id');
    }
   
}
