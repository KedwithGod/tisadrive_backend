<?php

namespace App\Transformers\User;

use Carbon\Carbon;
use App\Models\Admin\Zone;
use App\Models\Admin\Promo;
use App\Models\Admin\Driver;
use App\Models\Admin\ZoneType;
use App\Models\Admin\PromoCodeUser;
use App\Models\Admin\PromoUser;
use App\Transformers\Transformer;
use App\Models\Admin\ZoneSurgePrice;
use App\Models\Master\DistanceMatrix;
use Illuminate\Support\Facades\Redis;
use App\Helpers\Exception\ExceptionHelpers;
use App\Base\Constants\Masters\EtaConstants;
use App\Base\Constants\Masters\zoneRideType;
use App\Transformers\Access\RoleTransformer;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\Log;
use App\Helpers\Rides\RidePriceCalculationHelpers;

class EtaTransformer extends Transformer
{
    use ExceptionHelpers,RidePriceCalculationHelpers;
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
    public function transform(ZoneType $zone_type)
    {
        $pick_lat = request()->pick_lat;
        $pick_lng = request()->pick_lng;
        $drop_lat = request()->drop_lat;
        $drop_lng = request()->drop_lng;

        $response =  [
            'zone_type_id' => $zone_type->id,
            'name' => $zone_type->vehicleType->name,
            'vehicle_icon' => $zone_type->vehicleType->icon,
            'description'=> $zone_type->vehicleType->description,
            'short_description'=> $zone_type->vehicleType->short_description,
            'supported_vehicles'=> $zone_type->vehicleType->supported_vehicles,
            'size'=> $zone_type->vehicleType->size,
            'capacity'=> $zone_type->vehicleType->capacity,
            'payment_type'=>$zone_type->payment_type,
            'is_default'=>false,
            'enable_bidding'=>false,
            'is_out_station'=>false

        ];

        if($zone_type->transport_type=='taxi'){
            if ($zone_type->zone->default_vehicle_type==$zone_type->type_id) {
            $response['is_default'] = true;
        }
        }else{
            if ($zone_type->zone->default_vehicle_type_for_delivery==$zone_type->type_id) {
            $response['is_default'] = true;
        }
        }


        if (!request()->has('vehicle_type')) {
            $response['icon'] = $zone_type->icon;
            $response['type_id']=$zone_type->type_id;
        }
        /**
         * get prices from zone type
         */
            $ride_type = zoneRideType::RIDENOW;

        $coupon_detail = null;

        if (request()->has('promo_code') && request()->input('promo_code')) 
        {
                if(request()->has('vehicle_type'))
                {
                    if(request()->input('vehicle_type')==$zone_type->id){
                        $coupon_detail = $this->validate_promo_code($zone_type->zone->service_location_id,$zone_type);

                    }

                }else{

                        $coupon_detail = $this->validate_promo_code($zone_type->zone->service_location_id,$zone_type);
                }


        }


        $type_prices = $zone_type->zoneTypePrice()->where('price_type', $ride_type)->first();

        $distance_in_unit = 0;
        $total_duration = 0;

        if (request()->has('drop_lat') && request()->has('drop_lng') && request()->drop_lat) {
            
            if(request()->distance && request()->duration){

            $dropoff_distance_in_meters = request()->distance;
            $total_duration = request()->duration;

            if ($dropoff_distance_in_meters) {
                $distance_in_unit = $dropoff_distance_in_meters / 1000;

                $ride_setting_distance = (integer)get_settings('minimum_trip_distane');

                if($distance_in_unit > $ride_setting_distance){

                    $response['is_out_station'] = true;

                }
                if ($zone_type->zone->unit==2) {
                    $distance_in_unit = kilometer_to_miles($distance_in_unit);

                }
                $distance_in_unit = round($distance_in_unit,2);
            }
                
            }
           


        }


        $near_driver_status = 0; //its means there is no driver available

        $driver_lat = $pick_lat;
        $driver_lng = $pick_lng;
        $near_driver = null;
        if (request()->has('drivers')) {
            $driver_data_with_distance = [];
            $driver_distance = [];
            foreach (json_decode(request()->drivers) as $key => $driver) {
                $driver_data = new \stdClass();
                $driver_data->id = $driver->driver_id;
                $driver_data->lat = $driver->driver_lat;
                $driver_data->lng = $driver->driver_lng;
                $driver_data->distance = self::calculate_distance(request()->pick_lat, request()->pick_lng, $driver->driver_lat, $driver->driver_lng, 'K');
                $driver_data_with_distance []= $driver_data;
                $driver_distance[] = $driver_data->distance;
            }

            $min_distance_driver = min($driver_distance);

            foreach ($driver_data_with_distance as $key => $driver_data) {
                if ($min_distance_driver==$driver_data->distance) {
                    $near_driver = $driver_data;
                    break;
                }
            }

            if ($near_driver==null) {
                $driver_lat = $pick_lat;
                $driver_lng = $pick_lng;
            } else {
                $driver_lat = $near_driver->lat;
                $driver_lng = $near_driver->lng;
                $near_driver_status=1;
            }
        }
        $user_balance = 0;


// userWallet
    $user = auth()->user();
    if($user!=null)
    {
        if(!auth()->user()->hasRole(Role::DRIVER))
        {



        $user_balance = $user->userWallet ? $user->userWallet->amount_balance : 0;

        //$user_balance =  $user->userWallet->amount_balance;
        }

    }


        $response['user_wallet_balance'] = $user_balance;


        // $driver_to_pickup = $this->db_query_previous_pickup_dropoff($driver_lat, $driver_lng, $pick_lat, $pick_lng);

        // $driver_to_pickup_response = json_decode($driver_to_pickup->json_result);
        if ($zone_type->zone->unit==1) {
            $unit_in_words = 'KM';
        } else {
            $unit_in_words = 'MILES';
        }
        // $unit_in_words = EtaConstants::ENGLISH_UNITS[$zone_type->zone->unit];
        $translated_unit_in_words = $unit_in_words;
        // $airport_surge_fee =0;
        
        // if (request()->has('is_airport'))
        // {
        //     $airport_surge_fee =  $zone_type->airport_surge;
        // }

        $airport_surge = find_airport(request()->pick_lat,request()->pick_lng);
        if($airport_surge==null && request()->drop_lat)
        {
            $airport_surge = find_airport(request()->drop_lat,request()->drop_lng);
        }


        $airport_surge_fee = 0;

        if($airport_surge){

            $airport_surge_fee =  $zone_type->airport_surge;

        }
        $timezone = $zone_type->zone->serviceLocation->timezone;

        // Log::info("distance_in_unit");
        // Log::info($distance_in_unit);
        // Log::info("distance_in_unit");

        $ride = $this->calculateBillForARide($pick_lat,$pick_lng,$drop_lat,$drop_lng,$distance_in_unit, $total_duration, $zone_type, $type_prices, $coupon_detail,$timezone,null,0,null,null,$airport_surge_fee);


        if ($near_driver_status != 0) {
            if ($ride->pickup_duration != 0) {
                $driver_arival_estimation = "{$ride->pickup_duration} min";
            } else {
                $driver_arival_estimation = "1 min";
            }
        } else {
            $driver_arival_estimation = "--";
        }

        
        $app_for = config('app.app_for');

        if($app_for=='taxi' || $app_for=='delivery')
        {
            if(request()->has('transport_type') && request()->transport_type == "taxi") {
                $response['transport_type'] = "taxi";
            }
            if(request()->has('transport_type') && request()->transport_type == "delivery") {
                $response['transport_type'] = "delivery";
            }
        }
        $response['has_discount'] = false;
        if ($ride->discount_amount > 0) {
            $response['has_discount'] = true;
            $response['discounted_totel'] = round($ride->discounted_total_price, 2);
            $response['discount_total_tax_amount'] = $ride->discount_total_tax_amount;
            $response['promocode_id'] = $coupon_detail->id;
        }
        $response['discount_amount'] = $ride->discount_amount;
        $response['distance'] = $ride->distance;
        $response['distance_in_meters'] = request()->distance;
        $response['time'] = $ride->duration;
        $response['base_distance'] = $ride->base_distance;
        $response['calculated_distance'] = $ride->calculated_distance;

        $response['free_waiting_time_in_mins_before_trip_start'] = $type_prices->free_waiting_time_in_mins_before_trip_start;
        $response['free_waiting_time_in_mins_after_trip_start'] = $type_prices->free_waiting_time_in_mins_after_trip_start;
        $response['waiting_charge'] = $type_prices->waiting_charge;

        $response['base_price'] = $ride->base_price;
        $response['price_per_distance'] = $ride->price_per_distance;
        $response['price_per_time'] = $ride->price_per_time;
        $response['distance_price'] = $ride->distance_price;
        $response['time_price'] = $ride->time_price;
        $response['ride_fare'] = round($ride->subtotal_price,2);
        $response['tax_amount'] = $ride->tax_amount;
        $response['without_discount_admin_commision'] = round($ride->without_discount_admin_commision,2);
        $response['discount_admin_commision'] = round($ride->discount_admin_commision,2);
        $response['tax'] = $ride->tax_percent;
        $response['total'] = $ride->total_price;
        $response['approximate_value'] = 1;
        $response['min_amount'] = $ride->total_price;
        $maxamount=$ride->total_price * 1.05;
        $response['max_amount'] = round($maxamount,2);
        $response['currency'] = $zone_type->zone->serviceLocation->currency_symbol;
        $response['currency_name'] = $zone_type->zone->serviceLocation->currency_code;
        $response['type_name'] = $zone_type->vehicleType->name;
        $response['dispatch_type'] = $zone_type->vehicleType->trip_dispatch_type;
        $response['unit'] = (int) $zone_type->zone->unit;
        $response['unit_in_words_without_lang'] = $unit_in_words;
        $response['unit_in_words'] = $translated_unit_in_words;
        $response['airport_surge_fee'] = $ride->airport_surge_fee;
        $response['bidding_low_percentage'] = get_settings('bidding_low_percentage');
        $response['bidding_high_percentage'] = get_settings('bidding_high_percentage');
      
        // Log::info("ETA Response");
      
        // Log::info($response);

        // dd($previous_pickup_dropoff);
        // $response['trip_dispatch_type'] = $zone_type->vehicleType->trip_dispatch_type;

                if($zone_type->vehicleType->trip_dispatch_type !== 'normal'){

                    $response['enable_bidding'] = true;

                }

                if(get_settings('show_rental_ride_feature')=='0'){
                    $response['show_rental_ride'] = false;
                }


        return $response;
    }




    public function validate_promo_code($service_location)
    {
        $app_for = config('app.app_for');


        $user = auth()->user();
        if (!request()->has('promo_code')) {
            return $coupon_detail = null;
        }
        $promo_code = request()->input('promo_code');
        // Validate if the promo is expired
        $current_date = Carbon::today()->toDateTimeString();

        if($app_for=='taxi' || $app_for=='delivery')
        {      
        $expired = Promo::where('code', $promo_code)->where('service_location_id',$service_location)->where('to', '>', $current_date)->first();
        }else{
            $transport_type = request()->transport_type;
            $expired = Promo::where('code', $promo_code)->where('service_location_id',$service_location)->where(function($query)use($transport_type){
            $query->where('transport_type',$transport_type)->orWhere('transport_type','both');
            })->where('to', '>', $current_date)->where('active',true)->first();

        }

        if (!$expired) {
            $this->throwCustomException('Invalid Promo Code');
        }
        $validate_promo_code = true;
        if($expired->user_specific){
            $validate_promo_code = $expired->promoCodeUsers()->where('user_id',$user->id)->first();
            // Log::info($validate_promo_code);
        }
        if(!$validate_promo_code)
        {
            $this->throwCustomException('provided promo code invalid');
        }

        $exceed_usage = PromoUser::where('promo_code_id', $expired->id)->where('user_id', $user->id)->count();
        // Log::info($user);
        // Log::info($exceed_usage);
        // Log::info("testt");
        // Log::info(json_encode($expired));
        if ($exceed_usage >= $expired->uses_per_user) {
            $this->throwCustomException('provided promo code expired or invalid');
        }

        return $expired;
    }
}