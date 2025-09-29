<?php


namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;
use Illuminate\Support\Facades\Request;
 // Update this line
use App\Models\Admin\CancellationReason;
class ComplaintTitleTranslation extends Model
{
    use HasActive;
    protected $table = 'complaint_title_translations';

    protected $fillable = ['complaint_title_id','title','locale'];
    public function complaintTitleTranslationshow()
    {
        return $this->hasMany(ComplaintTitleTranslation::class, 'complaint_title_id', 'id');
    }

}
