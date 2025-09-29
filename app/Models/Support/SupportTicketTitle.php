<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Models\Support\SupportTicketCategory;
use App\Models\Support\SupportTicketTitleTranslation;

class SupportTicketTitle extends Model
{
    use HasActive, UuidModel,SearchableTrait,HasActiveCompanyKey;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_ticket_titles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title','category_type', 'title_type', 'user_type'
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [

    ];

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
        
    ];

    public function supportTicketTitleTranslationWords(){
        return $this->hasMany(SupportTicketTitleTranslation::class, 'ticket_title_id', 'id');
    }

}
