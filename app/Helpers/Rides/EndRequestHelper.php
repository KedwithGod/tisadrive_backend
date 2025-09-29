<?php

namespace App\Helpers\Rides;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;
use App\Base\Constants\Masters\UnitType;


trait EndRequestHelper
{

    /**
     * Calculate and charge Ride fare
     * @param Request $request_detail with generated bill
     * 
     */
    //
    protected function calculateDistanceAndDuration($distance_in_unit,$request_detail)
    {

        $trip_duration = $this->calculateDurationOfTrip($request_detail->trip_start_time);


        // $unit = $request_detail->zoneType->zone->unit == 2;
            
        //     if ($unit) {
        //         $distance_in_unit = kilometer_to_miles($distance_in_unit);

        //     }
            
            $app_distance = round($distance_in_unit,2);

            $distance_in_unit = $app_distance;  
            $duration = $trip_duration; 

        if($request_detail->requestEtaDetail()->exists()){
           
           $eta_duration =$request_detail->requestEtaDetail->total_time; 
            

            if($trip_duration<$eta_duration){

                $duration = $eta_duration;

            }

            
            

            $eta_detail = $request_detail->requestEtaDetail;

            if($eta_detail && $app_distance <= $eta_detail->total_distance){

                $distance_in_unit = $eta_detail->total_distance;  
                
            }



            if($request_detail->transport_type=='taxi' && get_settings('enable_eta_total_update') && !$request_detail->is_without_destination){

                
                    $distance_in_unit = $app_distance;

                    $duration = $trip_duration;


            }

            if($request_detail->transport_type=='delivery'){


                $distance_in_unit = $eta_detail->total_distance;  
                $duration = $eta_duration;  
                

            }

        }else{

            $distance_in_unit = $app_distance;  
            $duration = $trip_duration;  
        }


        return $distance_and_duration = ['distance'=>$distance_in_unit,'duration'=>$duration];

    
    }


    /**
     * Calculate Duration
     * @return $totald_duration number in minutes
     */
    protected function calculateDurationOfTrip($start_time)
    {

        $current_time = date('Y-m-d H:i:s');

        $start_time = Carbon::parse($start_time);
        // Log::info($start_time);
        $end_time = Carbon::parse($current_time);
        // Log::info($end_time);
        $totald_duration = $end_time->diffInMinutes($start_time);
        // Log::info($totald_duration);

        return $totald_duration;
    }


}
