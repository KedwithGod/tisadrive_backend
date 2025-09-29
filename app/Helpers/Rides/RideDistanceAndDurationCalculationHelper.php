<?php

namespace App\Helpers\Rides;

use Kreait\Firebase\Contract\Database;
use Sk\Geohash\Geohash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Base\Constants\Setting\Settings;
use App\Models\Master\DistanceMatrix;

trait RideDistanceAndDurationCalculationHelper
{

    /**
     * Calculate Ride Distance & Duration
     * pick lat,pick lng, drop lat, drop lng should be double
     * total_distance can be double
     * duration should be in integer and in mins
     * 
     */
    protected function calculateDistanceAndDurationForARide($pick_lat,$pick_lng,$drop_lat,$drop_lng,$zone_type)
    {

        if(get_map_settings('map_type')=='open_street_map'){

                       $distance_and_duration = getDistanceMatrixByOpenstreetMap($pick_lat,$pick_lng,$drop_lat,$drop_lng);

                $dropoff_distance_in_meters=$distance_and_duration['distance_in_meters'];

                $dropoff_time_in_seconds=$distance_and_duration['duration_in_secs'];


            if(request()->has('stops') && request()->stops){

            $requested_stops = json_decode(request()->stops);


            foreach ($requested_stops as $key => $stop) {

        if($key==0){

                $distance_and_duration = getDistanceMatrixByOpenstreetMap($pick_lat,$pick_lng,$stop->latitude, $stop->longitude);

                $dropoff_distance_in_meters+=$distance_and_duration['distance_in_meters'];

                $dropoff_time_in_seconds+=$distance_and_duration['duration_in_secs'];


            if ($dropoff_distance_in_meters) {
                if ($zone_type->zone->unit==2) {
                    $distance_in_unit+=$distance_and_duration['distance_in_miles'];

                }else{
                    $distance_in_unit+=$distance_and_duration['distance_in_km'];
                }
            }


            }else{


                $distance_and_duration = getDistanceMatrixByOpenstreetMap($requested_stops[$key-1]->latitude, $requested_stops[$key-1]->longitude, $stop->latitude, $stop->longitude,);

                $dropoff_distance_in_meters+=$distance_and_duration['distance_in_meters'];

                $dropoff_time_in_seconds+=$distance_and_duration['duration_in_secs'];

            if ($dropoff_distance_in_meters) {

                if ($zone_type->zone->unit==2) {
                    $distance_in_unit+=$distance_and_duration['distance_in_miles'];

                }else{
                    $distance_in_unit+=$distance_and_duration['distance_in_km'];
                }
            }

            }

            }

            }

         }else
         {

                // get previous place json or store current one
                $previous_pickup_dropoff = $this->db_query_previous_pickup_dropoff($pick_lat, $pick_lng, $drop_lat, $drop_lng);

                $place_details = json_decode($previous_pickup_dropoff->json_result);

                $dropoff_distance_in_meters = get_distance_value_from_distance_matrix($place_details);

                $dropoff_time_in_seconds = get_duration_value_from_distance_matrix($place_details);


                  if(request()->has('stops') && request()->stops)
                  {

                        $requested_stops = json_decode(request()->stops);


                    foreach ($requested_stops as $key => $stop) 
                    {

                        if($key==0){
                            $previous_pickup_dropoff = $this->db_query_previous_pickup_dropoff($pick_lat, $pick_lng, $stop->latitude, $stop->longitude);

                        $place_details = json_decode($previous_pickup_dropoff->json_result);

                        $dropoff_distance_in_meters+= get_distance_value_from_distance_matrix($place_details);

                        if ($dropoff_distance_in_meters) {

                            $stop_distance_in_unit = $dropoff_distance_in_meters / 1000;
                            if ($zone_type->zone->unit==2) {
                                $distance_in_unit+= kilometer_to_miles($stop_distance_in_unit);

                            }else{
                                $distance_in_unit+=$stop_distance_in_unit;
                            }
                        }
                        }else
                        {

                        $previous_pickup_dropoff = $this->db_query_previous_pickup_dropoff($requested_stops[$key-1]->latitude, $requested_stops[$key-1]->longitude, $stop->latitude, $stop->longitude);

                        $place_details = json_decode($previous_pickup_dropoff->json_result);

                        $dropoff_distance_in_meters+= get_distance_value_from_distance_matrix($place_details);

                         if ($dropoff_distance_in_meters) 
                         {
                            $stop_distance_in_unit = $dropoff_distance_in_meters / 1000;
                            if ($zone_type->zone->unit==2) {
                                $distance_in_unit+= kilometer_to_miles($stop_distance_in_unit);

                            }else{
                                $distance_in_unit+=$stop_distance_in_unit;
                            }
                         }

                        }

                     }//foreach
                  }//request stops
                }

            // Distance 
                if ($dropoff_distance_in_meters) {
                $distance_in_unit = $dropoff_distance_in_meters / 1000;
                if ($zone_type->zone->unit==2) {
                    $distance_in_unit = kilometer_to_miles($distance_in_unit);

                }
            }

    }

    
}
