<?php


namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;
use Illuminate\Support\Facades\Request;
 // Update this line
use App\Models\Admin\CancellationReason;
class CancellationReasonTranslation extends Model
{
    use HasActive;
    protected $table = 'cancellation_reason_translations';

    protected $fillable = ['cancellation_reason_id','name','locale'];

    public function cancellationReasonTranslationshow()
    {
        return $this->hasMany(CancellationReasonTranslation::class, 'cancellation_reason_id', 'id');
    }

}
