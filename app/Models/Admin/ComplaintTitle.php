<?php

namespace App\Models\Admin;

use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Illuminate\Support\Facades\Request;
use App\Models\Admin\ComplaintTitleTranslation;
class ComplaintTitle extends Model
{
    use HasActive,UuidModel,HasActiveCompanyKey;
    protected $table = 'complaint_titles';

    public $includes = [
         'complaint_titles_translation_words'
    ];
    protected $fillable = [
        'user_type','title','complaint_type','active','company_key',
    ];
    public function complaintTitleTranslationWords()
    {
        return $this->hasMany(ComplaintTitleTranslation::class, 'complaint_title_id', 'id');
    }

}
