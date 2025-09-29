<?php

namespace App\Transformers\Payment;

use App\Transformers\Transformer;
use App\Models\Payment\RewardPoint;
use App\Base\Constants\Setting\Settings;

class RewardPointsTransformer extends Transformer
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
    public function transform(RewardPoint $reward_point)
    {
        $params = [
            'id' => $reward_point->id,
            'user_id' => $reward_point->user_id,
            'points_added' => $reward_point->points_added,
            'balance_reward_points' => $reward_point->balance_reward_points,
            'minimun_reward_point' => (integer)get_settings('minimun_reward_point'),
            'conversion_quotient' => round((double)get_settings('reward_point_value'), 2),
            'enable_reward_conversion' => $reward_point->balance_reward_points >= get_settings('minimun_reward_point') ? 1 : 0,
            'points_spent' => $reward_point->points_spent,
            'total_reward_points_collected'=>$reward_point->user->countryDetail->total_reward_points_collected,
            'created_at' => $reward_point->converted_created_at,
            'updated_at' => $reward_point->converted_updated_at,
        ];

        return $params;
    }
}
