<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class LandingContact extends Model
{
    use HasFactory,SearchableTrait,UuidModel,HasActive,HasActiveCompanyKey;
    // ,SoftDeletes;

    protected $table = 'landing_contacts';

    protected $fillable = [
        'hero_title',
        'contact_heading',
        'contact_para',
        'contact_address_title',
        'contact_address',
        'contact_phone_title',
        'contact_phone',
        'contact_mail_title',
        'contact_mail',
        'contact_web_title',
        'contact_web',
        'form_name',
        'form_mail',
        'form_subject',
        'form_message',
        'form_btn',
        'locale','language',
        'direction',

        
    ];

 
    protected $appends = [
        

    ];
  



}