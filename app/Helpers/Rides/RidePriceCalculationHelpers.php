<?php

namespace App\Helpers\Rides;

use Kreait\Firebase\Contract\Database;
use Sk\Geohash\Geohash;
use Carbon\Carbon;
use App\Models\Request\RequestMeta;
use Illuminate\Support\Facades\DB;
use App\Models\Request\Request;
use Illuminate\Support\Facades\Log;
use App\Base\Constants\Setting\Settings;
use App\Models\Admin\Driver;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Request\DriverRejectedRequest;
use App\Jobs\NoDriverFoundNotifyJob;
use App\Models\Admin\ZoneSurgePrice;
use App\Models\Request\RequestCancellationFee;
use App\Models\Admin\PromoUser;
use App\Helpers\Rides\CalculatAdminCommissionAndTaxHelper;

trait RidePriceCalculationHelpers
{
    use CalculatAdminCommissionAndTaxHelper;



    /**
     * Calculate Ride fare
     * pick lat,pick lng, drop lat, drop lng should be double
     * total_distance can be double
     * duration should be in integer and in mins
     * 
     */
    //
    protected function calculateBillForARide($pick_lat,$pick_lng,$drop_lat,$drop_lng,$total_distance, $duration, $zone_type, $type_prices, $coupon_detail,$timezone,$user_id,$waiting_time,$request_detail,$driver,$airport_surge_fee)
    {

        $is_round = (integer)get_settings('can_round_the_bill_values');
        
        /**
         * Distance price calculation starts here
         * 
         * */

        // Pricing Parameters
        $price_per_distance = $type_prices->price_per_distance;

        // $ride_setting_distance = (integer)get_settings('minimum_trip_distane');

        // $dropoff_distance_in_meters = request()->distance;

        // $distance = $dropoff_distance_in_meters / 1000;
        
        if(request()->has('is_out_station') && request()->is_out_station){
            // Log::info("Driver True");
            $price_per_distance = $type_prices->outstation_price_per_distance;

        }
        if($driver && $driver->price_per_distance!=null)
        {
            // Log::info("Driver True");
            $price_per_distance = $driver->price_per_distance;

            // Log::info($price_per_distance);


        }

        $base_distance = (double) $type_prices->base_distance;

        if(request()->has('is_out_station') && request()->is_out_station){
            
            $base_distance = (double) $type_prices->outstation_base_distance;

        }
        $calculatable_distance = ($total_distance - $base_distance);

        if($calculatable_distance < 0 ){

            $calculatable_distance = 0;
        }


        // Calculate Surge price 
        $current_time = Carbon::now()->setTimezone($timezone);
        $day = $current_time->dayName;
        $current_time = $current_time->toTimeString();


        $zone_surge_price = ZoneSurgePrice::where('zone_type_id',$zone_type->id)->where('day',$day)->whereTime('start_time','<=',$current_time)->whereTime('end_time','>=',$current_time)->first();
        if($zone_surge_price){
            
            $surge_percent = $zone_surge_price->value;

            $surge_price_additional_cost = ($price_per_distance * ($surge_percent / 100));

            $price_per_distance += $surge_price_additional_cost;

        }


         // Base price
         
        $base_price = $type_prices->base_price;

        if(request()->has('is_out_station') && request()->is_out_station){
            // Log::info("Driver True");
            $base_price = $type_prices->outstation_base_price;

        }
         // Time price
        $price_per_time = $type_prices->price_per_time;
        
        if(request()->has('is_out_station') && request()->is_out_station){
            $time_price = $duration * $type_prices->outstation_price_per_time;
            // Log::info("Driver True");
   
           }
         $time_price = $duration * $type_prices->price_per_time;

         $time_price = round($time_price,2);

        // Distance Price

        $distance_price = ($calculatable_distance * $price_per_distance);

        $distance_price = round($distance_price,2);

         // Waiting charge
        $waiting_charge = $waiting_time * $type_prices->waiting_charge;

        $waiting_charge = round($waiting_charge,2);

        $sub_total = $base_price + $time_price + $distance_price + $waiting_charge + $airport_surge_fee;

        $sub_total = round($sub_total,2);

        if($is_round){
            
        // Time price
         $time_price = ceil($time_price);

        // Distance Price

        $distance_price = ceil($distance_price);

         // Waiting charge
        $waiting_charge = ceil($waiting_charge);


        $sub_total = ceil($sub_total);

        }

       
        /**
         * Apply Coupon
         * 
         * */

        $discount_amount = 0;
        $coupon_applied_sub_total = 0;
        $calculated_sub_total_for_coupon = $sub_total;
        $promo_applied = false;

        if ($coupon_detail) {
        $coupon_applied_sub_total = $sub_total;

            if ($coupon_detail->minimum_trip_amount <= $sub_total) {
                $discount_amount = $sub_total * ($coupon_detail->discount_percent/100);
                if ($coupon_detail->maximum_discount_amount>0 && $discount_amount > $coupon_detail->maximum_discount_amount) {
                    $discount_amount = $coupon_detail->maximum_discount_amount;
                }

                $coupon_applied_sub_total = $sub_total - $discount_amount;

                $coupon_applied_sub_total = round($coupon_applied_sub_total,2);

                if($is_round){

                    $coupon_applied_sub_total = ceil($coupon_applied_sub_total);
                }

                $calculated_sub_total_for_coupon = $coupon_applied_sub_total;
                
                $promo_applied= true;
            }else{

            if($request_detail){
                $promoUsers =  PromoUser::where('request_id', $request_detail->id)->delete();
            }



            }
        }


        // Apply coupon ends here

        if($request_detail){

        // Calculate Ride Cancellation Fee
        $cancellation_fee = RequestCancellationFee::where('user_id',$request_detail->user_id)->where('is_paid',0)->sum('cancellation_fee');

        $cancellation_fee = round($cancellation_fee,2);

        if($is_round){
            $cancellation_fee = ceil($cancellation_fee);
        }
        $sub_total += $cancellation_fee;

        if($cancellation_fee >0){

            RequestCancellationFee::where('user_id',$request_detail->user_id)->update([
                'is_paid'=>1,
                'paid_request_id'=>$request_detail->id]);

        }

         if($request_detail->is_bid_ride || $request_detail->is_trip_meter){
                
                $sub_total=$request_detail->accepted_ride_fare;
            }


        }

        
        $discount_admin_commision_and_tax = $this->calculateAdminCommissionAndTax($calculated_sub_total_for_coupon,$zone_type,$request_detail);

        $discount_tax_amount = $discount_admin_commision_and_tax['tax_amount'];

        $discount_admin_commision = $discount_admin_commision_and_tax['admin_commision'];

         $admin_commision_and_tax = $this->calculateAdminCommissionAndTax($sub_total,$zone_type,$request_detail);

        $admin_commision = $admin_commision_and_tax['admin_commision'];

        $tax_amount = $admin_commision_and_tax['tax_amount'];

        $tax_percent = $admin_commision_and_tax['tax_percent'];

        // Admin commision with tax amount
        $admin_commision_with_tax = $tax_amount + $admin_commision;

        $discounted_total_price = $coupon_applied_sub_total + $discount_tax_amount + $discount_admin_commision;

        $discounted_total_price = round($discounted_total_price,2);

        if($is_round){

            $discounted_total_price = ceil($discounted_total_price);
        }
        
        // Driver.0 Commissions Starts Here

        $driver_commision = $sub_total;

        $sub_total = $admin_commision_and_tax['sub_total'];

        if($driver && $driver->owner_id != NULL){

            $admin_commission_type_for_driver = $zone_type->admin_commission_type_for_owner ?? 0;
            $service_fee_for_driver = $zone_type->admin_commission_for_owner ?? 0;
        }else {
            $admin_commission_type_for_driver = $zone_type->admin_commission_type_from_driver ?? 0;
            $service_fee_for_driver = $zone_type->admin_commission_from_driver ?? 0;
        }

        if($admin_commission_type_for_driver==1){

        $admin_commission_from_driver = ($driver_commision * ($service_fee_for_driver / 100));

        }else{

            $admin_commission_from_driver = $service_fee_for_driver;

        }
        if($request_detail?->driverDetail?->is_subscribed && get_settings('driver_register_module') !== 'commission'){
            $admin_commission_from_driver = 0;
        }

        if(isset($cancellation_fee) && $cancellation_fee > 0){
            $admin_commission_from_driver += $cancellation_fee;
        }

        $driver_commision -= $admin_commission_from_driver;


        if($request_detail && $request_detail->is_bid_ride){
            $driver_commision -= $admin_commision_with_tax;
        }
        // Driver Commissions Ends Here

        // Total Amount
        $total_amount = $sub_total + $admin_commision_with_tax;

        
        if ($request_detail && $request_detail->additional_charges_amount > 0){
            $total_amount += $request_detail->additional_charges_amount;
        }


        if($is_round==0){
            $total_amount = round($total_amount,2);

        }
        $admin_commision_with_tax += $admin_commission_from_driver;

        $pickup_duration = 0;
        $dropoff_duration = $duration;
        $wait_duration = 0;
        $duration = $pickup_duration + $dropoff_duration + $wait_duration;
        $price_per_distance = round($price_per_distance,2);
        if($is_round){

            $price_per_distance = ceil($price_per_distance);
        }


        if($request_detail){

            
            $result = [
                'base_price'=>$base_price,
                'base_distance'=>$base_distance ?? 0,
                'price_per_distance'=>$price_per_distance,
                'distance_price'=>$distance_price,
                'price_per_time'=>$price_per_time,
                'time_price'=>$time_price,
                'promo_discount'=>$discount_amount,
                'waiting_charge'=>$waiting_charge,
                'service_tax'=>$tax_amount,
                'service_tax_percentage'=>$tax_percent,
                'admin_commision'=>$admin_commision,
                'admin_commision_with_tax'=>$admin_commision_with_tax,
                'driver_commision'=>$driver_commision,
                'admin_commission_from_driver'=>$admin_commission_from_driver,
                'total_amount'=> $total_amount,
                'total_distance'=>$total_distance,
                'total_time'=>$duration,
                'airport_surge_fee'=>$airport_surge_fee,
                'cancellation_fee'=>$cancellation_fee,
                ];

            if($discount_amount>0){

                $result['total_amount'] = $discounted_total_price;
                $result['admin_commision'] = $discount_admin_commision;
                $result['service_tax'] = $discount_tax_amount;
                $result['admin_commision_with_tax'] = ($discount_admin_commision + $discount_tax_amount);

            }

        return $result;


        }


        // return calculated params for eta
        return (object)[
                'distance' => $total_distance,
                'base_distance' => $base_distance,
                'base_price' => $base_price,
                'price_per_distance' => $price_per_distance,
                'price_per_time' => $price_per_time,
                'distance_price' => $distance_price,
                'time_price' => $time_price,
                'subtotal_price' => $sub_total,
                'calculated_distance' => number_format($calculatable_distance,2),
                'tax_percent' => $tax_percent,
                'tax_amount' => $tax_amount,
                'discount_total_tax_amount'=>$discount_tax_amount,
                'without_discount_admin_commision'=>$admin_commision,
                'discount_admin_commision'=>$discount_admin_commision,
                'total_price' => $total_amount,
                'discounted_total_price'=>$discounted_total_price,
                'discount_amount'=>$discount_amount,
                'pickup_duration' => $pickup_duration,
                'dropoff_duration' => $dropoff_duration,
                'wait_duration' =>$wait_duration,
                'duration' => $duration,
                'airport_surge_fee'=>$airport_surge_fee
            ];




    }

    protected function calculateRentalRideFares($zone_type_price, $distance, $duration, $waiting_time, $coupon_detail,$request_detail,$airport_surge_fee){

        // Distance Price
        $calculatable_distance = $distance - $zone_type_price->free_distance;
        $calculatable_distance = $calculatable_distance<0?0:$calculatable_distance;

        $price_per_distance = $zone_type_price->distance_price_per_km;

        // Validate if the current time in surge timings

        $timezone = $request_detail->serviceLocationDetail->timezone;

        $current_time = Carbon::now()->setTimezone($timezone);
        
        $day = $current_time->dayName;
        $current_time = $current_time->toTimeString();

        $zone_surge_price = $request_detail->zoneType->zoneSurge()->where('day',$day)->whereTime('start_time','<=',$current_time)->whereTime('end_time','>=',$current_time)->first();

        if($zone_surge_price){

            $surge_percent = $zone_surge_price->value;

            $surge_price_additional_cost = ($price_per_distance * ($surge_percent / 100));

            $price_per_distance += $surge_price_additional_cost;

            $request_detail->is_surge_applied = true;

            $request_detail->save();

        }

        $distance_price = $calculatable_distance * $price_per_distance;
        // Time Price
        $ride_duration = $duration > $zone_type_price->free_min ? $duration-$zone_type_price->free_min: 0; 
        $time_price = ($ride_duration) * $zone_type_price->time_price_per_min;
        // Waiting charge
        $waiting_charge = $waiting_time * $zone_type_price->waiting_charge;
        // Base Price
        $base_price = $zone_type_price->base_price;

        // Sub Total

        if($request_detail->zoneType->vehicleType->is_support_multiple_seat_price && $request_detail->passenger_count > 0){

            if($request_detail->passenger_count ==1){
                $seat_discount = $request_detail->zoneType->vehicleType->one_seat_price_discount;
            }
            if($request_detail->passenger_count ==2){
                $seat_discount = $request_detail->zoneType->vehicleType->two_seat_price_discount;
            }
            if($request_detail->passenger_count ==3){
                $seat_discount = $request_detail->zoneType->vehicleType->three_seat_price_discount;
            }
            if($request_detail->passenger_count ==4){
                $seat_discount = $request_detail->zoneType->vehicleType->four_seat_price_discount;
            }

            $base_price -= ($base_price * ($seat_discount / 100));

            $distance_price -=  ($distance_price * ($seat_discount / 100));

            $time_price -=  ($time_price * ($seat_discount / 100));

            $airport_surge_fee -= ($airport_surge_fee * ($seat_discount / 100));

        }

        $sub_total = $base_price+$distance_price+$time_price+$waiting_charge + $airport_surge_fee;


        $discount_amount = 0;

         if ($coupon_detail) {
            if ($coupon_detail->minimum_trip_amount < $sub_total) {

                $discount_amount = $sub_total * ($coupon_detail->discount_percent/100);
                if ($discount_amount > $coupon_detail->maximum_discount_amount) {
                    $discount_amount = $coupon_detail->maximum_discount_amount;
                }

                $sub_total = $sub_total - $discount_amount;
            }
        }
        $zone_type = $request_detail->zoneType;

        // Get service tax percentage from settings
        $tax_percent = $zone_type->service_tax;
        $tax_amount = ($sub_total * ($tax_percent / 100));

        // Get Admin Commision
        $admin_commision_type = $zone_type_price->zoneType->admin_commision_type;
        $service_fee = $zone_type_price->zoneType->admin_commision;
        $tax_percent = $zone_type_price->zoneType->service_tax; 
        $tax_amount = ($sub_total * ($tax_percent / 100));
        $admin_commission_type_for_driver = get_settings('admin_commission_type_for_driver');

        $driver = auth()->user()->driver;



        if($driver->owner_id != NULL){
            $admin_commission_type_for_driver = get_settings('admin_commission_type_for_owner');
            $service_fee_for_driver = get_settings('admin_commission_for_owner');
        }
        else {
            $admin_commission_type_for_driver = get_settings('admin_commission_type_for_driver');
            $service_fee_for_driver = get_settings('admin_commission_for_driver');
        }
        
        if($admin_commision_type==1){

        $admin_commision = ($sub_total * ($service_fee / 100));

        }else{

            $admin_commision = $service_fee;

        }
        // Admin commision with tax amount
        $admin_commision_with_tax = $tax_amount + $admin_commision;
        $driver_commision = $sub_total+$discount_amount;
        // Driver Commission
        if($coupon_detail && $coupon_detail->deduct_from==2){
            $driver_commision = $sub_total;
        }

        if($admin_commission_type_for_driver==1){

        $admin_commission_from_driver = ($driver_commision * ($service_fee_for_driver / 100));

        }else{

            $admin_commission_from_driver = $service_fee_for_driver;

        }

        $driver_commision -= $admin_commission_from_driver;

        // Total Amount
        $total_amount = $sub_total + $admin_commision_with_tax;
        $admin_commision_with_tax += $admin_commission_from_driver;

        return $result = [
        'base_price'=>$base_price,
        'base_distance'=>$zone_type_price->free_distance,
        'price_per_distance'=>$zone_type_price->distance_price_per_km,
        'distance_price'=>$distance_price,
        'price_per_time'=>$zone_type_price->time_price_per_min,
        'time_price'=>$time_price,
        'promo_discount'=>$discount_amount,
        'waiting_charge'=>$waiting_charge,
        'service_tax'=>$tax_amount,
        'service_tax_percentage'=>$tax_percent,
        'admin_commision'=>$admin_commision,
        'admin_commision_with_tax'=>$admin_commision_with_tax,
        'driver_commision'=>$driver_commision,
        'total_amount'=>$total_amount,
        'total_distance'=>$distance,
        'total_time'=>$duration,
        'airport_surge_fee'=>$airport_surge_fee
        ];
    }

    
}
