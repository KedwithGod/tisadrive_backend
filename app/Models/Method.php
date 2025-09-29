<?php

namespace App\Models;

use App\Models\Admin\Driver;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\DriverBankInfo;
use App\Models\Admin\OwnerBankInfo;

class Method extends Model
{
    protected $fillable = ['method_name', 'active'];

    protected $appends = ['field_names'];

    public function fields()
    {
        return $this->hasMany(Field::class, 'method_id', 'id'); 
    }
   
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id')->withTrashed(); 
    }
    public function driverBankInfo()
    {
        return $this->hasOne(DriverBankInfo::class, 'method_id', 'id');

    }
    public function ownerBankInfo()
    {
        return $this->hasOne(OwnerBankInfo::class, 'method_id', 'id');

    }
    public function getFieldNamesAttribute()
    {
        return $this->fields()->pluck('input_field_name'); 
    }
}

