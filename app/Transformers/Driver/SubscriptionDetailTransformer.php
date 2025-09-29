<?php

namespace App\Transformers\Driver;

use App\Base\Constants\Auth\Role;
use App\Transformers\Transformer;
use App\Models\Admin\SubscriptionDetail;
use Carbon\Carbon;

class SubscriptionDetailTransformer extends Transformer
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
    public function transform(SubscriptionDetail $detail)
    {

        $timezone = $detail->driverDetail->user->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        $expired_at = Carbon::parse($detail->expired_at)->setTimezone($timezone)->format('d-m-y, H:i A');
        $created_at = Carbon::parse($detail->created_at)->setTimezone($timezone)->format('d-m-y, H:i A');
        $params = [
            'id' => $detail->id,
            'subscription_id' => $detail->subscription_id,
            'subscription_name' => $detail->subscription->name,
            'transaction_id' => $detail->transaction_id,
            'paid_amount' => $detail->amount,
            'expired_at' => $expired_at,
            'started_at' => $created_at,
            'subscription_type' => $detail->subscription_type,
        ];



        return $params;
    }
}
