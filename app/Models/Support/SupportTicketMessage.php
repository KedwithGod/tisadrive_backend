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
use Carbon\Carbon;

class SupportTicketMessage extends Model
{
    use HasActive, UuidModel,SearchableTrait,HasActiveCompanyKey;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_ticket_messages';

    protected $fillable = ['ticket_id', 'user_id', 'employee_id', 'message', 'is_read','sender_id'];


      /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
        'converted_created_at'
    ];

    // Relationship with SupportTicket
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id', 'id');
    }

    // Relationship with Users (Sender)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relationship with Users (Receiver)
    public function employee()
    {
        return $this->belongsTo(AdminDetail::class, 'employee_id', 'user_id');
    }

    // // Relationship with Ticket Attachments
    // public function attachments()
    // {
    //     return $this->hasMany(TicketAttachment::class, 'message_id');
    // }

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
}
