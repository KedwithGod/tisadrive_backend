<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Payment\DriverIncentiveHistory;
use App\Models\Master\RefferalCommision;
use App\Models\Request\Request;
use App\Models\Admin\Incentive;
use Carbon\Carbon;


class DriverIncentiveHistoryTransformer extends Transformer
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
     * @param CancellationReason $reason
     * @return array
     */
    public function transform(DriverIncentiveHistory $driverIncentiveHistory)
    {

        $params['mode'] = $driverIncentiveHistory->mode;

        $params['amount'] = $driverIncentiveHistory->amount;

        $current_date = Carbon::now();

        $completed_rides = Request::where('driver_id', $driverIncentiveHistory->driver_id)->where('is_completed', true)->whereDate('trip_start_time', $current_date)->get()->count();

        $params['completed_rides'] = $completed_rides;

        $incentive = Incentive::where('ride_count', '>=', $completed_rides)->where('mode', 'daily')->orderBy('ride_count', 'asc') ->skip(1) // Skip the first record
            ->take(1) // Take only one record
            ->first();


            $params['upcomming_ride_count'] = $incentive->ride_count;
            $params['upcomming_incentive_amount'] = $incentive->amount;
      
       return $params;

    }
}
