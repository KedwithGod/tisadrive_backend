<?php

namespace App\Transformers\Owner;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin\Owner;
use App\Base\Constants\Auth\Role;
use App\Transformers\Transformer;
use App\Base\Constants\Setting\Settings;
use App\Models\Admin\Fleet;
use App\Transformers\Driver\DriverTransformer;
use Illuminate\Support\Facades\DB;

class FleetTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [
        'driverDetail'
        
    ];

    /**
    * Resources that can be included default.
    *
    * @var array
    */
    protected array $defaultIncludes = [

        
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Fleet $fleet)
    {
        // Basic fleet data
        $params = [
            'id' => $fleet->id,
            'owner_id' => $fleet->owner_id,//user_id 
            'driver_id' =>  $fleet->driverDetail->id ?? null,
            'driver_name' =>  $fleet->driverDetail->name ?? null,
            'license_number' => $fleet->license_number,
            'vehicle_type' => $fleet->vehicleTypeDetail->name ?? null,
            'brand' => $fleet->carBrand->name ?? $fleet->custom_make,
            'model' => $fleet->carModel->name ?? $fleet->custom_model,
            'approve' => $fleet->approve,
            'car_color' => $fleet->car_color,
            'type_icon' => $fleet->vehicleTypeDetail->icon ?? null
        ];
        
        $declined_documemt = $fleet->fleetDocument()
        ->where('document_status', 5)
        ->exists();
        $params['is_declined'] = $declined_documemt;

if (($fleet->status === "0") || ($fleet->status === "5") || ($fleet->status === "6")) {
    $params['status'] = "blocked";
} elseif (($fleet->status === "2") || ($fleet->status === "3") || ($fleet->status === "4")) {
    $params['status'] = "waiting";
} else {
    $params['status'] = "approved";
}


    
        // Earnings data
        $fleetEarnings = DB::table('fleets')
            ->join('requests', 'fleets.id', '=', 'requests.fleet_id')
            ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
            ->join('vehicle_types', 'fleets.vehicle_type', '=', 'vehicle_types.id')
            ->leftJoin('request_ratings',  function ($join) {
                $join->on('requests.id', '=', 'request_ratings.request_id')
                     ->where('request_ratings.user_rating', 1);
            })
            ->select(
                DB::raw('IFNULL(SUM(request_bills.total_amount), 0) as total_earnings'),
                DB::raw('IFNULL(SUM(request_bills.driver_commision), 0) as total_driver_earnings'),
                DB::raw('IFNULL(SUM(request_bills.admin_commision), 0) as total_admin_earnings'),
                DB::raw('COUNT(requests.id) as total_trips'),
                DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
                DB::raw('IFNULL(AVG(request_ratings.rating), 0) as average_user_rating')
            )
            ->where('requests.fleet_id', $fleet->id)
            ->groupBy('fleets.id')
            ->first();
    
        // Check if earnings data is found
        if ($fleetEarnings) {
            $params['total_earnings'] = $fleetEarnings->total_earnings;
            $params['total_driver_earnings'] = $fleetEarnings->total_driver_earnings;
            $params['total_admin_earnings'] = $fleetEarnings->total_admin_earnings;
            $params['total_trips'] = $fleetEarnings->total_trips;
            $params['completed_requests'] = $fleetEarnings->completed_requests;
            $params['average_user_rating'] = $fleetEarnings->average_user_rating;
        } else {
            // Handle case where there's no earnings data
            $params['total_earnings'] = 0;
            $params['total_driver_earnings'] = 0;
            $params['total_admin_earnings'] = 0;
            $params['total_trips'] = 0;
            $params['completed_requests'] = 0;
            $params['average_user_rating'] = 0;
        }
    
        return $params;
    }
    

    
    /**
     * Include the driver of the request.
     *
     * @param Fleet $fleet
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeDriverDetail(Fleet $fleet)
    {
        $driverDetail = $fleet->driverDetail;

        return $driverDetail
        ? $this->item($driverDetail, new DriverTransformer)
        : $this->null();
    }

   
}
