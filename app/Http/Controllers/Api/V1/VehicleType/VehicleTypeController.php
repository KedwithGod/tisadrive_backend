<?php

namespace App\Http\Controllers\Api\V1\VehicleType;

use Carbon\Carbon;
use App\Models\User;
use App\Events\Event;
use App\Models\Admin\Driver;
use Illuminate\Http\Request;
use App\Models\Admin\VehicleType;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ServiceLocation;
use App\Http\Controllers\ApiController;
use App\Jobs\Notifications\PhpToNodeJob;
use App\Base\Constants\Masters\DateOptions;
use App\Transformers\User\ZoneTypeTransformer;
use App\Http\Controllers\Api\V1\BaseController;
use App\Transformers\User\ZoneTypeTransformerOld;
use App\Http\Requests\Admin\VehicleTypes\CreateVehicleTypeRequest;
use Log;
use Config;
use App\Models\Admin\SubVehicleType;

/**
 * @group Vehicle Management
 *
 * APIs for Vehicle-Types
 */
class VehicleTypeController extends BaseController
{
    /**
     * The VehicleType model instance.
     *
     * @var \App\Models\Admin\VehicleType
     */
    protected $vehicle_type;


    /**
     * VehicleTypeController constructor.
     *
     * @param \App\Models\Admin\VehicleType $vehicle_type
     */
    public function __construct(VehicleType $vehicle_type)
    {
        $this->vehicle_type = $vehicle_type;
    }


    /**
    * Get Vehcile Types by Service location
    * @urlParam service_location_id required string service location's id
    * @response {"success":true,"message":"success","data":[{"id":"9ea6f9a0-6fd2-4962-9d81-645e6301096f","name":"Mini","icon":null,"capacity":4,"is_accept_share_ride":0,"active":1,"created_at":"2020-02-13 09:06:39","updated_at":"2020-02-13 09:06:39","deleted_at":null}]}
    */
    public function getVehicleTypesByServiceLocation(ServiceLocation $service_location)
    {
        // DB::enableQueryLog();
        if(config('app.app_for') !== 'taxi' && config('app.app_for') !== "delivery")
        {
        $response = $this->vehicle_type->whereActive(true)->whereHas('zoneType.zone', function ($query) use ($service_location) {
            $query->where('service_location_id', $service_location->id);
        })->where('is_taxi',request()->transport_type)->orWhere('is_taxi','both')->get();
        }else{
        $query = $this->vehicle_type->whereActive(true)->whereHas('zoneType.zone', function ($query) use ($service_location) {
            $query->where('service_location_id', $service_location->id);
        });

        $response = $query->limit(10)->get();
        // dd(DB::getQueryLog());
        }

        return $this->respondSuccess($response);
    }

    public function getSubVehicleTypesByServiceLocation(ServiceLocation $service_location)
    {

        $vehicle_type = request()->input('vehicle_type');
        $sub_vehicle_type = SubVehicleType::where('vehicle_type_id',$vehicle_type )->pluck('sub_vehicle_type_id')->toArray();

        $response = $this->vehicle_type->whereActive(true)
        ->whereIn('id', $sub_vehicle_type)
        ->get();

        return $this->respondSuccess($response);
    }
}
