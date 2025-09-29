<?php

namespace App\Models\Admin;

use App\Base\Uuid\UuidModel;
use App\Models\Master\CarMake;
use App\Models\Master\CarModel;
use App\Models\Traits\HasActive;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Request\Request;

class Fleet extends Model
{
    use UuidModel,SoftDeletes,HasActive;

    protected $table = 'fleets';

    protected $fillable = [
        'owner_id','brand','model','license_number','permission_number','vehicle_type','active','fleet_id',
        'qr_image','approve','car_color','driver_id','custom_make','custom_model',
        'image1','image2','image3','status'
    ];

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
       'car_make_name','car_model_name','image1', 'vehicle_type_name','driver_name','owner_name'
    ];

    public function vehicleTypeDetail(){
        return $this->belongsTo(VehicleType::class,'vehicle_type','id');
    }

    public function carBrand(){
        return $this->belongsTo(CarMake::class,'brand','id');
    }

    public function carModel(){
        return $this->belongsTo(CarModel::class,'model','id');
    }

    public function fleetDocument(){
        return $this->hasMany(FleetDocument::class,'fleet_id','id');
    }

    public function getQrCodeImageAttribute(){
        return asset('storage/uploads/qr-codes/'.$this->qr_image);
    }

    public function user(){
        return $this->belongsTo(User::class,'owner_id','id')->withTrashed();
    }

    public function uploadPath()
    {
        // Assuming $this->image1 contains the filename of the uploaded image
        return asset('storage/app/public/uploads/fleets/images/' . $this->image1);
    }

    public function getIconAttribute()
    {
        if (!$this->image1) {
            return null;
        }

        return $this->uploadPath();
    }

    public function requestDetail()
    {
        return $this->hasMany(Request::class, 'driver_id', 'id');
    }
    public function getCarMakeNameAttribute()
    {
        if($this->carBrand()->exists()){
            return $this->carBrand?$this->carBrand->name:null;
        }else{

            return $this->custom_make;
        }
    }
    public function getCarModelNameAttribute()
    {
        if($this->carModel()->exists()){
            return $this->carModel?$this->carModel->name:null;
        }else{
            return $this->custom_model;
        }
    }

    public function getFleetNameAttribute(){
        return  $this->carBrand->name .' - '. $this->carModel->name .' ('.$this->vehicleType->name.')';
    }
    public function getVehicleTypeNameAttribute(){
        return  $this->vehicleTypeDetail ? $this->vehicleTypeDetail->name : null;
    }
    public function getDriverNameAttribute(){
        return  $this->driverDetail ? $this->driverDetail->name : null;
    }
    public function getOwnerNameAttribute()
    {
        return  $this->ownerDetail ? $this->ownerDetail->name : null;
    }

    public function driverDetail(){
        return $this->belongsTo(Driver::class,'id','fleet_id')->withTrashed();
    }

    public function ownerDetail(){
        return $this->belongsTo(User::class,'owner_id','id')->withTrashed();
    }

    //////////
    public function getImage1Attribute($value)
    {
        // Define the logic to retrieve the image attribute here
        // For example, if 'image1' is a column in your database table, you can simply return it
        return $value;
    }
}
