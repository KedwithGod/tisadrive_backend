<?php

namespace App\Transformers\Payment;

use App\Transformers\Transformer;
use App\Models\Payment\RewardHistory;
use App\Base\Constants\Setting\Settings;

class RewardHistoryTransformer extends Transformer
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
     * @return array
     */
    public function transform(RewardHistory $reward_point)
    {
        $params = [
            "id" => $reward_point->id,
            "request_id" => $reward_point->request_id ?? "",
            "is_credit" => (bool)$reward_point->is_credit,
            "amount" => $reward_point->amount,
            "reward_points" => $reward_point->reward_points,
            "remarks" => $reward_point->remarks,
            'created_at' => $reward_point->converted_created_at,
        ];

        return $params;
    }
}
