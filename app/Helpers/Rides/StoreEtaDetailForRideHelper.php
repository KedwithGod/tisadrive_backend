<?php

namespace App\Helpers\Rides;

use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait StoreEtaDetailForRideHelper
{

    protected function storeEta($request_detail,$eta_result)
    {
        // Log::info("eta-distance");
        // Log::info($eta_result->data->distance);

         // Calculate ETA
         $request_eta_params=[
            'base_price'=>$eta_result->data->base_price,
            'base_distance'=>$eta_result->data->base_distance,
            'total_distance'=>$eta_result->data->distance,
            'total_time'=>$eta_result->data->time,
            'price_per_distance'=>$eta_result->data->price_per_distance,
            'distance_price'=>$eta_result->data->distance_price,
            'price_per_time'=>$eta_result->data->price_per_time,
            'time_price'=>$eta_result->data->time_price,
            'service_tax'=>$eta_result->data->tax_amount,
            'service_tax_percentage'=>$eta_result->data->tax,
            'promo_discount'=>$eta_result->data->discount_amount,
            'admin_commision'=>$eta_result->data->without_discount_admin_commision,
            'admin_commision_with_tax'=>($eta_result->data->without_discount_admin_commision + $eta_result->data->tax_amount),
            'total_amount'=>$request_detail->request_eta_amount,
            'requested_currency_code'=>$request_detail->requested_currency_code
        ];

        $requesteta = $request_detail->requestEtaDetail()->first();
        if ($requesteta) {
            $requesteta->update($request_eta_params);
        } else {
            $request_detail->requestEtaDetail()->create($request_eta_params);
        }


    }
    
}
