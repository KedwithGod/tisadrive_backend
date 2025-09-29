<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;
use Illuminate\Http\Request;
use Carbon\Carbon;
/**
 * Test filter to demonstrate the custom filter usage.
 * Delete later.
 */
class RequestFilter implements FilterContract
{

    /**
     * The available filters.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'is_completed','is_cancelled','is_trip_start','is_paid','payment_opt','vehicle_type',
            'is_assigned','is_later','on_trip','date_option','ride_status','transport_type','zone_id',
            'vehicle_type_id','is_bid_ride'
        ];
    }

    /**
    * Default column to sort.
    *
    * @return string
    */
    public function defaultSort()
    {
        return '-created_at';
    }

    public function is_completed($builder, $value=0)
    {


        $builder->whereHas('requestBill',function($query){
        })->where('is_completed', $value)
        ->where('is_cancelled', 0);

    }


    public function on_trip($builder,$value=0){

        if($value==1)
        {
            $builder->where('is_cancelled', false)->where('user_rated', false)->where('is_driver_started', true)->orderBy('updated_at','DESC');
        }


    }

    public function is_cancelled($builder, $value=0)
    {
        $builder->where('is_cancelled', $value);
    }

    public function is_later($builder, $value=0)
    {
       $builder->where('is_later', $value)->where('is_completed',false)->where('is_cancelled',false)->where('is_driver_started', false);

    }

    public function is_trip_start($builder, $value = 0)
    {
        $builder->where('is_trip_start', $value)->where('is_cancelled', 0);
    }

    public function is_paid($builder, $value = 0)
    {
        $builder->where('is_paid', $value);
    }

    public function payment_opt($builder, $value = 0)
    {
        $builder->where('payment_opt', $value);
    }

    public function vehicle_type($builder, $value = 0)
    {
        $builder->whereHas('zoneType.vehicleType', function ($q) use ($value) {// --------------------------------------
            $q->where('id', $value);
        });
    }
    public function is_assigned($builder, $value = 0)
    {
        $builder->where('is_trip_start', $value)->where('is_cancelled', 0)->where('is_completed', 0);
    }

    public function date_option($builder, $value = 0)
    {
        if ($value == 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($value == 'week') {
            $from = now()->startOfWeek();
            $to = now()->endOfWeek();
        } elseif ($value == 'month') {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();
        } elseif ($value == 'year') {
            $from = now()->startOfYear();
            $to = now()->endOfYear();
        } else {
            $date = explode('<>', $value);
            $from = Carbon::parse($date[0])->startOfDay();
            $to = Carbon::parse($date[1])->endOfDay();
        }
        $builder->whereBetween('created_at', [$from, $to]);
    }


    public function vehicle_type_id($builder, $value = 0)
    {
        $builder->whereHas('zoneType', function ($q) use ($value) {// --------------------------------------
            $q->whereIn('type_id', $value);
        });
    }
    public function zone_id($builder, $value = 0)
    {
        $builder->whereHas('zoneType', function ($q) use ($value) {// --------------------------------------
            $q->whereIn('zone_id', $value);
        });
    }
    public function ride_status($builder, $value = 'all')
    {
        switch ($value) {
            case 'cancelled':
                $builder->where('is_cancelled', true);
                break;
            case 'completed':
                $builder->where('is_completed', true);
                break;
            case 'upcoming':
                $builder->where('is_cancelled', false)->where('is_completed', false)->where('is_later', true)->where('is_out_station',false)->where('is_trip_start', false);
                break;
            case 'outstation':
                $builder->where('is_cancelled', false)->where('is_completed', false)->where('is_later', true)->where('is_out_station',true)->where('is_trip_start', false);
                break;
            case 'ontrip':
                $builder->where('is_cancelled', false)->where('is_completed', false);
                break;
        }
    }
    public function transport_type($builder, $value = 'all'){
        if($value !== 'all'){
            $builder->where('transport_type',$value);
        }
    }
    public function is_bid_ride($builder, $value = 'all'){
        $builder->where('is_bid_ride',$value);
    }

}
