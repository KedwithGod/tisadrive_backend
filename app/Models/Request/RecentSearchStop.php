<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

class RecentSearchStop extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recent_searches_stops';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['recent_searche_id','address','latitude','longitude','poc_name','poc_mobile','order','poc_instruction','short_address'];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        
    ];
    
}
