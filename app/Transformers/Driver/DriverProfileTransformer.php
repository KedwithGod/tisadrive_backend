<?php

namespace App\Transformers\Driver;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin\Driver;
use App\Base\Constants\Auth\Role;
use App\Transformers\Transformer;
use App\Models\Request\RequestBill;
use App\Models\Request\RequestMeta;
use App\Models\Admin\DriverDocument;
use App\Models\Admin\DriverNeededDocument;
use App\Transformers\Access\RoleTransformer;
use App\Transformers\Requests\TripRequestTransformer;
use App\Base\Constants\Setting\Settings;
use App\Models\Admin\Sos;
use App\Models\Admin\Subscription;
use App\Models\Admin\SubscriptionDetail;
use App\Transformers\Driver\SubscriptionDetailTransformer;
use App\Transformers\Common\SosTransformer;
use App\Models\Admin\UserDriverNotification;
use App\Transformers\Common\DriverVehicleTypeTransformer;
use App\Transformers\Payment\DriverWalletTransformer;
use App\Models\Chat;
use App\Models\Admin\DriverAvailability;
use App\Models\Request\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;
use App\Transformers\Payment\RewardPointsTransformer;
use App\Transformers\Driver\LevelDetailTransformer;
use App\Models\Admin\Incentive;


class DriverProfileTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [
        'onTripRequest','metaRequest', 'level','subscription',
    ];

    /**
    * Resources that can be included default.
    *
    * @var array
    */
    protected array $defaultIncludes = [
        'sos','driverVehicleType','wallet','loyaltyPoint',
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Driver $user)
    {


       $authorization_code = $user->user->authorization_code;
        $app_for = config('app.app_for');

        $params = [
            'id' => $user->id,
            'user_id'=>$user->user_id,
            'owner_id' => $user->owner_id,
            'transport_type' => $user->transport_type ?? 'both',
            'name' => $user->name,
            'gender' => $user->user->gender,
            'email' => $user->email,
            'mobile' => $user->mobile_number,
            'profile_picture' => $user->profile_picture,
            'active' => (bool)$user->active,
            'approve' => (bool)$user->approve,
            'available' => (bool)$user->available,
            'uploaded_document'=>false,
            'declined_reason'=>$user->reason,
            'service_location_id'=>$user->service_location_id,
            'service_location_name'=>$user->serviceLocation ? $user->serviceLocation->name: null,
            'vehicle_year'=>$user->vehicle_year,
            'vehicle_type_id'=> $user->vehicle_type,
            'vehicle_type_name'=>$user->vehicle_type_name,
            'vehicle_type_image'=>$user->vehicle_type_image,
            'vehicle_type_icon_for'=>$user->vehicle_type_icon_for,
            'car_make'=>$user->car_make,
            'car_model'=>$user->car_model,
            'car_make_name'=>$user->car_make_name,
            'car_model_name'=>$user->car_model_name,
            'car_color'=>$user->car_color,
            'driver_lat'=>$user->driver_lat,
            'driver_lng'=>$user->driver_lng,
            'car_number'=>$user->car_number,
            'rating'=>round($user->rating, 2),
            'no_of_ratings' => $user->no_of_ratings,
            'timezone'=>$user->timezone,
            'refferal_code'=>$user->user->refferal_code,
            //'map_key'=>get_settings('google_map_key'),
            'company_key'=>$user->user->company_key,
            'driver_mode' => get_settings('driver_register_module'),
            'show_instant_ride'=>false,
            'is_delivery_app'=>false,
            'country_id'=>$user->user->countryDetail->id,
            'currency_pointer'=>$user->user->countryDetail->currency_pointer ?? "ltr",
            'currency_symbol' => $user->user->countryDetail->currency_symbol,
            'my_route_lat'=>$user->my_route_lat,
            'my_route_lng'=>$user->my_route_lng,
            'my_route_address'=>$user->my_route_address,
            'enable_my_route_booking'=>$user->enable_my_route_booking,
            'role'=>'driver',
            'enable_bidding'=>false,
            'authorization_code'=>$authorization_code,
            'languages' => $user->languages,
            'has_subsription'=>false,
            'is_subscribed'=>(bool)$user->is_subscribed,
            'is_expired'=>false,
            'fcm_token'=>$user->user->fcm_token,
            'show_driver_level_feature'=> get_settings('show_driver_level_feature') && !$user->owner_id,
            'enable_support_ticket_feature' =>  get_settings('enable_support_ticket_feature'),
            'enable_leaderboard_feature' =>  (bool) get_settings('enable_driver_leaderboard_feature'),
            'android_app' =>  "ANDROID - ".get_settings('android_driver'),
            'ios_app' =>  "IOS - ".get_settings('ios_driver'),

        ];

        $params['vehicle_types'] = [];
        $params['sub_vehicle_type'] = [];
        $params['enable_sub_vehicle_feature'] = '0';

        $params['enable_my_route_booking_feature'] =  0;


        if($app_for == 'delivery'){
            $params['is_delivery_app']= true;
        }
        if ($user->owner_id!=null) {

            if($user->vehicleType()->exists()){
            if($user->vehicleType->trip_dispatch_type!='bidding')
             {
                         $params['enable_my_route_booking_feature'] =  get_settings('enable_my_route_booking_feature');

             }
            }

        }
        $params['enable_bid_on_fare'] = false;

        $referral_comission = get_settings('referral_commission_amount_for_driver');
        $referral_comission_string = 'Refer a friend and earn'.$user->user->countryDetail->currency_symbol.''.$referral_comission;
        $params['referral_comission_string'] = $referral_comission_string;
        
        if($user->driverVehicleTypeDetail()->exists())
        {
            foreach ($user->driverVehicleTypeDetail as $key => $type) {


                $params['vehicle_types'][] = $type->vehicle_type;

                if($type->vehicleType->trip_dispatch_type=='bidding'){

                    $params['enable_bidding'] = true;
                     $params['enable_my_route_booking_feature'] =  get_settings('enable_my_route_booking_feature');

                }
                if($type->vehicleType->trip_dispatch_type=='both')
                {

                     $params['enable_bid_on_fare'] = true;
                }

          }
          $params['vehicle_type_icon_for'] = $user->driverVehicleTypeDetail()->where('signed_vehicle',true)->first()->vehicleType->icon_types_for;


          if (!$user->owner_id)  {

                $params['sub_vehicle_type'] = $user->driverVehicleTypeDetail()->where('signed_vehicle',false)
                ->WhereHas("vehicleType")
                ->join('vehicle_types', 'driver_vehicle_types.vehicle_type', '=', 'vehicle_types.id')
                ->select("vehicle_types.name", "driver_vehicle_types.vehicle_type")->get();
    
                $sub_vehicle_option_list = $user->driverVehicleTypeDetail()->where('signed_vehicle',true)->first()->vehicleType->subVehicleTypeDetail->count();
    
                $params['enable_sub_vehicle_feature'] = $sub_vehicle_option_list > 0 ? get_settings('enable_sub_vehicle_feature') :'0';
          }
        }
        if($user->service_location_id!=null)
        {
                    // Start from the driver and load necessary relationships
        $driverWithDetails = $user->load([
            'serviceLocation.zones.zoneType.zoneTypePrice' // Eager load relationships
        ]);

        $result['price_per_distance'] = 0;
        // Loop through the zones to find the price_per_distance
        if($driverWithDetails->serviceLocation)
        {
            foreach ($driverWithDetails->serviceLocation->zones as $zone) {
                foreach ($zone->zoneType as $zoneType) {
                    foreach ($zoneType->zoneTypePrice as $zoneTypePrice) {
                        // like checking the specific transport type
                        if ($zoneTypePrice->active) { // Example condition
                            $result['price_per_distance'] = $zoneTypePrice->price_per_distance;
                            break 3; // Break out of all loops if found
                        }
                    }
                }
            }
        }


         $params['price_per_distance'] =  $result['price_per_distance'];
     }


    if($user->price_per_distance>0)
    {
    $params['price_per_distance'] =  $user->price_per_distance;
        
    }
    $dailyIncentives = Incentive::where('mode', 'daily')->exists();
    $weeklyIncentives = Incentive::where('mode', 'weekly')->exists();
    
    if ($dailyIncentives && $weeklyIncentives) {
        $params['available_incentive'] = 2; // Both available
    } elseif ($dailyIncentives) {
        $params['available_incentive'] = 0; // Only daily available
    } elseif ($weeklyIncentives) {
        $params['available_incentive'] = 1; // Only weekly available
    } else {
        $params['available_incentive'] = null; // None available (optional case)
    }
    


// dd($params);

        $plans = [];
        if(get_settings('driver_register_module') !== 'commission' && !$user->owner_id )
        {

            if($user->subscription_detail_id && !$user->is_subscribed){
                $params['is_expired'] = true;
            }
            $vehicleTypes = $user->driverVehicleTypeDetail->pluck('vehicle_type');
            $plans = Subscription::active()->whereIn('vehicle_type_id',$vehicleTypes)->get();
            if(count($plans) > 0){
                $params['has_subsription'] = true;
            }
        }

        $notifications_count= UserDriverNotification::where('driver_id',$user->id)
            ->where('is_read',0)->count();

        $params['notifications_count']=$notifications_count;
        

        if($user->transport_type !== 'delivery' ){
            $params['show_instant_ride_feature_on_mobile_app'] =  get_settings('show_instant_ride_feature_on_mobile_app');
        }

        if($user->fleet_id){
            $fleetDetail = $user->fleetDetail()->withTrashed()->first();
            $params['car_make_name'] = $fleetDetail->car_make_name;
            $params['car_model_name'] = $fleetDetail->car_model_name;
            $params['car_number'] = $fleetDetail->license_number;
            $params['car_color'] = $fleetDetail->car_color;

        }else{
            if($user->owner_id) {
                $params['show_instant_ride_feature_on_mobile_app'] = '0';
            }

        }

        $params['enable_modules_for_applications'] =  get_settings('enable_modules_for_applications');
        $params['enable_map_appearance_change_on_mobile_app'] = (get_settings(Settings::ENABLE_MAP_APPEARANCE_CHANE_ON_MOBILE_APP));

        $params['contact_us_mobile1'] =  get_settings('contact_us_mobile1');
        $params['contact_us_mobile2'] =  get_settings('contact_us_mobile2');
        $params['contact_us_link'] =  get_settings('contact_us_link');
         $params['show_wallet_feature_on_mobile_app'] =  get_settings('show_wallet_feature_on_mobile_app_driver');
         if($user->owner_id) {
            $params['show_wallet_feature_on_mobile_app'] =  '0';
         }
        $params['show_bank_info_feature_on_mobile_app'] =  get_settings('show_bank_info_feature_on_mobile_app');

        $params['show_incentive_feature_for_driver'] =  get_settings('show_incentive_feature_for_driver');


        if($app_for == 'bidding'){

            $show_outstationfeature = get_settings('show_outstation_ride_feature') || get_settings('show_delivery_outstation_ride_feature');
            $params['show_outstation_ride_feature'] = $show_outstationfeature ? '1' : '0';
        }

                // $params['show_outstation_ride_feature'] =  "0";

        $params['how_many_times_a_driver_can_enable_the_my_route_booking_per_day'] =  get_settings('how_many_times_a_driver_can_enable_the_my_route_booking_per_day');


        $params['show_wallet_money_transfer_feature_on_mobile_app'] =  get_settings('shoW_wallet_money_transfer_feature_on_mobile_app_for_driver');

        $current_date = Carbon::now();

        $total_earnings = RequestBill::whereHas('requestDetail', function ($query) use ($user, $current_date) {
            $query->where('driver_id', $user->id)
                  ->where('is_completed', 1)
                  ->whereDate('trip_start_time', $current_date);
        })->sum('driver_commision');
        


        $timezone = $user->user->timezone;

        if($timezone==null){
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }
        $updated_current_date =  $current_date->setTimezone($timezone);

        $params['total_earnings'] = round($total_earnings, 2);
        $zone = find_zone($user->user->current_lat,$user->user->current_lng);
        $distance_unit = "";
        if($zone) {
            $distance_unit = $zone->unit == 1 ? "km" : "mi";
        }
        $params['distance_unit'] = $distance_unit;
        $params['current_date'] = $updated_current_date->toDateString();


        $today = Carbon::today();

         // Driver duties
        $total_minutes_online = DriverAvailability::where('driver_id',$user->id)->where('created_at', '>=', $today)
        ->where('created_at', '<', $today->copy()->addDay())
        ->sum('duration');

        $params['total_minutes_online'] = $total_minutes_online;

        $lastOnlineRecord = DriverAvailability::where('driver_id',$user->id)
        ->orderBy('online_at', 'desc')
        ->first();

        $params['last_online_at'] = null;

        if($lastOnlineRecord){

            if($lastOnlineRecord->is_online){

                $currentDateTime = Carbon::now();

                $targetTime = Carbon::parse($lastOnlineRecord->online_at);

                $differenceInMinutes = $currentDateTime->diffInMinutes($targetTime);

                $params['total_minutes_online'] = $total_minutes_online + $differenceInMinutes;


            }

            $last_online_at = Carbon::parse($lastOnlineRecord->online_at)->setTimezone($timezone);

             $params['last_online_at'] = $last_online_at->toDateTimeString();

        }

        // Total Trip kms
        $total_trip_kms = Request::where('driver_id', $user->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->sum('total_distance');

        $params['total_trip_kms'] = number_format($total_trip_kms, 2);

        $total_trips = Request::where('driver_id', $user->id)->where('is_completed', 1)->whereDate('trip_start_time', $current_date)->get()->count();

        $params['total_trips'] = $total_trips;

        //Driver duties update ends

        if($user->owner_id){
            $driver_documents = DriverNeededDocument::active()->where(function($query){
                $query->where('account_type','fleet_driver')->orWhere('account_type','both');
            })->get();
        }else{

            $driver_documents = DriverNeededDocument::active()->where(function($query){
                $query->where('account_type','individual')->orWhere('account_type','both');
            })->get();
        }


        foreach ($driver_documents as $key => $needed_document) {
            if (DriverDocument::where('driver_id', $user->id)->where('document_id', $needed_document->id)->exists()) {

                $params['uploaded_document'] = true;

            } else {

                $request =  $user->requestDetail()->where(function($query){
                    $query->where('is_cancelled', false)->where('driver_rated', false)
                        ->where(function($subQuery){
                            $subQuery->where('is_driver_started', true)
                                    ->orwhere(function($deliveryQuery){
                                        $deliveryQuery->where('transport_type','delivery')->where('is_driver_arrived',true);
                                    });
                        });
                })->first();
                if(!$request){
                
                $user->update(['approve' => false]);

                    $params['uploaded_document'] = false;
                }


                break;
            }
        }

        $low_balance = false;

        $driver_wallet = $user->driverWallet;

        $wallet_balance= $driver_wallet?$driver_wallet->amount_balance:0;



        $minimum_balance = get_settings(Settings::DRIVER_WALLET_MINIMUM_AMOUNT_TO_GET_ORDER);

            if($user->owner_id){

            $minimum_balance = get_settings(Settings::OWNER_WALLET_MINIMUM_AMOUNT_TO_GET_ORDER);

            $owner_wallet = $user->owner->ownerWalletDetail;

            $wallet_balance= $owner_wallet?$owner_wallet->amount_balance:0;

            }

            if($minimum_balance >=0 && !$user->is_subscribed){
                if ($minimum_balance > $wallet_balance) {

                $user->active = false;

                $user->save();

                $params['active'] = false;


                $low_balance = true;
            }

            }
    // check if balance is in negative

          if($minimum_balance < 0 && !$user->is_subscribed)
          {
                if ($minimum_balance > $wallet_balance)
                {

                $user->active = false;

                $user->save();

                $params['active'] = false;


                $low_balance = true;
              }

         }
         $params['has_later'] = false;
        if(get_settings('show_outstation_ride_feature') || get_settings('show_delivery_outstation_ride_feature')){
            $driver_completed_rides = Request::where('driver_id', $user->id)->where('is_completed', true)->count();
            $params['overall_ride_count'] = $driver_completed_rides;
            $params['overall_amount_spent'] = $driver_wallet?$driver_wallet->amount_spent:0;
        }
        $outstation_rides = Request::where('driver_id', $user->id)->where('is_cancelled', false)->where('is_completed', false)->where('is_later', true)->where('is_trip_start', false)->exists();
        if($outstation_rides){
            $params['has_later'] = true;
        }
            $params['trip_accept_reject_duration_for_driver'] = get_settings(Settings::TRIP_ACCEPT_REJECT_DURATION_FOR_DRIVER);


            $params['maximum_time_for_find_drivers_for_bitting_ride'] = (get_settings(Settings::MAXIMUM_TIME_FOR_FIND_DRIVERS_FOR_BIDDING_RIDE));
            $params['bidding_amount_increase_or_decrease'] = (get_settings(Settings::BIDDING_AMOUNT_INCREASE_OR_DECREASE));
            $params['bidding_low_percentage'] = get_settings('bidding_low_percentage');
            $params['bidding_high_percentage'] = get_settings('bidding_high_percentage');

            $params['low_balance'] = $low_balance;
        $app_for = config('app.app_for');

        if($app_for=='delivery')
        {

                $params['enable_shipment_load_feature'] = get_settings(Settings::ENABLE_SHIPMENT_LOAD_FEATURE);
                    $params['enable_shipment_unload_feature'] = get_settings(Settings::ENABLE_SHIPMENT_UNLOAD_FEATURE);
                    $params['enable_digital_signature'] = get_settings(Settings::ENABLE_DIGITAL_SIGNATURE);
                    
        }elseif($app_for=='super' || $app_for=='bidding')
        {
            // Check if the 'transport_type' field exists in the request
            if (property_exists($user, 'transport_type')) {
                $transportType = $user->transport_type;

                // If 'transport_type' is 'delivery', add additional settings to the parameters
                if (($transportType === "delivery") || ($transportType === "both")) {
                    $params['enable_shipment_load_feature'] = get_settings(Settings::ENABLE_SHIPMENT_LOAD_FEATURE);
                    $params['enable_shipment_unload_feature'] = get_settings(Settings::ENABLE_SHIPMENT_UNLOAD_FEATURE);
                    $params['enable_digital_signature'] = get_settings(Settings::ENABLE_DIGITAL_SIGNATURE);
                }
            }

        }


            $params['conversation_id'] = "";
            $get_conversation_data = Conversation::where('user_id',$user->user->id)->where('is_closed', false)->first();
            if($get_conversation_data)
            {
                $params['conversation_id'] = $get_conversation_data->id;
            }


            $params['enable_vase_map'] = get_settings(Settings::ENABLE_VASE_MAP);

        if($user->user->is_deleted_at!=null)
        {
            $params['is_deleted_at'] = "Your Account Delete operation is Processing";
        }
        $params['map_type'] = $user->user->map_type ?? get_map_settings('map_type');

        $app_for = config('app.app_for');

        if($app_for=='taxi' || $app_for=='delivery')
        {
           $params['enable_modules_for_applications'] =  $app_for;
        }

        $completed_ride_count = $user->requestDetail()->where('is_completed', true)->count();

        $params['completed_ride_count'] = $completed_ride_count;

        return $params;
    }

    /**
     * Include the request of the driver.
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeOnTripRequest(Driver $user)
    {

        $request =  $user->requestDetail()->where(function($query){
            $query->where('is_cancelled', false)->where('driver_rated', false)
                ->where(function($subQuery){
                    $subQuery->where('is_driver_started', true)
                            ->orwhere(function($deliveryQuery){
                                $deliveryQuery->where('transport_type','delivery')->where('is_driver_arrived',true);
                            });
                });
        })->first();

        return $request
        ? $this->item($request, new TripRequestTransformer)
        : $this->null();
    }


    /**
    * Include the request meta of the user.
    *
    * @param User $user
    * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
    */
    public function includeSos(Driver $user)
    {

        $request = Sos::select('id', 'name', 'number', 'user_type', 'created_by')
        ->where('created_by', auth()->user()->id)
        ->orWhere('user_type', 'admin')
        ->orderBy('created_at', 'Desc')
        ->companyKey()->get();

        return $request
        ? $this->collection($request, new SosTransformer)
        : $this->null();
    }

    /**
     * Include the meta request of the driver.
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeMetaRequest(Driver $user)
    {
        $request_meta = RequestMeta::where('driver_id', $user->id)->where('active', true)->first();
        if ($request_meta) {
            $request = $request_meta->request;
            return $request
        ? $this->item($request, new TripRequestTransformer)
        : $this->null();
        }
        return $this->null();
    }
    /**
    * Include the request meta of the user.
    *
    * @param User $user
    * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
    */
    public function includeDriverVehicleType(Driver $user)
    {

        $driverVehicleType = $user->driverVehicleTypeDetail;

        return $driverVehicleType
        ? $this->collection($driverVehicleType, new DriverVehicleTypeTransformer)
        : $this->null();
    }

    /**
     * Include the favourite location of the user.
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeWallet(Driver $driver)
    {
        $driver_wallet = $driver->driverWallet;

        return $driver_wallet
        ? $this->item($driver_wallet, new DriverWalletTransformer)
        : $this->null();
    }
    /**
     * Include the meta request of the driver.
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeSubscription(Driver $user)
    {
        // dd($user);
        $detail = SubscriptionDetail::where('driver_id',$user->id)->where("id",$user->subscription_detail_id)->orderBy('created_at','DESC')->first();
        return $detail
        ? $this->item($detail, new SubscriptionDetailTransformer)
        : $this->null();
    }
    /**
     * Include the meta request of the driver.
     *
     * @param Driver $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeLevel(Driver $user)
    {
        // dd($user);
        $detail = $user->levelDetail;
        return $detail
        ? $this->item($detail, new LevelDetailTransformer)
        : $this->null();
    }
    /**
     * Include the favourite location of the user.
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeLoyaltyPoint(Driver $user)
    {
        $reward_point = $user->loyaltyPoint;

        return $reward_point
        ? $this->item($reward_point, new RewardPointsTransformer)
        : $this->null();
    }
}
