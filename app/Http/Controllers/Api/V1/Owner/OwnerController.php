<?php

namespace App\Http\Controllers\Api\V1\Owner;

use Illuminate\Support\Carbon;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Admin\Fleet;
use Illuminate\Http\Request;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use Kreait\Firebase\Contract\Database;
use App\Models\Admin\Owner;
use App\Base\Constants\Auth\Role;
use App\Models\Admin\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Request\Request as RequestModel;
use App\Transformers\Owner\OwnerProfileTransformer;
use App\Transformers\Owner\OwnerDashboardTransformer;
use App\Models\Admin\OwnerBankInfo;
use App\Transformers\BankInfoTransformer;
use App\Models\Method;
use App\Models\Field;

/**
 * @group Fleet-Owner-apis
 * @authenticated
 */
class OwnerController extends BaseController
{


//OwnerDashboard

    /**
     * Owner Dashboard Detail
     * @return \Illuminate\Http\JsonResponse
     * @response 
     * {
     *     "success": true,
     *     "message": "owner_dashboard_listed",
     *     "data": {
     *         "id": "0b886eed-a4e3-4c58-9545-3f325932a9cf",
     *         "user_id": 129,
     *         "company_name": "ship company",
     *         "blocked_fleets": 0,
     *         "active_fleets": 0,
     *         "inactive_fleets": 0,
     *         "card_earnings": 0,
     *         "cash_earnings": 0,
     *         "wallet_earnings": 0,
     *         "revenue": 0,
     *         "admin_commission": 0,
     *         "net_earnings": 0,
     *         "discount": 0,
     *         "digital_earnings": 0,
     *         "fleetDetail": {
     *             "data": []
     *         },
     *         "driverDetail": {
     *             "data": []
     *         }
     *     }
     * }
     */
    public function ownerDashboard(Request $request)
    {

        // dd($request->all());

        $owner = auth()->user()->owner;

         $result  = fractal($owner, new OwnerDashboardTransformer);

        return $this->respondSuccess($result,'owner_dashboard_listed');                

    }

    /**
     * Owner Dashboard Fleet Detail
     * @return \Illuminate\Http\JsonResponse
     * @response 
     * {
     *     "success": true,
     *     "fleet_data": {
     *         "fleet_id": "84af402f-e78d-4a39-a9a2-d7ef23892709",
     *         "license_number": "qwert",
     *         "vehicle_type_name": "Delivery",
     *         "total_earnings": 0,
     *         "total_distance": 0,
     *         "total_admin_earnings": 0,
     *         "total_revenue": 0,
     *         "per_day_revenue": 0,
     *         "total_trips": 0,
     *         "completed_requests": "0",
     *         "average_user_rating": 0,
     *         "rating_1_average": "0.0000",
     *         "rating_2_average": "0.0000",
     *         "rating_3_average": "0.0000",
     *         "rating_4_average": "0.0000",
     *         "rating_5_average": "0.0000",
     *         "total_duration_in_hours": 0,
     *         "average_login_hours_per_day": 0
     *     }
     * }
     */
    public function fleetDashboard(Request $request, Fleet $fleet)
    {
        // dd($request->all());
            // Define the fleet ID
            $fleetId = $request->fleet_id;

            $fleet = Fleet::where('id', $fleetId)->first();

            $now = Carbon::now();

            // Main query to get fleet earnings and average ratings
            $fleetEarnings = DB::table('fleets')
            ->leftJoin('requests', 'fleets.id', '=', 'requests.fleet_id') // Left join in case there are no requests
            ->leftJoin('request_bills', 'requests.id', '=', 'request_bills.request_id') // Left join for bills
            ->leftJoin('vehicle_types', 'fleets.vehicle_type', '=', 'vehicle_types.id')
            ->leftJoin('request_ratings', 'requests.id', '=', 'request_ratings.request_id') // Left join for ratings
            ->select(
                'fleets.id as fleet_id',
                'fleets.license_number',
                'vehicle_types.name as vehicle_type_name',
                DB::raw('IFNULL(SUM(request_bills.total_amount), 0) as total_earnings'),
                DB::raw('IFNULL(SUM(request_bills.total_distance), 0) as total_distance'),
                DB::raw('IFNULL(SUM(request_bills.admin_commision), 0) as total_admin_earnings'),
                DB::raw('IFNULL(SUM(request_bills.driver_commision), 0) as total_revenue'),
                DB::raw('IFNULL(SUM(request_bills.driver_commision) / GREATEST(DATEDIFF(NOW(), fleets.created_at), 1), 0) as per_day_revenue'),
                DB::raw('COUNT(requests.id) as total_trips'),
                DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
                DB::raw('FORMAT(AVG(request_ratings.user_rating), 2) as average_user_rating'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.user_rating <= 1 THEN request_ratings.user_rating ELSE NULL END), 0) as rating_1_average'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.user_rating > 1 AND request_ratings.user_rating <= 2 THEN request_ratings.user_rating ELSE NULL END), 0) as rating_2_average'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.user_rating > 2 AND request_ratings.user_rating <= 3 THEN request_ratings.user_rating ELSE NULL END), 0) as rating_3_average'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.user_rating > 3 AND request_ratings.user_rating <= 4 THEN request_ratings.user_rating ELSE NULL END), 0) as rating_4_average'),
                DB::raw('IFNULL(AVG(CASE WHEN request_ratings.user_rating > 4 AND request_ratings.user_rating <= 5 THEN request_ratings.user_rating ELSE NULL END), 0) as rating_5_average')
            )
            ->where('fleets.id', $fleetId) // Ensure you are filtering by fleet id
            ->groupBy('fleets.id', 'fleets.license_number', 'vehicle_types.name')
            ->first(); 
        

            // Calculate average login hours per day
            $createdAt = Carbon::parse($fleet->created_at);
            $daysSinceCreation = $createdAt->diffInDays($now);
            $daysSinceCreation = max($daysSinceCreation, 1);

            // Assume you have a separate query to get the total duration in minutes from driver_availabilities
            $totalDurationInMinutes = DB::table('driver_availabilities')
                ->join('drivers', 'driver_availabilities.driver_id', '=', 'drivers.id')
                ->join('fleets', 'drivers.fleet_id', '=', 'fleets.id')
                ->where('fleets.id', $fleetId)
                ->sum('driver_availabilities.duration');

            // Calculate average duration per day in hours
            $averageDurationPerDayInMinutes = $totalDurationInMinutes / $daysSinceCreation;
            $averageDurationPerDayInHours = round($averageDurationPerDayInMinutes / 60, 2);

            $result = [
                'fleet_id' => $fleetId,
                'license_number' => $fleetEarnings->license_number,
                'vehicle_type_name' => $fleetEarnings->vehicle_type_name,
                'total_earnings' => $fleetEarnings->total_earnings,
                'total_distance' => $fleetEarnings->total_distance,
                'total_admin_earnings' => $fleetEarnings->total_admin_earnings,
                'total_revenue' => round($fleetEarnings->total_revenue, 10,2),
                'per_day_revenue'=> round($fleetEarnings->per_day_revenue, 10,2),
                'total_trips' => $fleetEarnings->total_trips,
                'completed_requests' => $fleetEarnings->completed_requests,
                'average_user_rating' => round($fleetEarnings->average_user_rating, 10,2),
                'rating_1_average' => $fleetEarnings->rating_1_average,
                'rating_2_average' => $fleetEarnings->rating_2_average,
                'rating_3_average' => $fleetEarnings->rating_3_average,
                'rating_4_average' => $fleetEarnings->rating_4_average,
                'rating_5_average' => $fleetEarnings->rating_5_average,
                'per_day_revenue' => $fleetEarnings->per_day_revenue,
                'total_duration_in_hours' => round($totalDurationInMinutes / 60, 2),
                'average_login_hours_per_day' => $averageDurationPerDayInHours,
            ];


            return response()->json([
                'success' => true,
                'fleet_data' => $result
            ], 200);            


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

    public function fleetDriverDashboard(Request $request)
    {
        // dd($request->all());
            // Define the fleet ID
            $driverId = $request->driver_id;

            $driver = Driver::where('id', $driverId)->first();

            $now = Carbon::now();

            // Main query to get fleet earnings and average ratings
            $driverEarnings = DB::table('drivers')
            ->leftJoin('requests', 'drivers.id', '=', 'requests.driver_id') // Left join in case there are no requests
            ->leftJoin('request_bills', 'requests.id', '=', 'request_bills.request_id') // Left join for bills
            ->leftJoin('request_ratings', function($join) {
                $join->on('requests.id', '=', 'request_ratings.request_id')
                    ->where('request_ratings.user_rating', '=', 1); // Only include rows where user_rating is true
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
            $createdAt = Carbon::parse($driver->created_at);
            $daysSinceCreation = $createdAt->diffInDays($now);
            $daysSinceCreation = max($daysSinceCreation, 1);

            // Assume you have a separate query to get the total duration in minutes from driver_availabilities
            $totalDurationInMinutes = DB::table('driver_availabilities')
                ->join('drivers', 'driver_availabilities.driver_id', '=', 'drivers.id')
                ->where('drivers.id', $driverId)
                ->sum('driver_availabilities.duration');

            // Calculate average duration per day in hours
            $averageDurationPerDayInMinutes = $totalDurationInMinutes / $daysSinceCreation;
            $averageDurationPerDayInHours = round($averageDurationPerDayInMinutes / 60, 2);

            $result = [
                'driver_id' => $driverId,
                'mobile' => $driverEarnings->mobile,
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


            return response()->json([
                'success' => true,
                'driver_data' => $result
            ], 200);            


    }


    

}
