<?php

namespace App\Transformers\Driver;

use App\Base\Constants\Auth\Role;
use App\Transformers\Transformer;
use App\Models\Admin\Driver;
use App\Models\Admin\DriverLevelUp;
use App\Models\Admin\DriverLevelDetail;
use App\Transformers\Driver\LevelDetailTransformer;
use Carbon\Carbon;

class LevelTransformer extends Transformer
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
        'levelDetails'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(DriverLevelUp $detail)
    {

        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        $created_at = Carbon::parse($detail->created_at)->setTimezone($timezone)->format('d m Y');
        $params = [
            'id' => $detail->id,
            'level' => $detail->level,
            'level_name' => $detail->name,
            'min_ride_count' => $detail->min_ride_count,
            'min_ride_amount' => $detail->min_ride_amount,
            'is_min_ride' => $detail->is_min_ride_complete,
            'zone_type_id' => $detail->zone_type_id,
            'is_min_earning' => $detail->is_min_ride_amount_complete,
            'level_icon' => $detail->image,
            'level_completed' => auth()->user()->driver->levelHistory()->where('level_id',$detail->id)->exists() ? 1:0,
            'created_at' => $created_at,
        ];



        return $params;
    }
    /**
     * Include the meta request of the driver.
     *
     * @param Driver $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeLevelDetails(DriverLevelUp $detail)
    {
        $detail = auth()->user()->driver->levelHistory()->where('level_id',$detail->id)->first();
        return $detail
        ? $this->item($detail, new LevelDetailTransformer)
        : $this->null();
    }
}
