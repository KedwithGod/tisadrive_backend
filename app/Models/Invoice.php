<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = '_invoice';

    protected $fillable = [
        'time_fair',
        'distance_fair',
        'base_fair',
        'base_distance',
        'total'


    ];
}
