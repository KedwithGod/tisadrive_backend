<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Models\Support\SupportTicketCategory;

class SupportTicketTitleTranslation extends Model
{
    use HasActive;
    protected $table = 'support_ticket_titles_translations';

    protected $fillable = ['ticket_title_id','title','locale'];

  
      public function language()
    {
        return $this->belongsTo(Language::class, 'code', 'locale');
    }
}
