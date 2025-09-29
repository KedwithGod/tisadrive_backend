<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{

   
    public function userGeneralComplaint()
    {
        return Inertia::render('pages/user_complaint/general_complaint/index');
    }

    public function list()
    {
        // Get complaints where user_id is not null and order by created_at in descending order
        $results = Complaint::whereNotNull('user_id')->whereNull('request_id')
                    ->orderBy('created_at', 'desc')  // Order by created_at in descending order
                    ->paginate();
    
        return response()->json([
            'results' => $results->items(),  // Paginated items
            'paginator' => $results,         // Full pagination details (current page, total pages, etc.)
        ]);
    }
    
    public function taken(Complaint $complaint)
    {
        $complaint->update(['status'=>request()->status]);

        return response()->json([
            'successMessage' => 'user created successfully.',
        ], 201);
    }
    public function userRequestComplaint() {
        return Inertia::render('pages/user_complaint/request_complaint/index');
    }
    public function requestList()
    {
        // Get complaints where user_id is not null and order by created_at in descending order
        $results = Complaint::whereNotNull('user_id')->whereNotNull('request_id')
                    ->orderBy('created_at', 'desc')  // Order by created_at in descending order
                    ->paginate();
    
        return response()->json([
            'results' => $results->items(),  // Paginated items
            'paginator' => $results,         // Full pagination details (current page, total pages, etc.)
        ]);
    }  

    
    public function driverGeneralComplaint() {
        return Inertia::render('pages/driver_complaint/general_complaint/index');
    }


    public function driverList()
    {
        // Get complaints where user_id is not null and order by created_at in descending order
        $results = Complaint::whereNotNull('driver_id')->whereNull('request_id')
                    ->orderBy('created_at', 'desc')  // Order by created_at in descending order
                    ->paginate();
    
        return response()->json([
            'results' => $results->items(),  // Paginated items
            'paginator' => $results,         // Full pagination details (current page, total pages, etc.)
        ]);
    }

    public function driverRequestComplaint() {
        return Inertia::render('pages/driver_complaint/request_complaint/index');
    }

//driverRequestList
    public function driverRequestList()
    {
        // Get complaints where user_id is not null and order by created_at in descending order
        $results = Complaint::whereNotNull('driver_id')->whereNotNull('request_id')
                    ->orderBy('created_at', 'desc')  // Order by created_at in descending order
                    ->paginate();

        return response()->json([
            'results' => $results->items(),  // Paginated items
            'paginator' => $results,         // Full pagination details (current page, total pages, etc.)
        ]);
    }  
    public function ownerGeneralComplaint() {
        return Inertia::render('pages/owner_complaint/general_complaint/index');
    }
    public function ownerList()
    {
        // Get complaints where user_id is not null and order by created_at in descending order
        $results = Complaint::whereNotNull('owner_id')->whereNull('request_id')
                    ->orderBy('created_at', 'desc')  // Order by created_at in descending order
                    ->paginate();
    
        return response()->json([
            'results' => $results->items(),  // Paginated items
            'paginator' => $results,         // Full pagination details (current page, total pages, etc.)
        ]);
    }


    public function ownerRequestComplaint()
    {
        return Inertia::render('pages/owner_complaint/request_complaint/index');
    }
    // /OwnerRequestList
    public function OwnerRequestList()
    {
        // Get complaints where user_id is not null and order by created_at in descending order
        $results = Complaint::whereNotNull('owner_id')->whereNotNull('request_id')
                    ->orderBy('created_at', 'desc')  // Order by created_at in descending order
                    ->paginate();

        return response()->json([
            'results' => $results->items(),  // Paginated items
            'paginator' => $results,         // Full pagination details (current page, total pages, etc.)
        ]);
    }      
}
