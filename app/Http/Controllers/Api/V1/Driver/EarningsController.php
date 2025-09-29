<?php

namespace App\Http\Controllers\Api\V1\Driver;

use Carbon\Carbon;
use App\Models\Request\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Request\RequestBill;
use App\Models\Admin\DriverAvailability;
use App\Base\Constants\Master\PaymentType;
use App\Http\Controllers\Api\V1\BaseController;
use App\Base\Constants\Setting\Settings;
use App\Base\Constants\Auth\Role;
use Illuminate\Http\Request as ValidatorRequest;
use Kreait\Firebase\Contract\Database;
use Sk\Geohash\Geohash;
use App\Transformers\Driver\LeaderBoardEarningsTransformer;
use App\Transformers\Driver\LeaderBoardTripsTransformer;
use App\Models\Request\DriverRejectedRequest;
use App\Models\Request\RequestRating;
use App\Transformers\Requests\TripRequestTransformer;
use App\Models\Admin\Driver;
use Log;
use App\Transformers\Requests\EarningTripRequestTransformer;
/**
 * @group Driver Earnings
 * @authenticated
 * APIs for Driver's Earnings
 */
class EarningsController extends BaseController
{
    protected $request;

    protected $database;


    public function __construct(Request $request,Database $database)
    {
        $this->request = $request;
        $this->database = $database;
        
    }


    /**
     * List New Earnings
     * @response
     * {
     *     "success": true,
     *     "message": "earnings_listed_successfully",
     *     "currency_symbol": "₹",
     *     "earnings": [
     *         {
     *             "from_date": "18-Nov-24",
     *             "to_date": "24-Nov-24",
     *             "total_amount": 0,
     *             "total_trips": 0,
     *             "total_wallet_amount": 0,
     *             "total_cash_amount": 0,
     *             "total_logged_in_hours": "0 Mins",
     *             "dates": {
     *                 "Mon-18": 0,
     *                 "Tue-19": 0,
     *                 "Wed-20": 0,
     *                 "Thu-21": 0,
     *                 "Fri-22": 0,
     *                 "Sat-23": 0,
     *                 "Sun-24": 0
     *             }
     *         }
     *     ]
     * }
     * */
    public function newEarnings()
    {

        $driver = auth()->user()->driver;
      
        $owner = auth()->user()->owner;


        // Get the first day of the current month, then subtract 3 months to get the starting point.
        $threeMonthsAgo = Carbon::now()->subMonths(3)->startOfMonth();
        if(auth()->user()->hasRole('driver'))
        {
            // Query to get the first record from the last 3 months.
            $firstRecord = $this->request->where('driver_id', $driver->id)
            ->where('is_completed', 1)->where('created_at', '>=', $threeMonthsAgo)
            ->orderBy('created_at', 'asc')
            ->first();
        }else{

                  // Query to get the first record from the last 3 months.
                $firstRecord = $this->request->where('owner_id', $owner->id)
                ->where('is_completed', 1)->where('created_at', '>=', $threeMonthsAgo)
                ->orderBy('created_at', 'asc')
                ->first();  
        }



        $currency_symbol = auth()->user()->countryDetail->currency_symbol;

        // Define the response structure
        $response = [
        'success' => true,
        'message' => 'earnings_listed_successfully',
        'currency_symbol'=>$currency_symbol,
        'earnings' => [],
        ];



        $weeks = [];
        if($firstRecord){

        $date = Carbon::parse($firstRecord->updated_at); // Replace with your specific date

        }else{

        $date = Carbon::now();

        }
        $i = 0;
        do {
        $startOfWeek = Carbon::now()->startOfWeek()->subWeeks($i);
        $endOfWeek = Carbon::now()->endOfWeek()->subWeeks($i);

        $weeks[] = [
        'start' => $startOfWeek->format('Y-m-d'),
        'end' => $endOfWeek->format('Y-m-d')
        ];

        $i++;
        } while ($startOfWeek->greaterThanOrEqualTo($date));



        // Iterate over the weeks (assuming you have multiple weeks to process)
        foreach ($weeks as $week) {
        // Calculate the start and end of the week
        $startOfWeek = Carbon::parse($week['start']);
        $endOfWeek = Carbon::parse($week['end']);

        // Format the dates for the response
        $fromDate = $startOfWeek->format('d-M-y');
        $toDate = $endOfWeek->format('d-M-y');

        if(auth()->user()->hasRole('driver'))
        {
        // Calculate total trips for the week
        $totalTrips = $this->request->where('driver_id', $driver->id)
        ->where('is_completed', 1)
        ->whereBetween('trip_start_time', [$startOfWeek, $endOfWeek])
        ->count();
        }else{
        // Calculate total trips for the week
        $totalTrips = $this->request->where('owner_id', $owner->id)
        ->where('is_completed', 1)
        ->whereBetween('trip_start_time', [$startOfWeek, $endOfWeek])
        ->count();
        }
        if(auth()->user()->hasRole('driver'))
        {
        $totalDuration = DB::table('driver_availabilities')->where('driver_id',$driver->id)
        ->whereBetween('online_at', [$startOfWeek, $endOfWeek])
        ->sum('duration');
        }else{
            $totalDuration = 0;
        }
        $duration_in_mins = (integer)$totalDuration;

        $hours = floor($duration_in_mins / 60); // Get the full hours

        $remainingMinutes = $duration_in_mins % 60; // Get the remaining minutes

        if ($hours > 0) {
        // If there are full hours, show both hours and minutes
        $total_hours_worked = sprintf('%d Hr %d Mins', $hours, $remainingMinutes);
        } else {
        // If less than an hour, show only minutes
        $total_hours_worked = sprintf('%d Mins', $remainingMinutes);
        }

        if(auth()->user()->hasRole('driver'))
        {
        // Calculate total earnings for the week
        $totalEarnings = RequestBill::whereHas('requestDetail', function ($query) use ($driver, $startOfWeek, $endOfWeek) {
        $query->where('driver_id', $driver->id)
        ->where('is_completed', 1)
        ->whereBetween('trip_start_time', [$startOfWeek, $endOfWeek]);
        })->sum('driver_commision');
        }else{
                    // Calculate total earnings for the week
        $totalEarnings = RequestBill::whereHas('requestDetail', function ($query) use ($owner, $startOfWeek, $endOfWeek) {
            $query->where('owner_id', $owner->id)
            ->where('is_completed', 1)
            ->whereBetween('trip_start_time', [$startOfWeek, $endOfWeek]);
            })->sum('driver_commision');
        }
        $weeks = [$startOfWeek, $endOfWeek];
        if(auth()->user()->hasRole('driver'))
        {
            // Calculate total wallet amount (example)
            $totalWalletAmount = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$weeks) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks); //cash
            $query->where(function($new_query){
            $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            });
            })->sum('driver_commision');
         }else{
             // Calculate total wallet amount (example)
             $totalWalletAmount = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$weeks) {
                $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks); //cash
                $query->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
                });
                })->sum('driver_commision');   
        }
        if(auth()->user()->hasRole('driver'))
        {
            //Total cash trip amount
            $totalCashAmount = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$weeks) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1'); //cash
            })->sum('driver_commision');
        }else{
            //Total cash trip amount
            $totalCashAmount = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$weeks) {
                $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1'); //cash
                })->sum('driver_commision');
        }


        // Calculate earnings per day in the week
        $weekDays = [];
        $weekDaysString = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat','Sun'];

        for ($i = 0; $i < 7; $i++) {
        $weekDate = Carbon::parse($startOfWeek)->addDay($i)->format('Y-m-d');
        if(auth()->user()->hasRole('driver'))
        {
        $weeklyTotalEarnings = RequestBill::whereHas('requestDetail', function ($query) use ($driver, $weekDate) {
        $query->where('driver_id', $driver->id)
        ->where('is_completed', 1)
        ->whereDate('trip_start_time', $weekDate);
        })->sum('driver_commision');
        }else{
            $weeklyTotalEarnings = RequestBill::whereHas('requestDetail', function ($query) use ($owner, $weekDate) {
                $query->where('owner_id', $owner->id)
                ->where('is_completed', 1)
                ->whereDate('trip_start_time', $weekDate);
                })->sum('driver_commision');
        }
        $dayName = $weekDaysString[$i] . '-' . Carbon::parse($weekDate)->format('d');
        $weekDays[$dayName] =  round($weeklyTotalEarnings, 2);
        }

        // Build the earnings array for the week
        $earnings = [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'total_amount' => round($totalEarnings, 2),        // Round to 2 decimal places
            'total_trips' => round($totalTrips),                // Round to nearest whole number
            'total_wallet_amount' => round($totalWalletAmount, 2), // Round to 2 decimal places
            'total_cash_amount' => round($totalCashAmount, 2),  // Round to 2 decimal places
            'total_logged_in_hours' => $total_hours_worked,     // Assuming this is already formatted, no rounding needed
            'dates' => $weekDays,                               // No rounding needed for dates
        ];
        

        // Add to the response
        $response['earnings'][] = $earnings;
        }

        return $response;
    }


    /**
     * Earnings By Date
     * 
     * @response 
     * {
     *     "success": true,
     *     "message": "daily_earnings_listed_successfully",
     *     "total_trips": 0,
     *     "total_trip_kms": 0,
     *     "total_hours_worked": "0 Mins",
     *     "currency_symbol": "₹",
     *     "requests": {
     *         "data": []
     *     }
     * }
     * 
     * */
    public function earningsByDate(ValidatorRequest $request)
    {
        // Validate Request id
        $request->validate([
            'date' => 'required|date',
        ]);
    
        if (auth()->user()->hasRole('owner')) {
            $owner = auth()->user()->owner;
    
            $driver_ids = $owner->driverDetail()->withTrashed()->pluck('id')->toArray();
    
            $totalDuration = DB::table('driver_availabilities')->whereIn('driver_id', $driver_ids)
                ->whereDate('online_at', $request->date)
                ->sum('duration');
    
            $request_detail = $this->request->where('owner_id', $owner->id)
                ->where('is_completed', 1)
                ->whereDate('trip_start_time', $request->date)
                ->orderBy('trip_start_time', 'desc') // Order by trip_start_time descending
                ->get();
        } else {
            $driver = auth()->user()->driver;
    
            $totalDuration = DB::table('driver_availabilities')->where('driver_id', $driver->id)
                ->whereDate('online_at', $request->date)
                ->sum('duration');
    
            $request_detail = $this->request->where('driver_id', $driver->id)
                ->where('is_completed', 1)
                ->whereDate('trip_start_time', $request->date)
                ->orderBy('trip_start_time', 'desc') // Order by trip_start_time descending
                ->get();
        }
    
        $duration_in_mins = (integer)$totalDuration;
    
        $hours = floor($duration_in_mins / 60); // Get the full hours
        $remainingMinutes = $duration_in_mins % 60; // Get the remaining minutes
    
        if ($hours > 0) {
            // If there are full hours, show both hours and minutes
            $total_hours_worked = sprintf('%d Hr %d Mins', $hours, $remainingMinutes);
        } else {
            // If less than an hour, show only minutes
            $total_hours_worked = sprintf('%d Mins', $remainingMinutes);
        }
    
        $totalTrips = $request_detail->count();
    
        if (auth()->user()->hasRole('owner')) {
            $total_trip_kms = $this->request->where('owner_id', $owner->id)
                ->where('is_completed', 1)
                ->whereDate('trip_start_time', $request->date)
                ->sum('total_distance');
        } else {
            $total_trip_kms = $this->request->where('driver_id', $driver->id)
                ->where('is_completed', 1)
                ->whereDate('trip_start_time', $request->date)
                ->sum('total_distance');
        }
    
        $request_result = fractal($request_detail, new EarningTripRequestTransformer);
    
        $currency_symbol = auth()->user()->countryDetail->currency_symbol;
    
        return response()->json([
            'success' => true,
            'message' => 'daily_earnings_listed_successfully',
            'total_trips' => $totalTrips,
            'total_trip_kms' => $total_trip_kms,
            'total_hours_worked' => $total_hours_worked,
            'currency_symbol' => $currency_symbol,
            'requests' => $request_result
        ]);
    }

    /**
    * Today-Earnings
    * @responseFile responses/driver/today-earnings.json
    */
    public function index()
    {
        if(access()->hasRole(Role::OWNER)){

            return $this->ownerEarningsIndex();

        }
        $driver = auth()->user()->driver;

        $current_date = Carbon::now();//->subDays(1)

        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        $converted_current_date = Carbon::parse($current_date)->setTimezone($timezone)->format('jS M Y');

        $total_trips = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->get()->count();

        // Total Trip kms
        $total_trip_kms = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->sum('total_distance');
        // Total Earnings
        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$current_date) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date);
        })->sum('driver_commision');

        //Total cash trip amount
        $total_cash_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$current_date) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->where('payment_opt', '1'); //cash
        })->sum('driver_commision');
        
        $today = Carbon::today();

        // Driver duties
        $total_hours_worked = DriverAvailability::where('driver_id',$driver->id)->where('created_at', '>=', $today)
    ->where('created_at', '<', $today->copy()->addDay())
    ->sum('duration');

        $total_hours_worked = $total_hours_worked>60?round($total_hours_worked/60, 3).' Hrs':$total_hours_worked.' Mins';

        // Total Wallet trip amount
        $total_wallet_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$current_date) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date); //cash
            $query->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            });

        })->sum('driver_commision');

        $total_cash_trip_count = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->where('payment_opt', '1')->get()->count();

        $total_wallet_trip_count = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            })->get()->count();

        $currency_symbol = auth()->user()->countryDetail->currency_symbol;

        return response()->json(['success'=>true,'message'=>'todays_earnings','data'=>[
            'current_date'=>$converted_current_date,
            'total_trips_count'=>$total_trips,
            'total_trip_kms'=>round($total_trip_kms,2),
            'total_earnings'=>round($total_earnings,2),
            'total_cash_trip_amount'=>round($total_cash_trip_amount,2),
            'total_wallet_trip_amount'=>round($total_wallet_trip_amount,2),
            'total_cash_trip_count'=>$total_cash_trip_count,
            'total_wallet_trip_count'=>$total_wallet_trip_count,
            'currency_symbol'=>$currency_symbol,
            'total_hours_worked'=>$total_hours_worked]]);
    }

    /**
     * Owner Earnings
     * 
     * This method retrieves the owner's earnings summary for the current date,
     * including the total trips, distance, earnings, and breakdown of cash and wallet payments.
     * 
     * @response {
     *   "success": true,
     *   "message": "todays_earnings",
     *   "data": {
     *       "current_date": "9th Nov 2024",
     *       "total_trips_count": 5,
     *       "total_trip_kms": 120.5,
     *       "total_earnings": 2000.75,
     *       "total_cash_trip_amount": 1200.50,
     *       "total_wallet_trip_amount": 800.25,
     *       "total_cash_trip_count": 3,
     *       "total_wallet_trip_count": 2,
     *       "currency_symbol": "$",
     *       "total_hours_worked": "4 Hrs"
     *   }
     * }
     */

    public function ownerEarningsIndex()
    {
        $owner = auth()->user()->owner;

        $current_date = Carbon::now();//->subDays(1)

        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        $converted_current_date = Carbon::parse($current_date)->setTimezone($timezone)->format('jS M Y');

        $total_trips = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->get()->count();

        // Total Trip kms
        $total_trip_kms = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->sum('total_distance');
        // Total Earnings
        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$current_date) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date);
        })->sum('driver_commision');

        //Total cash trip amount
        $total_cash_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$current_date) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->where('payment_opt', '1'); //cash
        })->sum('driver_commision');

    

        $total_hours_worked = 0;

        $total_hours_worked = $total_hours_worked>60?round($total_hours_worked/60, 3).' Hrs':$total_hours_worked.' Mins';

        // Total Wallet trip amount
        $total_wallet_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$current_date) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date); //cash
            $query->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            });
        })->sum('driver_commision');

        $total_cash_trip_count = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->where('payment_opt', '1')->get()->count();

        $total_wallet_trip_count = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            })->get()->count();

        $currency_symbol = auth()->user()->countryDetail->currency_symbol;

        return response()->json(['success'=>true,'message'=>'todays_earnings','data'=>['current_date'=>$converted_current_date,'total_trips_count'=>$total_trips,'total_trip_kms'=>round($total_trip_kms,2),'total_earnings'=>$total_earnings,'total_cash_trip_amount'=>$total_cash_trip_amount,'total_wallet_trip_amount'=>$total_wallet_trip_amount,'total_cash_trip_count'=>$total_cash_trip_count,'total_wallet_trip_count'=>$total_wallet_trip_count,'currency_symbol'=>$currency_symbol,'total_hours_worked'=>$total_hours_worked]]);
    }
 
    /**
    * Weekly Earnings
    * @urlParam week_number integer week number of year
    * @responseFile responses/driver/weekly-earnings.json
    */
    public function weeklyEarnings()
    {

        if(access()->hasRole(Role::OWNER)){

            return $this->ownerWeeklyEarningsIndex();

        }

        $driver = auth()->user()->driver;
        $current_date = Carbon::now();
        $disable_next_week = true;
        $disable_previous_week = false;

        $current_week_number = $current_date->weekOfYear;

        if (request()->has('week_number')) {
            if ($current_week_number == request()->week_number) {
                $current_week_number = (integer)request()->week_number;
            } else {
                $current_week_number = (integer)request()->week_number;
                $disable_next_week = false;
            }
        }

        // $current_date->week($current_week_number)->format('Y-m-d H:i');

        $start_of_week = $current_date->startOfWeek()->toDateString();

        $end_of_week = $current_date->endOfWeek()->toDateString();

        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        $converted_start_of_week = Carbon::parse($start_of_week)->setTimezone($timezone)->format('jS M Y');
        $converted_end_of_week = Carbon::parse($end_of_week)->setTimezone($timezone)->format('jS M Y');


        $weekDays = [];

        $week_days_string = ['mon','tues','wed','thurs','fri','sat','sun'];


        for ($i = 0; $i < 7; $i++) {
            $week_date =  Carbon::parse($start_of_week)->addDay($i)->format('Y-m-d');
            // dd($week_date);
            $weekly_total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$week_date) {
                $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereDate('trip_start_time', $week_date);
            })->sum('driver_commision');
            foreach ($week_days_string as $key => $week_day) {
                if ($key==$i) {
                    $weekDays[$week_day] = $weekly_total_earnings;
                }
            }
        }

        $weeks = [$start_of_week,$end_of_week];

        $converted_current_date = Carbon::parse(Carbon::now())->setTimezone($timezone)->format('jS M Y');

        $total_trips = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->get()->count();

        if ($total_trips==0) {
            $disable_previous_week = true;
        }
        // Total Trip kms
        $total_trip_kms = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->sum('total_distance');

        // Driver Duties
        $query = "SELECT SUM(total_hours_worked) AS total_hours_worked
        FROM (SELECT date(online_at), SUM(duration) AS total_hours_worked
        FROM driver_availabilities where driver_id= $driver->id AND date(online_at) BETWEEN '$start_of_week' and '$end_of_week'
        GROUP BY date(online_at)) as duration";

        $driver_duties = DB::select($query);

        $total_hours_worked = $driver_duties[0]->total_hours_worked;
        $total_hours_worked = $total_hours_worked>60?round($total_hours_worked/60, 3).' Hrs':$total_hours_worked.' Mins';

        // Total Earnings
        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$weeks) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks);
        })->sum('driver_commision');

        //Total cash trip amount
        $total_cash_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$weeks) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1'); //cash
        })->sum('driver_commision');

        // Total Wallet trip amount
        $total_wallet_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$weeks) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks); //cash
            $query->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            });
        })->sum('driver_commision');

        $total_cash_trip_count = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1')->get()->count();

        $total_wallet_trip_count = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            })->get()->count();

        $currency_symbol = auth()->user()->countryDetail->currency_symbol;

        return response()->json(['success'=>true,'message'=>'weekly_earnings','data'=>['week_days'=>$weekDays,'current_date'=>$converted_current_date,'current_week_number'=>$current_week_number,'start_of_week'=>$converted_start_of_week,'end_of_week'=>$converted_end_of_week,'disable_next_week'=>$disable_next_week,'disable_previous_week'=>$disable_previous_week,'total_trips_count'=>$total_trips,'total_trip_kms'=>round($total_trip_kms,2),'total_earnings'=>$total_earnings,'total_cash_trip_amount'=>$total_cash_trip_amount,'total_wallet_trip_amount'=>$total_wallet_trip_amount,'total_cash_trip_count'=>$total_cash_trip_count,'total_wallet_trip_count'=>$total_wallet_trip_count,'currency_symbol'=>$currency_symbol,'total_hours_worked'=>$total_hours_worked]]);
    }
    /**
     * Owner Weekly Earnings
     * 
     * This method retrieves the weekly earnings summary for the authenticated owner,
     * including daily earnings, total trips, distances, and breakdown of cash and wallet payments.
     * It also provides navigation control for previous and next weeks.
     * 
     * @response {
     *   "success": true,
     *   "message": "weekly_earnings",
     *   "data": {
     *       "week_days": {
     *           "mon": 250.75,
     *           "tues": 300.50,
     *           "wed": 0,
     *           "thurs": 150.00,
     *           "fri": 400.00,
     *           "sat": 500.00,
     *           "sun": 100.25
     *       },
     *       "current_date": "9th Nov 2024",
     *       "current_week_number": 45,
     *       "start_of_week": "4th Nov 2024",
     *       "end_of_week": "10th Nov 2024",
     *       "disable_next_week": true,
     *       "disable_previous_week": false,
     *       "total_trips_count": 20,
     *       "total_trip_kms": 300.5,
     *       "total_earnings": 1701.50,
     *       "total_cash_trip_amount": 1000.75,
     *       "total_wallet_trip_amount": 700.75,
     *       "total_cash_trip_count": 10,
     *       "total_wallet_trip_count": 10,
     *       "currency_symbol": "$",
     *       "total_hours_worked": "10 Hrs"
     *   }
     * }
     */

    public function ownerWeeklyEarningsIndex()
    {
        $owner = auth()->user()->owner;
        $current_date = Carbon::now();
        $disable_next_week = true;
        $disable_previous_week = false;

        $current_week_number = $current_date->weekOfYear;

        if (request()->has('week_number')) {
            if ($current_week_number == request()->week_number) {
                $current_week_number = (integer)request()->week_number;
            } else {
                $current_week_number = (integer)request()->week_number;
                $disable_next_week = false;
            }
        }

        // $current_date->week($current_week_number)->format('Y-m-d H:i');

        $start_of_week = $current_date->startOfWeek()->toDateString();

        $end_of_week = $current_date->endOfWeek()->toDateString();

        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        $converted_start_of_week = Carbon::parse($start_of_week)->setTimezone($timezone)->format('jS M Y');
        $converted_end_of_week = Carbon::parse($end_of_week)->setTimezone($timezone)->format('jS M Y');


        $weekDays = [];

        $week_days_string = ['mon','tues','wed','thurs','fri','sat','sun'];


        for ($i = 0; $i < 7; $i++) {
            $week_date =  Carbon::parse($start_of_week)->addDay($i)->format('Y-m-d');
            // dd($week_date);
            $weekly_total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$week_date) {
                $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereDate('trip_start_time', $week_date);
            })->sum('driver_commision');
            foreach ($week_days_string as $key => $week_day) {
                if ($key==$i) {
                    $weekDays[$week_day] = $weekly_total_earnings;
                }
            }
        }

        $weeks = [$start_of_week,$end_of_week];

        $converted_current_date = Carbon::parse(Carbon::now())->setTimezone($timezone)->format('jS M Y');

        $total_trips = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->get()->count();

        if ($total_trips==0) {
            $disable_previous_week = true;
        }
        // Total Trip kms
        $total_trip_kms = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->sum('total_distance');

        $total_hours_worked = 0;
        $total_hours_worked = $total_hours_worked>60?round($total_hours_worked/60, 3).' Hrs':$total_hours_worked.' Mins';

        // Total Earnings
        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$weeks) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks);
        })->sum('driver_commision');

        //Total cash trip amount
        $total_cash_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$weeks) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1'); //cash
        })->sum('driver_commision');

        // Total Wallet trip amount
        $total_wallet_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$weeks) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks); //cash
            $query->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            });

        })->sum('driver_commision');

        $total_cash_trip_count = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1')->get()->count();

        $total_wallet_trip_count = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            })->get()->count();

        $currency_symbol = auth()->user()->countryDetail->currency_symbol;

        return response()->json(['success'=>true,'message'=>'weekly_earnings','data'=>['week_days'=>$weekDays,'current_date'=>$converted_current_date,'current_week_number'=>$current_week_number,'start_of_week'=>$converted_start_of_week,'end_of_week'=>$converted_end_of_week,'disable_next_week'=>$disable_next_week,'disable_previous_week'=>$disable_previous_week,'total_trips_count'=>$total_trips,'total_trip_kms'=>round($total_trip_kms,2),'total_earnings'=>$total_earnings,'total_cash_trip_amount'=>$total_cash_trip_amount,'total_wallet_trip_amount'=>$total_wallet_trip_amount,'total_cash_trip_count'=>$total_cash_trip_count,'total_wallet_trip_count'=>$total_wallet_trip_count,'currency_symbol'=>$currency_symbol,'total_hours_worked'=>$total_hours_worked]]);
    }
    /**
    * Earnings Report
    * @urlParam from_date date date string
    * @urlParam to_date date date string
    * @responseFile responses/driver/earnings-report.json
    */
    public function earningsReport($from_date, $to_date)
    {

        if(access()->hasRole(Role::OWNER)){

            return $this->ownerEarningsReport($from_date, $to_date);

        }

        $driver = auth()->user()->driver;

        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        $from_date = Carbon::parse($from_date)->startOfDay();
        $to_date = Carbon::parse($to_date)->endOfDay();
        $weeks = [$from_date,$to_date];

        $total_trips = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->get()->count();

        if ($total_trips==0) {
            $disable_previous_week = true;
        }
        // Total Trip kms
        $total_trip_kms = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->sum('total_distance');

        // Total Earnings
        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$weeks) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks);
        })->sum('driver_commision');

        $total_earnings = round( $total_earnings,2);
        //Total cash trip amount
        $total_cash_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$weeks) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1'); //cash
        })->sum('driver_commision');

        $total_cash_trip_amount = round( $total_earnings,2);


        $query = "SELECT SUM(total_hours_worked) AS total_hours_worked
        FROM (SELECT date(online_at), SUM(duration) AS total_hours_worked
        FROM driver_availabilities where driver_id= $driver->id AND date(online_at) BETWEEN '$from_date' and '$to_date'
        GROUP BY date(online_at)) as duration";

        $driver_duties = DB::select($query);

        $total_hours_worked = $driver_duties[0]->total_hours_worked;
        $total_hours_worked = $total_hours_worked>60?round($total_hours_worked/60, 3).' Hrs':$total_hours_worked.' Mins';

        // Total Wallet trip amount
        $total_wallet_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($driver,$weeks) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks);
            $query->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            });

        })->sum('driver_commision');

        $total_cash_trip_count = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1')->get()->count();

        $total_wallet_trip_count = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            })->get()->count();

        $currency_symbol = auth()->user()->countryDetail->currency_symbol;

        $converted_from_date = Carbon::parse($from_date)->format('jS M Y');
        $converted_to_date = Carbon::parse($to_date)->format('jS M Y');

        return response()->json(['success'=>true,'message'=>'earnings_report','data'=>['from_date'=>$converted_from_date,'to_date'=>$converted_to_date,'total_trips_count'=>$total_trips,'total_trip_kms'=>round($total_trip_kms,2),'total_earnings'=>$total_earnings,'total_cash_trip_amount'=>$total_cash_trip_amount,'total_wallet_trip_amount'=>$total_wallet_trip_amount,'total_cash_trip_count'=>$total_cash_trip_count,'total_wallet_trip_count'=>$total_wallet_trip_count,'currency_symbol'=>$currency_symbol,'total_hours_worked'=>$total_hours_worked]]);
    }

    /**
     * Owner Earnings Report
     * 
     * This method provides a detailed earnings report for the authenticated owner
     * for a specified date range. It includes total trips, kilometers traveled,
     * earnings, and payment breakdown.
     * 
     * @param string $from_date The start date of the report (format: Y-m-d).
     * @param string $to_date The end date of the report (format: Y-m-d).
     * 
     * @response {
     *   "success": true,
     *   "message": "earnings_report",
     *   "data": {
     *       "from_date": "1st Nov 2024",
     *       "to_date": "8th Nov 2024",
     *       "total_trips_count": 30,
     *       "total_trip_kms": 450.75,
     *       "total_earnings": 2500.50,
     *       "total_cash_trip_amount": 1500.00,
     *       "total_wallet_trip_amount": 1000.50,
     *       "total_cash_trip_count": 15,
     *       "total_wallet_trip_count": 15,
     *       "currency_symbol": "$",
     *       "total_hours_worked": "12 Hrs"
     *   }
     * }
     */

    public function ownerEarningsReport($from_date, $to_date)
    {


        $owner = auth()->user()->owner;

        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        $from_date = Carbon::parse($from_date)->startOfDay();
        $to_date = Carbon::parse($to_date)->endOfDay();

        $weeks = [$from_date,$to_date];

        $total_trips = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->get()->count();

        if ($total_trips==0) {
            $disable_previous_week = true;
        }
        // Total Trip kms
        $total_trip_kms = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->sum('total_distance');

        // Total Earnings
        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$weeks) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks);
        })->sum('driver_commision');

        //Total cash trip amount
        $total_cash_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$weeks) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1'); //cash
        })->sum('driver_commision');


        $total_hours_worked = 0;
        $total_hours_worked = $total_hours_worked>60?round($total_hours_worked/60, 3).' Hrs':$total_hours_worked.' Mins';

        // Total Wallet trip amount
        $total_wallet_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($owner,$weeks) {
            $query->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks); //cash
            $query->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            });
        })->sum('driver_commision');

        $total_cash_trip_count = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where('payment_opt', '1')->get()->count();

        $total_wallet_trip_count = $this->request->where('owner_id', $owner->id)->where('is_completed', 1)->whereBetween('trip_start_time', $weeks)->where(function($new_query){
                $new_query->where('payment_opt','2')->orWhere('payment_opt','0');
            })->get()->count();

        $currency_symbol = auth()->user()->countryDetail->currency_symbol;

        $converted_from_date = Carbon::parse($from_date)->format('jS M Y');
        $converted_to_date = Carbon::parse($to_date)->format('jS M Y');

        return response()->json(['success'=>true,'message'=>'earnings_report','data'=>['from_date'=>$converted_from_date,'to_date'=>$converted_to_date,'total_trips_count'=>$total_trips,'total_trip_kms'=>round($total_trip_kms,2),'total_earnings'=>$total_earnings,'total_cash_trip_amount'=>$total_cash_trip_amount,'total_wallet_trip_amount'=>$total_wallet_trip_amount,'total_cash_trip_count'=>$total_cash_trip_count,'total_wallet_trip_count'=>$total_wallet_trip_count,'currency_symbol'=>$currency_symbol,'total_hours_worked'=>$total_hours_worked]]);

    }

    /**
     * 
     * Driver Leaderboard by Earnings
     *
     * This endpoint retrieves the top drivers based on their earnings for the current day,
     * limited to 20 drivers near the provided latitude and longitude. Drivers are ranked
     * by their total commission earned.
     *
     * @bodyParam current_lat double required The current latitude of the driver.
     * @bodyParam current_lng double required The current longitude of the driver.
     *
     * @response {
     *   "success": true,
     *   "message": "Driver earnings leaderboard retrieved successfully.",
     *   "data": [
     *       {
     *           "driver_id": 1,
     *           "driver_name": "John Doe",
     *           "commission": 150.50,
     *           "profile_picture": "https://example.com/uploads/drivers/profile1.jpg"
     *       },
     *       {
     *           "driver_id": 2,
     *           "driver_name": "Jane Smith",
     *           "commission": 120.75,
     *           "profile_picture": "https://example.com/uploads/drivers/profile2.jpg"
     *       }
     *   ]
     * }
     */
    public function leaderBoardEarnings(ValidatorRequest $request)
    {
       $nearest_driver_ids = $this->getLocationDrivers($request);


        // Get the current date
        $currentDate = Carbon::today();

        $request = Request::whereDate('trip_start_time', $currentDate)
            ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
            ->join('drivers', 'requests.driver_id', '=', 'drivers.id')
            ->select('drivers.name', 'requests.driver_id',
                     DB::raw('COUNT(request_bills.id) as request_count'), DB::raw('ROUND(SUM(driver_commision), 2) as commission'))            ->groupBy('requests.driver_id', 'drivers.name')
            ->orderBy('commission', 'desc')
            ->whereIn('requests.driver_id', $nearest_driver_ids)
            ->limit(20)
            ->get()
            ->toArray();

            // dd($request);

        $requests = fractal($request, new LeaderBoardEarningsTransformer);


        return $this->respondSuccess($requests);


    }
    /**
     * Driver Leaderboard by Trips
     *
     * This endpoint retrieves the top drivers ranked by the total number of trips they completed today.
     *
     * @bodyParam current_lat double required Current latitude of the driver.
     * @bodyParam current_lng double required Current longitude of the driver.
     *
     * @response {
     *   "success": true,
     *   "message": "Driver trips leaderboard retrieved successfully.",
     *   "data": [
     *       {
     *           "driver_id": 1,
     *           "driver_name": "John Doe",
     *           "total_trips": 15,
     *           "profile_picture": "https://example.com/uploads/drivers/profile1.jpg"
     *       },
     *       {
     *           "driver_id": 2,
     *           "driver_name": "Jane Smith",
     *           "total_trips": 12,
     *           "profile_picture": "https://example.com/uploads/drivers/profile2.jpg"
     *       }
     *   ]
     * }
     */
    public function leaderBoardTrips(ValidatorRequest $request)
    {
        $nearest_driver_ids = $this->getLocationDrivers($request);
        
        // dd($nearest_driver_ids);

        $currentDate = Carbon::today();
    
        $driver_trip = DB::table('requests')
            ->join('drivers', 'requests.driver_id', '=', 'drivers.id')
            ->whereIn('requests.driver_id', $nearest_driver_ids)
            ->whereDate('requests.trip_start_time', '=', $currentDate)
            ->select('drivers.name', 'drivers.id as driver_id', DB::raw('count(*) as total'))
            ->groupBy('drivers.id', 'drivers.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    // dd($driver_trip);
        $driver_trips = fractal($driver_trip, new LeaderBoardTripsTransformer);

        return $this->respondSuccess($driver_trips);
    }
    /**
     * Get Drivers in the Location's Zone
     * 
     * This function retrieves a list of driver IDs operating in the zone
     * determined by the provided latitude and longitude coordinates.
     * 
     * @param \Illuminate\Http\Request $request The request object containing `current_lat` and `current_lng` parameters.
     * 
     * @return array An array of driver IDs in the corresponding zone.
     *
     */
    public function getLocationDrivers($request)
    {
        $pick_lat = $request->current_lat;
        $pick_lng = $request->current_lng;
    
        $zone = find_zone($pick_lat, $pick_lng);
        $driver_ids = [];
    
        if ($zone) {
            $driver_ids = Driver::where('service_location_id', $zone->service_location_id)
                ->pluck('id')
                ->toArray();
        }
    
        // Debugging output
        // Log::info('Driver IDs:', $driver_ids); // Log the output
        return $driver_ids;
    }
    

 
    /**
    * All-Earnings
    * @responseFile responses/driver/today-earnings.json
    */
    public function allEarnings()
    {
        if(access()->hasRole(Role::OWNER)){

            return $this->ownerEarningsIndex();

        }
        $driver = auth()->user()->driver;

        $current_date = Carbon::now();//->subDays(1)

        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        $converted_current_date = Carbon::parse($current_date)->setTimezone($timezone)->format('jS M Y');

        $total_trips = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->get()->count();
// Request rejected

        $rejected_rides = DriverRejectedRequest::where('driver_id', $driver->id)->get()->count();

        $overall_trips_today = $total_trips + $rejected_rides;

        // Total Trip kms
        $total_trip_kms = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->sum('total_distance');
        // Total Earnings
        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($driver) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1);
        })->sum('driver_commision');

        //Total cash trip amount
        $total_cash_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($driver) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->where('payment_opt', '1'); //cash
        })->sum('driver_commision');

        // Driver duties
        $driver_duties = DriverAvailability::select(DB::raw(" driver_id, date(online_at) AS working_date, SUM(duration) AS total_hours_worked"))->groupBy(DB::raw("driver_id, date(online_at)"))->first();

        $total_hours_worked = $driver_duties?$driver_duties->total_hours_worked:0;

        $total_hours_worked = $total_hours_worked>60?round($total_hours_worked/60, 3).' Hrs':$total_hours_worked.' Mins';

        // Total Wallet trip amount
        $total_wallet_trip_amount = RequestBill::whereHas('requestDetail', function ($query) use ($driver) {
            $query->where('driver_id', $driver->id)->where('is_completed', 1)->where('payment_opt', '2'); //cash
        })->sum('driver_commision');

        $total_cash_trip_count = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->where('payment_opt', '1')->get()->count();

        $total_wallet_trip_count = $this->request->where('driver_id', $driver->id)->where('is_completed', 1)->where('payment_opt', '2')->get()->count();

        $currency_symbol = auth()->user()->countryDetail->currency_symbol;

// total_request_recevied

        $total_request_recevied = $rejected_rides +  $total_trips;

        $rating = round($driver->rating, 2);


        return response()->json(['success'=>true,'message'=>'overall_earnings','data'=>['current_date'=>$converted_current_date,'total_trips_count'=>$total_trips,'total_trip_kms'=>round($total_trip_kms,2),'total_earnings'=>$total_earnings,'total_cash_trip_amount'=>$total_cash_trip_amount,'total_wallet_trip_amount'=>$total_wallet_trip_amount,'total_cash_trip_count'=>$total_cash_trip_count,'total_wallet_trip_count'=>$total_wallet_trip_count,'currency_symbol'=>$currency_symbol,'total_hours_worked'=>$total_hours_worked,'total_recevied_requests'=>$total_request_recevied,'rating'=>$rating]]);
    }

    public function updatePrice(ValidatorRequest $request)
    {
        $driver = auth()->user()->driver;

         $driver->update(['price_per_distance'=>$request->price_per_distance]);

        return response()->json(['success'=>true,'message'=>'price_updated']);
    }
    /**
     * Owner Dashboard Driver Detail
     * @return \Illuminate\Http\JsonResponse
     * @response 
     *   {
     *      "success": true,
     *      "driver_data": {
     *          "driver_id": "100",
     *          "mobile": "7899877890",
     *          "total_earnings": 323232,
     *          "total_distance": 20.402,
     *          "total_admin_earnings": 0,
     *          "total_revenue": 38450,
     *          "per_day_revenue": 38450,
     *          "total_trips": 3,
     *          "completed_requests": "2",
     *          "average_user_rating": 0.5,
     *          "rating_1_average": "0.5000",
     *          "rating_2_average": "0.0000",
     *          "rating_3_average": "0.0000",
     *          "rating_4_average": "0.0000",
     *          "rating_5_average": "0.0000",
     *          "total_duration_in_hours": 0.27,
     *          "average_login_hours_per_day": 0.27
     *      }
     *  }
     */

    public function historyReport(ValidatorRequest $request)
    {
        // dd($request->all());

            $driver = auth()->user()->driver;
            $driverId = $driver->id;

            $createdAt = Carbon::parse($driver->created_at);

            if($request->has('from_date')) {
                $createdAt = Carbon::parse($request->from_date)->startOfDay();
            }
            $now = Carbon::now();

            if($request->has('to_date')) {
                $now = Carbon::parse($request->to_date)->endOfDay();
            }
            // Main query to get fleet earnings and average ratings
            $driverEarnings = DB::table('drivers')
            ->leftJoin('requests', 'drivers.id', '=', 'requests.driver_id') // Left join in case there are no requests
            ->whereBetween('requests.created_at', [$createdAt,$now])
            ->leftJoin('request_bills', 'requests.id', '=', 'request_bills.request_id') // Left join for bills
            ->leftJoin('request_ratings', function($join) {
                $join->on('requests.id', '=', 'request_ratings.request_id')
                    ->where('request_ratings.user_rating', 1); // Only include rows where user_rating is true
            })
            ->select(
                'drivers.id as driver_id',
                'drivers.mobile',
                DB::raw('IFNULL(SUM(request_bills.total_amount), 0) as total_earnings'),
                DB::raw('IFNULL(SUM(request_bills.total_distance), 0) as total_distance'),
                DB::raw('IFNULL(SUM(request_bills.admin_commision), 0) as total_admin_earnings'),
                DB::raw('IFNULL(SUM(request_bills.driver_commision), 0) as total_revenue'),
                DB::raw('IFNULL(SUM(request_bills.driver_commision) / GREATEST(DATEDIFF(NOW(), drivers.created_at), 1), 0) as per_day_revenue'),
                DB::raw('COUNT(requests.id) as total_trips'),
                DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
                DB::raw('FORMAT(AVG(request_ratings.rating), 2) as average_user_rating'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.rating <= 1 THEN request_ratings.rating ELSE NULL END), 0) as rating_1_average'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.rating > 1 AND request_ratings.rating <= 2 THEN request_ratings.rating ELSE NULL END), 0) as rating_2_average'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.rating > 2 AND request_ratings.rating <= 3 THEN request_ratings.rating ELSE NULL END), 0) as rating_3_average'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.rating > 3 AND request_ratings.rating <= 4 THEN request_ratings.rating ELSE NULL END), 0) as rating_4_average'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.rating > 4 AND request_ratings.rating <= 5 THEN request_ratings.rating ELSE NULL END), 0) as rating_5_average')
            )
            ->where('drivers.id', $driverId) // Ensure you are filtering by fleet id
            ->groupBy('drivers.id', 'drivers.mobile', 'drivers.name')
            ->first(); 
        

            // Calculate average login hours per day
            $daysSinceCreation = $createdAt->diffInDays($now);
            $daysSinceCreation = max($daysSinceCreation, 1);

            // Assume you have a separate query to get the total duration in minutes from driver_availabilities
            $totalDurationInMinutes = DB::table('driver_availabilities')
                ->whereBetween('driver_availabilities.created_at', [$createdAt,$now])
                ->join('drivers', 'driver_availabilities.driver_id', '=', 'drivers.id')
                ->where('drivers.id', $driverId)
                ->sum('driver_availabilities.duration');

            // Calculate average duration per day in hours
            $averageDurationPerDayInMinutes = $totalDurationInMinutes / $daysSinceCreation;
            $averageDurationPerDayInHours = round($averageDurationPerDayInMinutes / 60, 2);

            $result = [
                'driver_id' => $driverId,
                'mobile' => $driver->mobile,
                'total_earnings' => 0,
                'total_distance' => 0,
                'total_admin_earnings' => 0,
                'total_revenue' => 0,
                'per_day_revenue'=> 0,
                'total_trips' => 0,
                'completed_requests' => 0,
                'average_user_rating' => 0,
                'rating_1_average' => 0,
                'rating_2_average' => 0,
                'rating_3_average' => 0,
                'rating_4_average' => 0,
                'rating_5_average' => 0,
                'per_day_revenue' => 0,
                'total_duration_in_hours' => round($totalDurationInMinutes / 60, 2),
                'average_login_hours_per_day' => $averageDurationPerDayInHours,
            ];
            if($driverEarnings)
            {
                $result = [
                    'driver_id' => $driverId,
                    'mobile' => $driver->mobile,
                    'total_earnings' => $driverEarnings->total_earnings,
                    'total_distance' => $driverEarnings->total_distance,
                    'total_admin_earnings' => $driverEarnings->total_admin_earnings,
                    'total_revenue' => round($driverEarnings->total_revenue, 10,2),
                    'per_day_revenue'=> round($driverEarnings->per_day_revenue, 10,2),
                    'total_trips' => $driverEarnings->total_trips,
                    'completed_requests' => $driverEarnings->completed_requests,
                    'average_user_rating' => round($driverEarnings->average_user_rating, 10,2),
                    'rating_1_average' => $driverEarnings->rating_1_average,
                    'rating_2_average' => $driverEarnings->rating_2_average,
                    'rating_3_average' => $driverEarnings->rating_3_average,
                    'rating_4_average' => $driverEarnings->rating_4_average,
                    'rating_5_average' => $driverEarnings->rating_5_average,
                    'per_day_revenue' => $driverEarnings->per_day_revenue,
                    'total_duration_in_hours' => round($totalDurationInMinutes / 60, 2),
                    'average_login_hours_per_day' => $averageDurationPerDayInHours,
                ];
            }

            return response()->json([
                'success' => true,
                'driver_data' => $result
            ], 200);            


    }

}
