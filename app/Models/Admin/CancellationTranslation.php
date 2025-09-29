<?php


namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActive;
 // Update this line
use App\Models\Admin\CancellationReason;
class CancellationTranslation extends Model
{
    use HasActive;
    protected $table = 'cancellation_reasons_translations';

    protected $fillable = ['cancellation_id','reason','locale'];

     public function CancellationReason()
    {
        return $this->belongsTo(CancellationReason::class, 'cancellation_id', 'id');
    }
      public function language()
    {
        return $this->belongsTo(Language::class, 'code', 'locale');
    }
    public function cancellationtranslations()
    {
        return $this->hasMany(CancellationTranslation::class, 'cancellation_id', 'id');
    }
    public function getNameAttribute()
    {

        $locale = Request::input('locale');
        if($locale == "")
        {
            $locale = app()->getLocale();
        }

        if(count($this->categorytranslations) > 0)
        {
            $err_status = 0;
            foreach($this->categorytranslations as $k=>$value)
            {
                if($value->locale == $locale)
                {
                    $err_status = 1;
                    return $value->name;
                }
                if($value->locale == "en")
                {
                    $name = $value->name;
                }
            }
            if($err_status == 0)
            {
                return $name;
            }
        }
    }
    public function reason()
    {
        return $this->belongsTo(CancellationReason::class, 'cancellation_id', 'id');
    }
}
