<?php

namespace App\Models\Admin;

use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Support\Facades\Request;
use App\Models\Admin\CancellationReasonTranslation;



class CancellationReason extends Model
{
    use UuidModel, HasActive, HasActiveCompanyKey, SearchableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cancellation_reasons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type', 'payment_type', 'arrival_status', 'reason', 'active', 'company_key', 'transport_type',
        'compensate_from',
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'modelDetail', 'cancellationtranslation'
    ];

    /**
     * Define the cancellationtranslation relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function cancellationReasonTranslationWords()
    {
        return $this->hasMany(CancellationReasonTranslation::class, 'cancellation_reason_id', 'id');
    }

}
