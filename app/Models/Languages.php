<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Languages extends Model
{
    use HasFactory,SearchableTrait;
    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */

    protected $table = 'languages'; 

   protected $fillable = [
       'code',
       'direction',
       'active',
       'name',
       'default_status'
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
        'languages.name' => 20,
        'languages.code' => 20,
        'languages.direction' => 20,
    ],

];

}
