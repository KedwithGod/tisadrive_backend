<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingEmail extends Model
{
    use HasFactory;

    protected $table = 'landing_email';

    protected $fillable = [
        'name',
        'mail',
        'subject',
        'comments',        
    ];
}
