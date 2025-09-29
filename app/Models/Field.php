<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'method_id',
        'input_field_name',
        'placeholder',
        'is_required',
        'input_field_type',
    ];

    public function method()
    {
        return $this->belongsTo(Method::class);
    }
}

