<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

class RecentSearch extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recent_searches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','transport_type','pick_lat','pick_lng','drop_lat','drop_lng','pick_address','drop_address','pickup_poc_name','pickup_poc_mobile','pickup_poc_instruction','drop_poc_name','drop_poc_mobile','drop_poc_instruction','total_distance','total_time','poly_line','pick_short_address','drop_short_address'];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'searchStops'
    ];
    /**
     * The searchStops that the meta belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function searchStops()
    {
        return $this->hasMany(RecentSearchStop::class, 'recent_searche_id', 'id');
    }
}
