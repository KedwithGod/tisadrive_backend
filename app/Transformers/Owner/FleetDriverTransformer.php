<?php

namespace App\Transformers\Owner;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin\Driver;
use App\Base\Constants\Auth\Role;
use App\Transformers\Transformer;
use App\Models\Request\RequestBill;
use App\Models\Request\RequestMeta;
use App\Models\Admin\DriverDocument;
use App\Models\Admin\DriverNeededDocument;
use App\Transformers\Access\RoleTransformer;
use App\Transformers\Requests\TripRequestTransformer;
use Illuminate\Support\Facades\DB;

class FleetDriverTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [
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
    public function transform(Driver $driver)
    {
        $params = [
            'id' => $driver->id,
            'name' => $driver->name,
            'email' => $driver->email,
            'owner_id' => $driver->owner_id,
            'mobile' => $driver->user->countryDetail->dial_code . $driver->mobile,
            'profile_picture' => $driver->profile_picture,
            'active' => (bool)$driver->active,
            'fleet_id' => $driver->fleet_id,
            'approve' => (bool)$driver->approve,
            'available' => (bool)$driver->available,
            'uploaded_document' => false,
            'declined_reason' => $driver->reason,
            'service_location_id' => $driver->service_location_id ?? null,
            'vehicle_type_id' => $driver->vehicle_type,
            'vehicle_type_name' => $driver->vehicle_type_name,
            'vehicle_type_icon' => $driver->vehicle_type_image,
            'car_make' => $driver->car_make,
            'car_model' => $driver->car_model,
            'car_make_name' => $driver->car_make_name,
            'car_model_name' => $driver->car_model_name,
            'car_color' => $driver->car_color,
            'driver_lat' => $driver->driver_lat,
            'driver_lng' => $driver->driver_lng,
            'car_number' => $driver->car_number,
            'rating' => round($driver->rating, 2),
            'no_of_ratings' => $driver->no_of_ratings,
            'timezone' => $driver->timezone,
            'refferal_code' => $driver->user->refferal_code,
            'company_key' => $driver->user->company_key,
            'show_instant_ride' => true,
            'currency_symbol' => $driver->user->countryDetail ? $driver->user->countryDetail->currency_symbol : '₹',
            'currency_code' => $driver->user->countryDetail ? $driver->user->countryDetail->currency_code : '₹',
            'languages' => $driver->languages,
        ];
    
        // Update with fleet details if driver belongs to a fleet
        if ($driver->fleetDetail()->exists()) {
            $params['car_make_name'] = $driver->fleetDetail->carBrand->name ?? $driver->custom_make;
            $params['car_model_name'] = $driver->fleetDetail->carModel->name ?? $driver->custom_model;
            $params['car_number'] = $driver->fleetDetail->license_number;
            $params['car_color'] = $driver->fleetDetail->car_color;
        }
    
        // Fleet driver earnings data
        $fleet_driver_data = DB::table('drivers')
            ->join('requests', 'drivers.id', '=', 'requests.driver_id')
            ->join('request_bills', 'requests.id', '=', 'request_bills.request_id')
            ->leftJoin('request_ratings',  function ($join) {
                $join->on('requests.id', '=', 'request_ratings.request_id')
                     ->where('request_ratings.user_rating', 1);
            })
            ->select(
                DB::raw('FORMAT(SUM(request_bills.total_amount), 2) as total_earnings'),
                DB::raw('FORMAT(SUM(request_bills.driver_commision), 2) as total_driver_earnings'),
                DB::raw('FORMAT(SUM(request_bills.admin_commision), 2) as total_admin_earnings'),
                DB::raw('COUNT(requests.id) as total_trips'),
                DB::raw('SUM(CASE WHEN requests.is_completed = 1 THEN 1 ELSE 0 END) as completed_requests'),
                DB::raw('FORMAT(AVG(request_ratings.rating), 2) as average_user_rating')
            )
            ->where('requests.driver_id', $driver->id)
            ->groupBy('drivers.id')
            ->first();
    
        // Add earnings data to the params if available
        if ($fleet_driver_data) {
            $params['total_earnings'] = $fleet_driver_data->total_earnings;
            $params['total_driver_earnings'] = $fleet_driver_data->total_driver_earnings;
            $params['total_admin_earnings'] = $fleet_driver_data->total_admin_earnings;
            $params['total_trips'] = $fleet_driver_data->total_trips;
            $params['completed_requests'] = $fleet_driver_data->completed_requests;
            $params['average_user_rating'] = $fleet_driver_data->average_user_rating;
        } else {
            // Default values if no earnings data found
            $params['total_earnings'] = '0.00';
            $params['total_driver_earnings'] = '0.00';
            $params['total_admin_earnings'] = '0.00';
            $params['total_trips'] = 0;
            $params['completed_requests'] = 0;
            $params['average_user_rating'] = '0.00';
        }
    
        return $params;
    }
}    
