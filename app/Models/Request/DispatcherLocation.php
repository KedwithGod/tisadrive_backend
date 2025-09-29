<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

class DispatcherLocation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dispatcher_location';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'place_id',
        'address',
        'latitude',
        'longitude',
        'is_airport',
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [

    ];
}
