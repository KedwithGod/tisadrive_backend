<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Request\DriverRejectedRequest;

class RequestRejectDriverTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [

    ];

    /**
     * A Fractal transformer.
     *
     * @param DriverRejectedRequest $request
     * @return array
     */
    public function transform(DriverRejectedRequest $request)
    {
    
        return [
            'id' => $request->id,
            'profile_picture' => $request->drivers->profile_picture,
            'driver_name' => $request->drivers->name,
            'driver_mobile' => $request->drivers->mobile,
            'driver_rating' => $request->drivers->rating,
            'driver_email' => $request->drivers->email,
            'converted_created_at'=>$request->converted_created_at_date
        ];
    }
}
