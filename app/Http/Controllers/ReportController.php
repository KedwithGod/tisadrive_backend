<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\DriversExport;
use App\Exports\OwnersExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin\Driver;
use App\Models\Admin\Fleet;
use Carbon\Carbon;
use App\Models\Admin\VehicleType;
use App\Http\Controllers\Web\BaseController;
use App\Models\Admin\Owner;
use App\Models\Admin\ServiceLocation;
use App\Exports\RequestExport;
use App\Exports\DutyExport;


use  App\Models\Request\Request as RequestModel;
use App\Transformers\Driver\DriverProfileTransformer;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    public function userReport() 
    {
        return Inertia::render('pages/user_report/index');
    }
    public function userReportDownload(Request $request)
    {
        // dd($request->all());

        $format = $request->input('file_format');
        $status = $request->input('select_status');
        $dateOption = $request->input('select_date_option');
        $dateRange = $request->input('date');
        // Query the User model based on the form input
        $query = User::belongsToRole('user');

        if ($status !== null) {
            if($status!=="2")
            {
                $query->where('active', $status);

            }else{
            // dd($status);

                $query->where('is_deleted_at', "!=", null);

            }
        }

        if ($dateOption === 'date' && $dateRange) {
            $dates = explode(' to ', $dateRange);
            // Convert the dates to Y-m-d format
            $startDate = Carbon::createFromFormat('d M, Y', $dates[0])->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d M, Y', $dates[1])->format('Y-m-d');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($dateOption === 'today') {
            $query->whereDate('created_at', today()->format('Y-m-d'));
        } elseif ($dateOption === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($dateOption === 'this_month') {
            $query->whereMonth('created_at', now()->month);
            $query->whereYear('created_at', now()->year);
        } elseif ($dateOption === 'this_year') {
            $query->whereYear('created_at', now()->year);
        }

        $users = $query->get();

        if ($format === 'xlsx') {
            return Excel::download(new UsersExport($users), 'users.xlsx');
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('users.pdf', ['results' => $users]);
            return $pdf->download('users.pdf');
        }

        if ($format === 'csv') {
            return Excel::download(new UsersExport($users), 'users.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return response()->json(['error' => 'Invalid format'], 400);

    }
    public function driverReport() 
    {
       return Inertia::render('pages/driver_report/index');
    }
    public function driverReportDownload(Request $request)
    {
        // Gather the input data
        $vehicleTypes = $request->input('vehicle_types', []); // Array of vehicle types
        $format = $request->input('file_format');
        $status = $request->input('select_status');
        $dateOption = $request->input('select_date_option');
        $dateRange = $request->input('date');
        $transportType = $request->input('select_transport_type');
    // dd($dateOption);
        // Initialize the query
        $query = Driver::query();
    
        // Filter by vehicle types
        if (!empty($vehicleTypes)) {
            $query->whereHas('driverVehicleTypeDetail', function ($q) use ($vehicleTypes) {
                $q->whereIn('vehicle_type', $vehicleTypes);
            });
        }
    
        // Filter by transport type
        if (!is_null($transportType)) {
            $query->where('transport_type', $transportType);
        }
    
        // Filter by status
        if (!is_null($status)) {
            $query->where('approve', $status);
        }
    
        // Filter by date option or custom date range
        if (!empty($dateOption) || !empty($dateRange)) {
            $today = now();
            $to = now()->endOfDay()->toDateTimeString();
            $from = null;
    
            if ($dateOption == 'today') {
                $from = $today->startOfDay()->toDateTimeString();
            } elseif ($dateOption == 'this_week') {
                $from = $today->startOfWeek()->toDateTimeString();
            } elseif ($dateOption == 'this_month') {
                $from = $today->startOfMonth()->toDateTimeString();
            } elseif ($dateOption == 'this_year') {
                $from = $today->startOfYear()->toDateTimeString();
            } elseif (!empty($dateRange)) {
                $date = explode(' to ', $dateRange);
                if (count($date) == 2) {
                    $from = Carbon::createFromFormat('d M, Y', $date[0])->startOfDay()->toDateTimeString();
                    $to = Carbon::createFromFormat('d M, Y', $date[1])->endOfDay()->toDateTimeString();
                }
            }
    
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }
    
        // Apply sorting (you can customize this as per your requirement)
        $query->orderBy('created_at', 'desc');
    
        // Get the filtered data
        $drivers = $query->get();
    
        // Handle file export based on the selected format
        if ($format === 'xlsx') {
            return Excel::download(new DriversExport($drivers), 'drivers.xlsx');
        }
    
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('drivers.pdf', ['results' => $drivers]);
            return $pdf->download('drivers.pdf');
        }
    
        if ($format === 'csv') {
            return Excel::download(new DriversExport($drivers), 'drivers.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    
        return response()->json(['error' => 'Invalid format'], 400);
    }
    
    
    public function getVehicleTypes(Request $request)
    {
        $transportType = $request->query('transport_type');

        $vehicleTypes = VehicleType::where('is_taxi', $transportType)->get();

        return response()->json($vehicleTypes);
    }

    public function ownerReport() 
    {
        $serviceLocations= ServiceLocation::get();

        // dd($serviceLocations);

        return Inertia::render('pages/owner_report/index',['serviceLocations'=>$serviceLocations]);
    }

    public function fleetReport() 
    {
        $owners= Owner::where('approve', true)->get();
      
        if(auth()->user()->hasRole('owner'))
        {
            $owners= Owner::where('user_id', auth()->user()->id)->get();

        }

        return Inertia::render('pages/fleet_report/index',['owners'=>$owners]);
    }
    public function listFleet()
    {

        $owner = Owner::find(request()->owner_id);

        $fleets = Fleet::where('owner_id', $owner->user->id)->get();

        // dd($fleets);

        return response()->json(['fleets' => $fleets]);

        // dd("dfsds");
    }

    public function fleetReportDownload(Request $request)
    {
        // Gather the input data
        $format = $request->input('file_format');
        $payment_type = $request->input('select_payment_type');
        $dateOption = $request->input('select_date_option');
        $dateRange = $request->input('date');
        $fleet_id = $request->input('fleet_id'); // Array of vehicle types
    
        $trip_status = $request->input('trip_status');
    
        // Initialize the query
        $query = RequestModel::where('fleet_id', $fleet_id)->where('owner_id', $request->input('owner_id'));
        
        // Filter by status
        if (!is_null($trip_status)) {
            if ($trip_status == 0) {
                $query->where('is_completed', 1);
            } else {
                $query->where('is_cancelled', 1);
            }
        }
    
        // Filter by date option or custom date range
        if (!empty($dateOption) || !empty($dateRange)) {
            $today = now();
            $to = $today->endOfDay()->toDateTimeString();
            $from = null;
    
            if ($dateOption === 'today') {
                $from = $today->startOfDay()->toDateTimeString();
            } elseif ($dateOption === 'this_week') {
                $from = $today->startOfWeek()->toDateTimeString();
            } elseif ($dateOption === 'this_month') {
                $from = $today->startOfMonth()->toDateTimeString();
            } elseif ($dateOption === 'this_year') {
                $from = $today->startOfYear()->toDateTimeString();
            } elseif (!empty($dateRange)) {
                $date = explode(' to ', $dateRange);
                if (count($date) === 2) {
                    try {
                        $from = Carbon::createFromFormat('d M, Y', $date[0])->startOfDay()->toDateTimeString();
                        $to = Carbon::createFromFormat('d M, Y', $date[1])->endOfDay()->toDateTimeString();
                    } catch (\Exception $e) {
                        return response()->json(['error' => 'Invalid date format'], 400);
                    }
                }
            }
    
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }
    
        // Apply sorting (you can customize this as per your requirement)
        $query->orderBy('created_at', 'desc');
    
        // Get the filtered data
        $requests = $query->get();
    
        // Handle file export based on the selected format
        if ($format === 'xlsx') {
            return Excel::download(new RequestExport($requests), 'requests.xlsx');
        }
    
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('requests.pdf', ['results' => $requests]);
            return $pdf->download('requests.pdf');
        }
    
        if ($format === 'csv') {
            return Excel::download(new RequestExport($requests), 'requests.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    
        return response()->json(['error' => 'Invalid format'], 400);
    }
    

    public function ownerReportDownload(Request $request)
    {
        // dd($request->all());
        // Gather the input data
        $serviceLocations = $request->input('service_locations', []); // Array of vehicle types
        $format = $request->input('file_format');
        $status = $request->input('select_status');
        $dateOption = $request->input('select_date_option');
        $dateRange = $request->input('date');
    // dd($dateOption);
        // Initialize the query
        $query = Owner::query();
    
        // Filter by vehicle types
        if (!empty($serviceLocations)) {
            $query->whereHas('area', function ($q) use ($serviceLocations) {
                $q->whereIn('service_location_id', $serviceLocations);
            });
        }
    
        // Filter by transport type
        // if (!is_null($transportType)) {
        //     $query->where('transport_type', $transportType);
        // }
    
        // Filter by status
        if (!is_null($status)) {
            $query->where('approve', $status);
        }
    
        // Filter by date option or custom date range
        if (!empty($dateOption) || !empty($dateRange)) {
            $today = now();
            $to = now()->endOfDay()->toDateTimeString();
            $from = null;
    
            if ($dateOption == 'today') {
                $from = $today->startOfDay()->toDateTimeString();
            } elseif ($dateOption == 'this_week') {
                $from = $today->startOfWeek()->toDateTimeString();
            } elseif ($dateOption == 'this_month') {
                $from = $today->startOfMonth()->toDateTimeString();
            } elseif ($dateOption == 'this_year') {
                $from = $today->startOfYear()->toDateTimeString();
            } elseif (!empty($dateRange)) {
                $date = explode(' to ', $dateRange);
                if (count($date) == 2) {
                    $from = Carbon::createFromFormat('d M, Y', $date[0])->startOfDay()->toDateTimeString();
                    $to = Carbon::createFromFormat('d M, Y', $date[1])->endOfDay()->toDateTimeString();
                }
            }
    
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }
    
        // Apply sorting (you can customize this as per your requirement)
        $query->orderBy('created_at', 'desc');
    
        // Get the filtered data
        $owners = $query->get();
    
        // Handle file export based on the selected format
        if ($format === 'xlsx') {
            return Excel::download(new OwnersExport($owners), 'owners.xlsx');
        }
    
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('owners.pdf', ['results' => $owners]);
            return $pdf->download('owners.pdf');
        }
    
        if ($format === 'csv') {
            return Excel::download(new OwnersExport($owners), 'owners.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    
        return response()->json(['error' => 'Invalid format'], 400);
    }
    
    public function financeReport() 
    {
        $vehicle_type = VehicleType::get();

        return Inertia::render('pages/finance_report/index',['vehicleType'=>$vehicle_type]);
    }
    public function financeReportDownload(Request $request)
    {
        // dd($request->all());
        // Gather the input data
        $format = $request->input('file_format');
        $payment_type = $request->input('select_payment_type');
        $dateOption = $request->input('select_date_option');
        $dateRange = $request->input('date');
        $vehicle_types = $request->input('vehicle_types', []); // Array of vehicle types

        $trip_status = $request->input('trip_status');


    // dd($dateOption);
        // Initialize the query
        $query = RequestModel::query();
    

        // Filter by vehicle types
        if (!empty($vehicle_types)) {
            $query->whereHas('zoneType', function ($query) use ($vehicle_types) {
                $query->whereIn('type_id', $vehicle_types);
            });
            
        }

           // Filter by status
        if (!empty($payment_type)) {
            $query->whereIn('payment_opt', $payment_type);
        }
        // Filter by status
        if (!is_null($trip_status)) 
        {
            if($trip_status==0)
            {
                // dd("status 0");
                $query->where('is_completed',1);
            }else{
                // dd("status 1");

                $query->where('is_cancelled', 1);

            }
        }
    
        // Filter by date option or custom date range
        if (!empty($dateOption) || !empty($dateRange)) {
            $today = now();
            $to = now()->endOfDay()->toDateTimeString();
            $from = null;
    
            if ($dateOption == 'today') {
                $from = $today->startOfDay()->toDateTimeString();
            } elseif ($dateOption == 'this_week') {
                $from = $today->startOfWeek()->toDateTimeString();
            } elseif ($dateOption == 'this_month') {
                $from = $today->startOfMonth()->toDateTimeString();
            } elseif ($dateOption == 'this_year') {
                $from = $today->startOfYear()->toDateTimeString();
            } elseif (!empty($dateRange)) {
                $date = explode(' to ', $dateRange);
                if (count($date) == 2) {
                    $from = Carbon::createFromFormat('d M, Y', $date[0])->startOfDay()->toDateTimeString();
                    $to = Carbon::createFromFormat('d M, Y', $date[1])->endOfDay()->toDateTimeString();
                }
            }
    
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }
    
        // Apply sorting (you can customize this as per your requirement)
        $query->orderBy('created_at', 'desc');
    
        // Get the filtered data
        $requests = $query->get();
    
        // Handle file export based on the selected format
        if ($format === 'xlsx') {
            return Excel::download(new RequestExport($requests), 'requests.xlsx');
        }
    
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('requests.pdf', ['results' => $requests]);
            return $pdf->download('requests.pdf');
        }
    
        if ($format === 'csv') {
            return Excel::download(new RequestExport($requests), 'requests.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    
        return response()->json(['error' => 'Invalid format'], 400);
    }
    public function driverDutyReport() 
    {
        $serviceLocations= ServiceLocation::get();

       return Inertia::render('pages/driver_report/duty-index',['serviceLocations'=>$serviceLocations]);
    }

    public function getDrivers(Request $request)
    {
           $service_location_id = $request->query('service_location_id');

            $drivers = Driver::where('service_location_id', $service_location_id)
            ->select('id', 'name')->get();

            return response()->json(['drivers' => $drivers]);

    }
    
    
    public function driverDutyReportDownload(Request $request)
    {
    // Validate the incoming request
    $request->validate([
        'driver_id' => 'required|integer',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date', // Ensure to_date is not before from_date
    ]);

    $format = $request->file_format;

    // Retrieve the input values
    $driverId = $request->input('driver_id');
    $fromDate = $request->input('from_date'); 
    $toDate = $request->input('to_date');

    // Optional: Convert the dates to a Carbon instance (if you need to perform any date manipulations)
    $fromDate = Carbon::parse($fromDate)->toDateString(); // Ensure it's in YYYY-MM-DD format
    $toDate = Carbon::parse($toDate)->toDateString(); // Ensure it's in YYYY-MM-DD format

    // Call the stored procedure
    $results = DB::select('CALL GetDriverOnlineOfflineDuration(?, ?, ?)', [$driverId, $fromDate, $toDate]);

    // dd($results);

        // Handle file export based on the selected format
        if ($format === 'xlsx') {
            return Excel::download(new DutyExport($results), 'results.xlsx');
        }
    
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('driver_duty.pdf', ['results' => $results]);
            return $pdf->download('driver_duty.pdf');
        }
    
        if ($format === 'csv') {
            return Excel::download(new DutyExport($results), 'results.csv', \Maatwebsite\Excel\Excel::CSV);
        }
    
        return response()->json(['error' => 'Invalid format'], 400);

    }
    // function downloadInvoice(RequestModel $request_detail, Request $request) {
    //     if($request->invoice_type == "user") {

    //         $data = $request_detail;

    //         return view('emails.invoice',compact('data'));
    //     }elseif($request->invoice_type == "driver"){
            
    //         $data = $request_detail;

    //         return view('emails.driver_invoice',compact('data'));
    //     }
    //     return response()->json(['error' => 'Invalid format'], 400);

    // }

    function downloadInvoice(RequestModel $request_detail, Request $request) {
        if ($request->invoice_type == "user") {
            // Prepare data for user invoice
            $data = $request_detail;
    
            // Generate PDF for user invoice
            $pdf = Pdf::loadView('emails.invoice', compact('data'));
    dd($pdf);
            // Return the PDF as a download
            return $pdf->download('user_invoice.pdf');
        } elseif ($request->invoice_type == "driver") {
            // Prepare data for driver invoice
            $data = $request_detail;
    
            // Generate PDF for driver invoice
            $pdf = Pdf::loadView('emails.driver_invoice', compact('data'));
    
            // Return the PDF as a download
            return $pdf->download('driver_invoice.pdf');
        }
    
        // Handle invalid invoice type
        return response()->json(['error' => 'Invalid invoice type'], 400);
    }


}
