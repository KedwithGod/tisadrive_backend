<?php

namespace App\Models\Master;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasActiveCompanyKey;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class CarMake extends Model
{
    use HasActive,HasActiveCompanyKey,SearchableTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'car_makes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','transport_type','vehicle_make_for','active','translation_dataset'];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'modelDetail','carmaketranslation'
    ];

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'car_makes.name' => 20,
            'car_makes.vehicle_make_for' => 20,
            'car_makes.transport_type' => 20,
        ],


    ];
    public function carmaketranslation() {
        return $this->hasMany(CarMakeTranslation::class, 'car_make_id', 'id');
    }

    public function modelDetail()
    {
        return $this->hasOne(CarModel::class, 'make_id', 'id');
    }
}
