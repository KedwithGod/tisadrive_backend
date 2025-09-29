<?php

namespace App\Http\Controllers\support;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Transformers\CountryTransformer;
use App\Models\Support\SupportTicket;
use App\Models\Support\SupportTicketCategory;
use App\Models\Support\SupportTicketTitle;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\V1\BaseController;
use Kreait\Firebase\Database;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;
use App\Base\Filters\Admin\SupportTicketFilter;
use Illuminate\Support\Facades\Storage;
use App\Base\Filters\Master\CommonMasterFilter;
use Carbon\Carbon;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\AdminDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Support\SupportTicketMessage;
use App\Models\Support\SupportTicketMultiFile;


class SupportTicketsListController extends BaseController
{

    protected $imageUploader;
    protected $category;

    public function __construct(ImageUploaderContract $imageUploader, SupportTicketCategory $category)
    {
        $this->imageUploader = $imageUploader;
        $this->category = $category;
    }

    public function index() 
    {

        if(auth()->user()->hasRole('employee'))
        {
            $employee = auth()->user()->admin;

            return $this->IndividualDashboard();

        }
        $app_for = env("APP_FOR");
        $employees = AdminDetail::get();
        $support_ticket = SupportTicket::pluck("title_id");
        $admin = auth()->user()->admin;

        return Inertia::render('pages/support/tickets_lists/index',['app_for'=>$app_for,'employees' => $employees,
        'support_ticket'=> $support_ticket,'admin'=>$admin]);
    }

    //IndividualDashboard employee

    public function IndividualDashboard() 
    {
        
        if(auth()->user()->hasRole('employee'))
        {
            $employees = auth()->user()->admin;
            $support_ticket = SupportTicket::where("service_location_id",$employees->service_location_id)->get();
        }
        return Inertia::render('pages/support/tickets_lists/individual-index', [
            'support_ticket'=>$support_ticket,'employees' =>$employees,
        ]);

    }
    // List of User
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = SupportTicket::query()->orderBy('created_at','DESC');

        // $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();
        if (auth()->user()->hasRole('employee')) {
            $query->where("assign_to", auth()->user()->id)->orderBy('created_at','DESC');
        }
        

        $results = $queryFilter->builder($query)->customFilter(new SupportTicketFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function individualList(QueryFilterContract $queryFilter, Request $request)
    {
        $query = SupportTicket::query()->orderBy('created_at','DESC');

        if (auth()->user()->hasRole('employee')) {
            $employees = auth()->user()->admin;
            $query->where("service_location_id",$employees->service_location_id);
        }
        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function viewTicketDetails(SupportTicket $supportTicket) {
        $user = Auth::user();
        // $reply_message = SupportTicketMessage::where('ticket_id', $supportTicket->id)->get();
        $reply_message = SupportTicketMessage::where('ticket_id', $supportTicket->id)
            ->orderBy('created_at', 'asc') // Ensure messages are sorted in ascending order
            ->get();
        $attachment = SupportTicketMultiFile::where('ticket_id', $supportTicket->id)->get();
        return Inertia::render('pages/support/tickets_lists/view_ticket', ['supportTicket'=>$supportTicket,
        'user' => $user,'reply_message'=>$reply_message,'attachment' => $attachment
         ]);
    }

    public function replyMessage(Request $request, SupportTicket $supportTicket)
    {      // if(env('APP_FOR') == 'demo'){
        //     return response()->json([
        //         'alertMessage' => 'You are not Authorized'
        //     ], 403);
        // }
        // dd($request->all());
         // Validate the incoming request
        $validated = $request->validate([
            'message' => 'required',
        ]);

        $created_params['message'] = $validated['message'];
        $created_params['ticket_id'] = $supportTicket->id;
        $created_params['user_id'] = $supportTicket->users_id;
        $created_params['employee_id'] = $supportTicket->assign_to;
        $created_params['sender_id'] = $supportTicket->assign_to;

       
        $reply_ticket = SupportTicketMessage::create($created_params);
        // dd($reply_ticket);


        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Title created successfully.',
            'reply_ticket' => $reply_ticket,
        ], 201);
    }

    public function updateAssingTO(Request $request, SupportTicket $support_ticket)
    {

        // Validate the incoming request
            $updated_params =  $request->validate([
                'assign_to' => 'required',
            ]);
            $support_ticket->update($updated_params);

            // Optionally, return a response
            return response()->json([
                'successMessage' => 'Assing to updated successfully.',
                'support_ticket' => $support_ticket,
            ], 201);

        }

    public function destroy(SupportTicketCategory $category)
    {
        $category->delete();
        return response()->json([
            'successMessage' => 'category deleted successfully',
        ]);
    }   

     public function updateStatus(Request $request)
    {
        SupportTicket::where('id', $request->id)->update(['status'=> $request->status,'assign_to' => $request->employee_id]);

        return response()->json([
            'successMessage' => 'Ticket status updated successfully',
        ]);
    }

    public function getTicketCounts()
    {
        // Count tickets based on status
        $counts = [
            'open' => SupportTicket::where('status', 1)->count(),
            'acknowledge' => SupportTicket::where('status', 2)->count(),
            'closed' => SupportTicket::where('status', 3)->count(),
            'total' => SupportTicket::count()
        ];

        return response()->json($counts);
    }

   

}
