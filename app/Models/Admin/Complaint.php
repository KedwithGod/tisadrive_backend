<?php

namespace App\Models\Admin;

use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use App\Models\User;
use App\Models\Admin\Owner;
use App\Models\Admin\Driver;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Complaint extends Model
{
    use HasActive,UuidModel,SearchableTrait;

    protected $fillable = [
    'user_type','user_id','request_id','complaint_type','complaint_title_id','description','status','driver_id','transport_type'
    ];



    protected $appends = [
        'user_name', 'complaint_title','driver_name','owner_name'
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
            'complaints.user_type' => 20,
        ],

    ];

    public function complaint(){
        return $this->belongsTo(ComplaintTitle::class,'complaint_title_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id')->withTrashed();
    } 
    public function driver(){
        return $this->belongsTo(Driver::class,'driver_id','id')->withTrashed();
    }
    public function owner(){
        return $this->belongsTo(Owner::class,'owner_id','id')->withTrashed();
    }


    public function getUserNameAttribute()
    {
        if($this->user()->exists()){
            return $this->user?$this->user->name:null;            
        }else{

            return null;
        }
    }
    public function getDriverNameAttribute()
    {
        if($this->driver()->exists()){
            return $this->driver?$this->driver->name:null;            
        }else{

            return null;
        }
    }
    public function getOwnerNameAttribute()
    {
        if($this->owner()->exists()){
            return $this->owner?$this->owner->name:null;            
        }else{

            return null;
        }
    }
    public function getComplaintTitleAttribute()
    {
        if($this->complaint()->exists()){
            return $this->complaint?$this->complaint->title:null;            
        }else{

            return null;
        }
    }

}
