<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Request\RequestBill;

class RequestBillTransformer extends Transformer
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
     * @param RequestBill $request
     * @return array
     */
    public function transform(RequestBill $request)
    {

        $base_distance = (double) $request->base_distance;

        $calculatable_distance = ($request->total_distance - $base_distance);

        if($calculatable_distance < 0 ){

            $calculatable_distance = 0;
        }
        return [
            'id' => $request->id,
            'base_price' => $request->base_price,
            'base_distance' => $request->base_distance,
            'price_per_distance' => round($request->price_per_distance, 2),
            'distance_price' => round($request->distance_price, 2),
            'price_per_time' => round($request->price_per_time, 2),
            'time_price' => round($request->time_price, 2),
            'waiting_charge' => round($request->waiting_charge, 2),
            'cancellation_fee' => round($request->cancellation_fee, 2),
            'airport_surge_fee'=>$request->airport_surge_fee,
            'service_tax' => round($request->service_tax, 2),
            'service_tax_percentage' => round($request->service_tax_percentage,2),
            'promo_discount' => round($request->promo_discount,2),
            'admin_commision' => round($request->admin_commision,2),
            'driver_commision' => round($request->driver_commision,2),
            'total_amount' => round($request->total_amount,2),
            'total_distance' => round($request->total_distance,2),
            'total_time' => round($request->total_time,2),
            'calculated_distance' => round($calculatable_distance,2),
            'unit' => $request->requestDetail->unit==2?'MILES':'KM',
            'driver_tips' => round($request->tips,2),
            'requested_currency_code' => $request->requested_currency_code,
            'requested_currency_symbol' => $request->requested_currency_symbol,
            'admin_commision_with_tax' => round($request->admin_commision_with_tax,2),
            'calculated_waiting_time'=>round($request->calculated_waiting_time, 2),
            'waiting_charge_per_min'=>round($request->waiting_charge_per_min, 2),
            'admin_commision_from_driver'=>round($request->admin_commission_from_driver, 2),
            'additional_charges_reason' => $request->additional_charges_reason,
            'additional_charges_amount' => round($request->additional_charges_amount, 2),
            'enable_driver_tips_feature'=> get_settings('enable_driver_tips_feature'),
        ];
    }
}
