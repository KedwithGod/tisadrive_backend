<?php

namespace App\Models\Master;

use App\Models\Traits\HasActive;
use App\Models\Traits\HasActiveCompanyKey;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class CarModel extends Model
{
    use HasActive,HasActiveCompanyKey,SearchableTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'car_models';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['make_id','name','active'];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'makeDetail','carmodeltranslation'
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
            'car_models.name' => 20,
        ],


    ];

    public function CarModelTranslation()
    {
        return $this->belongsTo(CarModelTranslation::class, 'make_id', 'id');
    }
    public function makeDetail()
    {
        return $this->belongsTo(CarMake::class, 'make_id', 'id');
    }
}
