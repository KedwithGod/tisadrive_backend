<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Transformers\User\UserTransformer;
use App\Transformers\Driver\DriverTransformer;
use App\Models\Request\Request as RequestModel;
use App\Transformers\User\AdHocUserTransformer;
use App\Transformers\Requests\RequestBillTransformer;
use Carbon\Carbon;
use App\Base\Constants\Masters\PaymentType;
use App\Base\Constants\Setting\Settings;
use App\Transformers\Requests\RequestStopsTransformer;
use App\Transformers\Requests\RequestProofsTransformer;
use Log;

class EarningTripRequestTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [
    ];

    /**
     * Resources that can be included in default.
     *
     * @var array
     */
    protected array $defaultIncludes = [
    ];

    /**
     * A Fractal transformer.
     *
     * @param RequestModel $request
     * @return array
     */
    public function transform(RequestModel $request)
    {
        $params =  [
            'id' => $request->id,
            'request_number' => $request->request_number,
            'ride_otp'=>$request->ride_otp,
            'is_later' => (bool) $request->is_later,
            'user_id' => $request->user_id,
            'service_location_id'=>$request->service_location_id,
            'trip_commission'=> $request->requestBill ? $request->requestBill->driver_commision : $request->driver_commision,
            'total_distance'=>number_format($request->total_distance,2),
            'total_time'=>$request->total_time,
            'unit' => $request->unit==2?'MILES':'KM',
            'trip_start_time' => $request->converted_trip_start_time,


        ];

        return $params;
    }

 
}
