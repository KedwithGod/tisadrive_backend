<?php

namespace App\Models\Admin;

use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;

class PackageType extends Model
{
    //
     use HasActive;

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

   
}
