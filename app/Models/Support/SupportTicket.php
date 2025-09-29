<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Base\Uuid\UuidModel;
use App\Models\Traits\HasActive;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasActiveCompanyKey;
use Nicolaslopezj\Searchable\SearchableTrait;
use App\Models\Support\SupportTicketTitle;
use App\Models\Support\SupportTicketMultiFile;
use App\Models\User;
use App\Models\Admin\AdminDetail;
use App\Models\Admin\ServiceLocation;
use Carbon\Carbon;

class SupportTicket extends Model
{
    use HasActive, UuidModel,SearchableTrait,HasActiveCompanyKey;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title_id', 'description', 'users_id', 'assign_to', 'request_id', 'status', 'support_type', 'ticket_id', 'service_location_id'
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [

    ];

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'support_tickets.ticket_id' => 20,
        ],


    ];

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
        'title','user_name','converted_created_at','admin_name','service_location_name','user_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function adminDetails()
    {
        return $this->belongsTo(AdminDetail::class, 'assign_to', 'user_id');
    }

    public function request()
    {
        return $this->hasOne(Request::class, 'request_id', 'request_number')->withTrashed();
    }

    public function ticketTitle()
    {
        return $this->belongsTo(SupportTicketTitle::class, 'title_id', 'id');
    }

    public function multiFiles(){
        return $this->hasMany(SupportTicketMultiFile::class,'ticket_id','id');
    }
    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class, 'service_location_id', 'id')->withTrashed();
    } 

    public function getTitleAttribute()
    {
       if ($this->ticketTitle()->exists()) {
            return $this->ticketTitle->title;
        }
        return null;
    }
    public function getUserTypeAttribute()
    {
       if ($this->ticketTitle()->exists()) {
            return $this->ticketTitle->user_type;
        }
        return null;
    }
    public function getUserNameAttribute()
    {
       if ($this->user()->exists()) {
            return $this->user->name;
        }
        return null;
    }

    public function getAdminNameAttribute()
    {
       if ($this->adminDetails()->exists()) {
            return $this->adminDetails->first_name;
        }
        return null;
    }

    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedCreatedAtAttribute()
    {
        if ($this->created_at==null||!auth()->user()) {
            return null;
        }
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedUpdatedAtAttribute()
    {
        if ($this->updated_at==null||!auth()->user()) {
            return null;
        }
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->updated_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    public function getServiceLocationNameAttribute()
    {
        if($this->serviceLocation()->exists()){
            return $this->serviceLocation?$this->serviceLocation->name:null;            
        }else{

            return null;
        }
    }
}
