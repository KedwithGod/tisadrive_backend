<?php

namespace App\Models\Admin;

use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoneRewardPoint extends Model
{
    use HasActive;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'zone_reward_points';

    protected $fillable = [
        'zone_id','from','to','no_of_reward_points'
    ];


}
