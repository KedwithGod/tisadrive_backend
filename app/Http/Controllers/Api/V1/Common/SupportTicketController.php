<?php

namespace App\Http\Controllers\Api\V1\Common;

use Illuminate\Http\Request;
use App\Models\Admin\Complaint;
use App\Base\Constants\Auth\Role;
use App\Models\Admin\ComplaintTitle;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Http\Controllers\Api\V1\BaseController;
use App\Base\Constants\Masters\ComplaintType;
use App\Base\Filters\Admin\ComplaintTitleFilter;
use App\Transformers\Requests\SupportTicketTitleTransformer;
use App\Models\Support\SupportTicketTitle;
use Carbon\Carbon;
use App\Models\Support\SupportTicket;
use App\Models\Support\SupportTicketMultiFile;
use App\Models\Support\SupportTicketMessage;
use Illuminate\Support\Facades\Auth;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\SupportTicketFilter;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use Log;



/**
 * @group Complaints apis
 *
 * APIs for Complaints
 */
class SupportTicketController extends BaseController
{
    protected $imageUploader;
    public function __construct(ImageUploaderContract $imageUploader, SupportTicketMultiFile $multifile)
    {
        $this->imageUploader = $imageUploader;
        $this->multifile = $multifile;
      
    }
    
    // List Ticket titles
    //  @queryParam title_type string required title type for the title request
    //  @response 
    //  {
    //         "success": true,
    //         "message": "tikcket_titles_listed",
    //         "data": [
    //             {
    //                 "id": "2",
    //                 "title_type": "general",
    //                 "user_type": "user",
    //                 "title": "Support Management"
    //             }
    //         ]
    //     }
    
    public function index()
    {
        if (access()->hasRole(Role::USER)) {
            $user_type = 'user';
        } elseif(access()->hasRole(Role::DRIVER)) {
            $user_type = 'driver';
        }else{
            $user_type = 'owner';
        }
        $title_type='general';

        if (request()->title_type=='request') {
            $title_type = 'request';
        }

        $result = SupportTicketTitle::where('active',true)->where('user_type', $user_type)->where('title_type', $title_type)->get();

        $translated_results = fractal($result, new SupportTicketTitleTransformer);


        return $this->respondSuccess($translated_results, 'Ticket Titles Listed');
    }

    //Ticket List
    public function tikcetList( Request $request)
    {
        $query = SupportTicket::query()->orderBy('created_at','DESC')->where("users_id",auth()->user()->id);
        // $results = $queryFilter->builder($query)->customFilter(new TicketFilter)->paginate();
        $result  = filter($query)->customFilter(new SupportTicketFilter)->paginate();
        return $this->respondSuccess($result->items(),'Ticket Listed');

    }

    // create Ticket
    public function makeTicket(Request $request)
    {
        // Validate params
        $request->validate([
        'title_id' => 'required',
        'description'=>'required',
        ]);

        $created_params = $request->all();
        if ($request->request_id) {
            $created_params['request_id'] = $request->request_id;
            $created_params['support_type'] = 'request';
        }

	else{
            $created_params['request_id'] = null;
            $created_params['support_type'] = 'general';

	}
        if (access()->hasRole(Role::USER)) {
            $created_params['service_location_id'] = $request->service_location_id;
        }elseif(access()->hasRole(Role::DRIVER)) {
            $created_params['service_location_id'] = auth()->user()->driver->service_location_id;
        }elseif(access()->hasRole(Role::OWNER)) {
            $created_params['service_location_id'] = auth()->user()->owner->service_location_id;
        }

        if (access()->hasRole(Role::USER)) {
            $created_params['users_id'] = auth()->user()->id;
        }elseif(access()->hasRole(Role::DRIVER)) {
            $created_params['users_id'] = auth()->user()->id;
        }

        $created_params['status'] = 1;
        $created_params['assign_to'] = null;
	    $current_timestamp = Carbon::now()->timestamp.rand(0, 99);

        $created_params['ticket_id'] = 'TIC_'.$current_timestamp;

        
        $support_ticket = SupportTicket::create($created_params);

        // Store Multiple Files in MultiImages Table
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = $this->imageUploader->file($file)->saveSupportImage();
                    SupportTicketMultiFile::create([
                        'ticket_id' => $support_ticket->id, 
                        'image_name' => $filename, // Store consistent filename
                    ]);
                }
            }else{
            }
            return $this->respondSuccess($data=null, 'support Ticket Successfully');
        }

            // reply message for support Ticket

            public function replyMessage(Request $request, SupportTicket $supportTicket)
            { 

                $validated = $request->validate([
                    'message' => 'required',
                ]);
                $created_params['message'] = $validated['message'];
                $created_params['ticket_id'] = $supportTicket->id;
                $created_params['user_id'] = $supportTicket->users_id;
                $created_params['employee_id'] = $supportTicket->assign_to;
                $created_params['sender_id'] = $supportTicket->users_id;

            
                $reply_ticket = SupportTicketMessage::create($created_params);
                // dd($reply_ticket);


                // Optionally, return a response
                return response()->json([
                    'successMessage' => 'Title created successfully.',
                    'reply_ticket' => $reply_ticket,
                ], 201);
            }


            // view Details for the Support Ticket
            public function viewTicketDetails(SupportTicket $supportTicket) {
                $user = Auth::user();
                // $reply_message = SupportTicketMessage::where('ticket_id', $supportTicket->id)->get();
                $reply_message = SupportTicketMessage::where('ticket_id', $supportTicket->id)
                    ->orderBy('created_at', 'asc') // Ensure messages are sorted in ascending order
                    ->get();
                $attachment = SupportTicketMultiFile::where('ticket_id', $supportTicket->id)->get();
                // dd($attachment); 
                return response()->json(['supportTicket'=>$supportTicket,
                'user' => $user,'reply_message'=>$reply_message,'attachment' => $attachment
                 ]);
            }

}
