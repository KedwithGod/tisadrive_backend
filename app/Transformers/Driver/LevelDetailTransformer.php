<?php

namespace App\Transformers\Driver;

use App\Base\Constants\Auth\Role;
use App\Transformers\Transformer;
use App\Models\Admin\Driver;
use App\Models\Admin\DriverLevelDetail;
use App\Models\Request\Request;
use App\Models\Request\RequestBill;
use Carbon\Carbon;

class LevelDetailTransformer extends Transformer
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
    public function transform(DriverLevelDetail $detail)
    {

        $timezone = $detail->driverDetail->user->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        $created_at = Carbon::parse($detail->created_at)->setTimezone($timezone)->format('d M, Y');
        $level = $detail->levelDetail;
        $level_key = $level ? $level->level : 0;
        $params = [
            'id' => $detail->id,
            'level' => $level_key,
            'level_name' => $level ? $level->name : 'Level '.$level_key,
            'level_id' => $level ? $level->id : 0,
            'is_min_ride_completed' => $detail->ride_rewarded,
            'is_min_earning_completed' => $detail->amount_rewarded,
            'level_icon' => $level ? $level->image : null,
            'created_at' => $created_at,
        ];
        if(!$detail->amount_rewarded){
            $params['total_earnings'] = RequestBill::whereHas('requestDetail', function ($query) use ($detail) {
                $query->where('driver_id', $detail->driver_id)
                      ->where('is_completed', 1);
            })->sum('driver_commision');
        }else{
            $params['total_earnings'] = $level ? $level->min_ride_amount : 0;
        }
        if(!$detail->ride_rewarded){
            $params['total_rides'] = Request::where('driver_id', $detail->driverDetail->id)->where('is_completed', true)->count();
        }else{
            $params['total_rides'] = $level ? $level->is_min_ride_complete : 0;
        }



        return $params;
    }
}
