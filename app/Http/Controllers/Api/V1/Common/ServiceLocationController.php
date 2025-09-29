<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Models\Admin\ServiceLocation;
use App\Http\Controllers\ApiController;
use App\Transformers\ServiceLocationTransformer;
use Illuminate\Http\Request;

/**
 * @group ServiceLocations
 *
 * Get ServiceLocatons
 */
class ServiceLocationController extends ApiController
{
    /**
     * Get all the ServiceLocatons.
     *
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *     "success": true,
     *     "data": [
     *         {
     *             "id": "45c8948b-73dd-4557-af3c-a3dd62cd35b6",
     *             "name": "Wolrd",
     *             "currency_name": "Indian rupee",
     *             "currency_symbol": "₹",
     *             "currency_code": "INR",
     *             "timezone": "Asia/Kolkata",
     *             "active": 1
     *         }
     *     ]
     * }
     */
    public function index()
    {
        $servicelocationsQuery  = ServiceLocation::active()->companyKey();

        $serviceLocations = filter($servicelocationsQuery, new ServiceLocationTransformer)->get();

        return $this->respondOk($serviceLocations);
    }
}
