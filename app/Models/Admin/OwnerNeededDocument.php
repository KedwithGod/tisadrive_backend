<?php

namespace App\Models\Admin;

use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class OwnerNeededDocument extends Model
{
    use HasActive,SearchableTrait;

    protected $table = 'owner_needed_documents';

    protected $fillable = [
        'name', 'doc_type', 'has_identify_number','has_expiry_date','active','identify_number_locale_key'
        ,'image_type','is_editable',
        'document_name_front','document_name_back','is_required'
    ];

            /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'owner_needed_documents.name' => 20,
        ],


    ];

    public function ownerDocument()
    {
        return $this->hasOne(OwnerDocument::class, 'document_id', 'id');
    }
}
