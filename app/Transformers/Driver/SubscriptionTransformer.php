<?php

namespace App\Transformers\Driver;

use App\Transformers\Transformer;
use App\Models\Admin\Subscription;
use App\Models\Admin\SubscriptionDetail;
use App\Models\Admin\SubscriptionDiscount;
use App\Transformers\Driver\SubscriptionDetailTransformer;
use App\Transformers\Driver\SubscriptionDiscountTransformer;


class SubscriptionTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [
        'subscriptionDetail'
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
    public function transform(Subscription $plan)
    {
        $params = [
            'id' => $plan->id,
            'name' => $plan->name,
            'description' => $plan->description,
            'duration' => $plan->subscription_duration,
            'duration' => $plan->subscription_duration,
            'currency_symbol' => get_settings('currency_symbol'),
            'amount' => round($plan->amount, 2),
            'vehicle_type_id' => $plan->vehicle_type_id,
            'vehicle_type_name' => $plan->vehicleTypeDetail->name,
        ];



        return $params;
    }


    /**
     * Include the meta request of the driver.
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeSubscriptionDetail(Subscription $plan)
    {
        $driver = auth()->user()->driver;
        $detail = SubscriptionDetail::active()->where('subscription_id',$plan->id)->first();
        return $detail
        ? $this->item($detail, new SubscriptionDetailTransformer)
        : $this->null();
    }
}
