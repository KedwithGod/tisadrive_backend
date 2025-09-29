<?php

namespace App\Models\Admin;

use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class FleetNeededDocument extends Model
{
    use HasActive,SearchableTrait;

    protected $table = 'fleet_needed_documents';

    protected $fillable = [
        'name', 'doc_type', 'has_identify_number','has_expiry_date',
        'document_name_front','document_name_back',
        'active','identify_number_locale_key','image_type','is_editable','is_required'
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
            'fleet_needed_documents.name' => 20,
        ],


    ];

    public function fleetDocument()
    {
        return $this->hasOne(FleetDocument::class, 'document_id', 'id');
    }
}
