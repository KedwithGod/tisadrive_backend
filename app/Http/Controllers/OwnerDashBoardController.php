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
use App\Models\Admin\Fleet;
use App\Models\Admin\Owner;
use App\Models\Admin\ServiceLocation;
use Illuminate\Support\Facades\DB;

class OwnerDashBoardController extends BaseController
{
    public function index() 
    {
        
        if(auth()->user()->hasRole('owner'))
        {
            $owner = auth()->user()->owner;

            return $this->IndividualDashboard();

        }
        // card Datas 
        $total_owners = Owner::selectRaw('
                                        IFNULL(SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END),0) AS approved,
                                        IFNULL((SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS approve_percentage,
                                        IFNULL((SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS decline_percentage,
                                        IFNULL(SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END),0) AS declined,
                                        count(*) AS total
                                    ')
                                ->whereHas('user', function ($query) {
                                    $query->companyKey();
                                });

        $total_owners = $total_owners->first();

        $total_fleets = Fleet::selectRaw('
                                        IFNULL(SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END),0) AS approved,
                                        IFNULL((SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS approve_percentage,
                                        IFNULL((SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS decline_percentage,
                                        IFNULL(SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END),0) AS declined,
                                        count(*) AS total
                                    ')
                                ->whereHas('user', function ($query) {
                                    $query->companyKey();
                                });

        $total_fleets = $total_fleets->first();

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

        $total_drivers = $total_drivers->whereNotNull('owner_id')->first();



// dd($total_fleets);
        $firebaseSettings = [
            'firebase_api_key' => get_firebase_settings('firebase_api_key'),
            'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
            'firebase_database_url' => get_firebase_settings('firebase_database_url'),
            'firebase_project_id' => get_firebase_settings('firebase_project_id'),
            'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
            'firebase_app_id' => get_firebase_settings('firebase_app_id'),
        ];

        $today = date('Y-m-d');
        // card Datas 
        $driver_ids = Driver::whereNotNull('owner_id')->pluck('id');     
       
        //Today Earnings && today trips
        $cardEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=0,request_bills.total_amount,0)),0)";
        $cashEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=1,request_bills.total_amount,0)),0)";
        $walletEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=2,request_bills.total_amount,0)),0)";
        $adminCommissionQuery = "IFNULL(SUM(request_bills.admin_commision_with_tax),0)";
        $driverCommissionQuery = "IFNULL(SUM(request_bills.driver_commision),0)";
        $totalEarningsQuery = "$cardEarningsQuery + $cashEarningsQuery + $walletEarningsQuery";

        $todayEarnings = Request::leftJoin('request_bills', 'requests.id', '=', 'request_bills.request_id')
                        ->selectRaw("
                            {$cardEarningsQuery} AS card,
                            {$cashEarningsQuery} AS cash,
                            {$walletEarningsQuery} AS wallet,
                            {$totalEarningsQuery} AS total,
                            {$adminCommissionQuery} AS admin_commision,
                            {$driverCommissionQuery} AS driver_commision
                        ")
                        ->companyKey()
                        ->whereNotNull('requests.owner_id')
                        ->where('requests.is_completed', true)
                        ->whereDate('requests.trip_start_time', date('Y-m-d'))
                        ->first();
    

        $todayTrips = Request::companyKey()
                                    ->whereDate('created_at', $today)
                                    ->whereNotNull('owner_id')
                                    ->selectRaw('
                                        IFNULL(SUM(CASE WHEN is_completed=1 THEN 1 ELSE 0 END), 0) AS today_completed,
                                        IFNULL(SUM(CASE WHEN is_completed=0 AND is_cancelled=0 THEN 1 ELSE 0 END), 0) AS today_scheduled,
                                        IFNULL(SUM(CASE WHEN is_cancelled=1 THEN 1 ELSE 0 END), 0) AS today_cancelled
                                    ')
                                    ->first();        


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
                            ->whereNotNull('requests.owner_id')
                            ->where('requests.is_completed',true)
                            ->first();


                            $startDate = Carbon::now()->startOfYear(); // Start of the current year (January 1st)
                            $endDate = Carbon::now();
                            $earningsData=[];

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
                $a[] = Request::whereNotNull('owner_id')->whereBetween('created_at', [$from, $to])->where('cancel_method', '0')->whereIsCancelled(true)->count();
                $u[] = Request::whereNotNull('owner_id')->whereBetween('created_at', [$from, $to])->where('cancel_method', '1')->whereIsCancelled(true)->count();
                $d[] = Request::whereNotNull('owner_id')->whereBetween('created_at', [$from, $to])->where('cancel_method', '2')->whereIsCancelled(true)->count();
            
                $earningsData['earnings']['months'][] = $monthName;
                $earningsData['earnings']['values'][] = RequestBill::whereHas('requestDetail', function ($query) use ($from,$to) {
                                    $query->whereNotNull('owner_id')->whereBetween('trip_start_time', [$from,$to])->whereIsCompleted(true);
                                })->sum('total_amount');

                $startDate->addMonth();
            }
        $currency_code = get_settings('currency_code');
        $currency_symbol = get_settings('currency_symbol');
      
   
        // Query to calculate Fleet earnings for each fleet
        $fleetsEarnings = DB::table('fleets')
        ->join('requests', 'fleets.id', '=', 'requests.fleet_id')
        ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
        ->join('vehicle_types', 'fleets.vehicle_type', '=', 'vehicle_types.id')
        ->leftJoin('request_ratings', 'requests.id', '=', 'request_ratings.request_id')
        ->select(
            'fleets.id as fleet_id',
            'fleets.license_number',
            'vehicle_types.name as vehicle_type_name',
            DB::raw('FORMAT(SUM(request_bills.total_amount), 2) as total_earnings'),
            DB::raw('FORMAT(SUM(request_bills.driver_commision), 2) as total_driver_earnings'),
            DB::raw('FORMAT(SUM(request_bills.admin_commision), 2) as total_admin_earnings'),
            DB::raw('COUNT(requests.id) as total_trips'),
            DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
            DB::raw('FORMAT(AVG(request_ratings.user_rating), 2) as average_user_rating')
        )
        ->groupBy('fleets.id', 'fleets.license_number', 'vehicle_types.name')
        ->get();

// Driver earning by fleet
        // Query to calculate Fleet earnings for each fleet
        $fleetDriverEarnings = DB::table('drivers')
        ->join('requests', 'drivers.id', '=', 'requests.driver_id')
        ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
        ->leftJoin('request_ratings', 'requests.id', '=', 'request_ratings.request_id')
        ->whereNotNull('drivers.owner_id') // Add this line to filter drivers with non-null owner_id
        ->select(
            'drivers.id as driver_id',
            'drivers.name',
            DB::raw('FORMAT(SUM(request_bills.total_amount), 2) as total_earnings'),
            DB::raw('FORMAT(SUM(request_bills.driver_commision), 2) as total_driver_earnings'),
            DB::raw('FORMAT(SUM(request_bills.admin_commision), 2) as total_admin_earnings'),
            DB::raw('COUNT(requests.id) as total_trips'),
            DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
            DB::raw('FORMAT(AVG(request_ratings.user_rating), 2) as average_user_rating')
        )
        ->groupBy('drivers.id', 'drivers.name')
        ->get();


// dd($fleetDriverEarnings);
// dd($cancelData);

        return Inertia::render('pages/owner-dashboard/index', [
            'driverIds' => $driver_ids,
            'firebaseSettings'=>$firebaseSettings,
            'earningsData' => $earningsData,
            'currency_code' => $currency_code,
            'currencySymbol' => $currency_symbol,
            'todayTrips' => $todayTrips,
            'fleetsEarnings' => $fleetsEarnings,
            'todayEarnings' => $todayEarnings,
            'overallEarnings' => $overallEarnings,
            'fleetDriverEarnings' => $fleetDriverEarnings,
            'total_owners'=> $total_owners,
            'total_fleets'=> $total_fleets,
            'total_drivers'=> $total_drivers,

        ]);
    }

//IndividualDashboard

        public function IndividualDashboard() 
        {
            
            if(auth()->user()->hasRole('owner'))
            {
                $owner = auth()->user()->owner;

            }

        $total_fleets = Fleet::selectRaw('
                                        IFNULL(SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END),0) AS approved,
                                        IFNULL((SUM(CASE WHEN approve=1 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS approve_percentage,
                                        IFNULL((SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END) / count(*)),0) * 100 AS decline_percentage,
                                        IFNULL(SUM(CASE WHEN approve=0 THEN 1 ELSE 0 END),0) AS declined,
                                        count(*) AS total
                                    ')
                                ->whereHas('user', function ($query) {
                                    $query->companyKey();
                                });
        $total_fleets = $total_fleets->where('owner_id', $owner->user_id)->first();

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

        $total_drivers = $total_drivers->where('owner_id', $owner->id)->first();


            // dd($owner);
            $firebaseSettings = [
                'firebase_api_key' => get_firebase_settings('firebase_api_key'),
                'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
                'firebase_database_url' => get_firebase_settings('firebase_database_url'),
                'firebase_project_id' => get_firebase_settings('firebase_project_id'),
                'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
                'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
                'firebase_app_id' => get_firebase_settings('firebase_app_id'),
            ];

            $today = date('Y-m-d');
            // card Datas 
            
            $driver_ids = Driver::where('owner_id', $owner->id)->pluck('id');     
            
        
            $fire_base_driver_ids = Driver::where('owner_id', $owner->id)
            ->pluck('id')
            ->map(function ($id) {
                return 'driver_' . $id;
            });
        // dd($fire_base_driver_ids);

            //Today Earnings && today trips
            $cardEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=0,request_bills.total_amount,0)),0)";
            $cashEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=1,request_bills.total_amount,0)),0)";
            $walletEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=2,request_bills.total_amount,0)),0)";
            $adminCommissionQuery = "IFNULL(SUM(request_bills.admin_commision_with_tax),0)";
            $driverCommissionQuery = "IFNULL(SUM(request_bills.driver_commision),0)";
            $totalEarningsQuery = "$cardEarningsQuery + $cashEarningsQuery + $walletEarningsQuery";

            $todayEarnings = Request::leftJoin('request_bills', 'requests.id', '=', 'request_bills.request_id')
                            ->selectRaw("
                                {$cardEarningsQuery} AS card,
                                {$cashEarningsQuery} AS cash,
                                {$walletEarningsQuery} AS wallet,
                                {$totalEarningsQuery} AS total,
                                {$adminCommissionQuery} AS admin_commision,
                                {$driverCommissionQuery} AS driver_commision
                            ")
                            ->companyKey()
                            ->where('owner_id', $owner->id)
                            ->where('requests.is_completed', true)
                            ->whereDate('requests.trip_start_time', date('Y-m-d'))
                            ->first();


            $todayTrips = Request::companyKey()
                                        ->whereDate('created_at', $today)
                                        ->where('owner_id', $owner->id)
                                        ->selectRaw('
                                            IFNULL(SUM(CASE WHEN is_completed=1 THEN 1 ELSE 0 END), 0) AS today_completed,
                                            IFNULL(SUM(CASE WHEN is_completed=0 AND is_cancelled=0 THEN 1 ELSE 0 END), 0) AS today_scheduled,
                                            IFNULL(SUM(CASE WHEN is_cancelled=1 THEN 1 ELSE 0 END), 0) AS today_cancelled
                                        ')
                                        ->first();        


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
                                ->where('requests.owner_id', $owner->id)
                                ->where('requests.is_completed',true)
                                ->first();


                                $startDate = Carbon::now()->startOfYear(); // Start of the current year (January 1st)
                                $endDate = Carbon::now();
                                $earningsData=[];

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
                    $a[] = Request::where('owner_id', $owner->id)->whereBetween('created_at', [$from, $to])->where('cancel_method', '0')->whereIsCancelled(true)->count();
                    $u[] = Request::where('owner_id', $owner->id)->whereBetween('created_at', [$from, $to])->where('cancel_method', '1')->whereIsCancelled(true)->count();
                    $d[] = Request::where('owner_id', $owner->id)->whereBetween('created_at', [$from, $to])->where('cancel_method', '2')->whereIsCancelled(true)->count();
                
                    $earningsData['earnings']['months'][] = $monthName;
                    $earningsData['earnings']['values'][] = RequestBill::whereHas('requestDetail', function ($query) use ($from,$to, $owner) {
                                        $query->where('owner_id', $owner->id)->whereBetween('trip_start_time', [$from,$to])->whereIsCompleted(true);
                                    })->sum('total_amount');

                    $startDate->addMonth();
                }
            $currency_code = get_settings('currency_code');
            $currency_symbol = get_settings('currency_symbol');

            // Query to calculate Fleet earnings for each fleet
            $fleetsEarnings = DB::table('fleets')
            ->join('requests', 'fleets.id', '=', 'requests.fleet_id')
            ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
            ->join('vehicle_types', 'fleets.vehicle_type', '=', 'vehicle_types.id')
            ->leftJoin('request_ratings', 'requests.id', '=', 'request_ratings.request_id')
            ->where('fleets.owner_id', $owner->user_id) // Assuming this is the correct field name
            ->select(
                'fleets.id as fleet_id',
                'fleets.license_number',
                'vehicle_types.name as vehicle_type_name',
                DB::raw('SUM(request_bills.total_amount) as total_earnings'),
                DB::raw('SUM(request_bills.driver_commision) as total_driver_earnings'),
                DB::raw('SUM(request_bills.admin_commision) as total_admin_earnings'),
                DB::raw('COUNT(requests.id) as total_trips'),
                DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
                DB::raw('AVG(COALESCE(request_ratings.user_rating, 0)) as average_user_rating')
            )
            ->groupBy('fleets.id', 'fleets.license_number', 'vehicle_types.name')
            ->get();
        

        // Driver earning by fleet
            // Query to calculate Fleet earnings for each fleet
            $fleetDriverEarnings = DB::table('drivers')
            ->join('requests', 'drivers.id', '=', 'requests.driver_id')
            ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
            ->leftJoin('request_ratings', 'requests.id', '=', 'request_ratings.request_id')
            ->where('drivers.owner_id', $owner->id) // Assuming this is the correct field name
            ->select(
                'drivers.id as driver_id',
                'drivers.name',
                DB::raw('FORMAT(SUM(request_bills.total_amount), 2) as total_earnings'),
                DB::raw('FORMAT(SUM(request_bills.driver_commision), 2) as total_driver_earnings'),
                DB::raw('FORMAT(SUM(request_bills.admin_commision), 2) as total_admin_earnings'),
                DB::raw('COUNT(requests.id) as total_trips'),
                DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
                DB::raw('FORMAT(AVG(request_ratings.user_rating), 2) as average_user_rating')
            )
            ->groupBy('drivers.id', 'drivers.name')
            ->get();

            $map_key = get_map_settings('google_map_key');

        // dd($fleetDriverEarnings);
        // dd($driver_ids);

            return Inertia::render('pages/owner-dashboard/individual-index', [
                'fire_base_driver_ids'=>$fire_base_driver_ids,
                'driverIds' => $driver_ids,
                'firebaseSettings'=>$firebaseSettings,
                'earningsData' => $earningsData,
                'currency_code' => $currency_code,
                'currencySymbol' => $currency_symbol,
                'todayTrips' => $todayTrips,
                'fleetsEarnings' => $fleetsEarnings,
                'todayEarnings' => $todayEarnings,
                'overallEarnings' => $overallEarnings,
                'fleetDriverEarnings' => $fleetDriverEarnings,
                'total_fleets'=> $total_fleets,
                'total_drivers'=> $total_drivers,
                'map_key'=>$map_key,
                'default_lat'=>get_settings('default_latitude'),
                'default_lng'=>get_settings('default_longitude'),
            ]);
        }


}
