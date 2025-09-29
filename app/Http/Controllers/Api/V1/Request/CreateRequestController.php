<?php

namespace App\Http\Controllers\Api\V1\Request;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Jobs\NotifyViaMqtt;
use App\Models\Admin\Driver;
use App\Jobs\NotifyViaSocket;
use App\Models\Admin\ZoneType;
use App\Models\Request\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Request\RequestMeta;
use Illuminate\Support\Facades\Log;
use App\Transformers\User\EtaTransformer;
use App\Base\Constants\Masters\PushEnums;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Request\CreateTripRequest;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Jobs\Notifications\FcmPushNotification;
use App\Base\Constants\Setting\Settings;
use Sk\Geohash\Geohash;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
use Illuminate\Http\Request as ValidatorRequest;
use App\Helpers\Rides\FetchDriversFromFirebaseHelpers;
use App\Models\Admin\Promo;
use App\Models\Admin\PromoUser;
use App\Helpers\Rides\StoreEtaDetailForRideHelper;
use App\Jobs\ValidateAndGeneratePeakZone;
use App\Mail\RideLaterMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\V1\Payment\Stripe\StripeController;
/**
 * @group User-trips-apis
 *
 * APIs for User-trips apis
 */
class CreateRequestController extends StripeController
{
    use FetchDriversFromFirebaseHelpers, StoreEtaDetailForRideHelper;

    protected $request;

    protected $database;


    public function __construct(Request $request,Database $database)
    {
        $this->request = $request;
        $this->database = $database;
    }
    /**
    * Create Request
    * @bodyParam pick_lat double required pikup lat of the user
    * @bodyParam pick_lng double required pikup lng of the user
    * @bodyParam drop_lat double required drop lat of the user
    * @bodyParam drop_lng double required drop lng of the user
    * @bodyParam drivers json required drivers json can be fetch from firebase db
    * @bodyParam vehicle_type string required id of zone_type_id
    * @bodyParam payment_opt tinyInteger required type of ride whther cash or card, wallet('0 => card,1 => cash,2 => wallet)
    * @bodyParam pick_address string required pickup address of the trip request
    * @bodyParam drop_address string required drop address of the trip request
    * @bodyParam is_later tinyInteger sometimes it represent the schedule rides param must be 1.
    * @bodyParam trip_start_time timestamp sometimes it represent the schedule rides param must be datetime format:Y-m-d H:i:s.
    * @bodyParam promocode_id uuid optional id of promo table
    * @bodyParam rental_pack_id integer optional id of package type
    * @responseFile responses/requests/create-request.json
    *
    */
    public function createRequest(CreateTripRequest $request)
    {
       
        /**
        * Check if the user has registred a trip already
        * Validate payment option is available.
        * if card payment choosen, then we need to check if the user has added thier card.
        * if the paymenr opt is wallet, need to check the if the wallet has enough money to make the trip request
        * Check if thge user created a trip and waiting for a driver to accept. if it is we need to cancel the exists trip and create new one
        * Find the zone using the pickup coordinates & get the nearest drivers
        * create request along with place details
        * assing driver to the trip depends the assignment method
        * send emails and sms & push notifications to the user& drivers as well.
        */
        // Check whether the trip is schedule ride or not
        // Log::info("create Request");
        // Log::info($request->all());
        // Log::info("create Request");

        if ($request->has('is_later') && $request->is_later) {
            return $this->createRideLater($request);
        }

        // Validate payment option is available.
        // @TODO
        //Check if thge user created a trip and waiting for a driver to accept. if it is we need to cancel the exists trip and create new one
        $request_meta_with_current_user = RequestMeta::where('user_id', auth()->user()->id);
        $check_request_data_with_user = $request_meta_with_current_user->exists();
        if ($check_request_data_with_user) {
            // get request detail
            $request_with_user = $request_meta_with_current_user->pluck('request_id')->first();
            if ($request_with_user) {
                $this->request->where('id', $request_with_user)->update(['is_cancelled'=>1,'cancel_method'=>1,'cancelled_at'=>date('Y-m-d H:i:s')]);
            }
            // Delete all meta details
            $request_meta_with_current_user->delete();
        }
        // get type id
        $zone_type_detail = ZoneType::where('id', $request->vehicle_type)->first();
        $type_id = $zone_type_detail->type_id;

        // Get currency code of Request
        $service_location = $zone_type_detail->zone->serviceLocation;
        $currency_code = $service_location->currency_code;
        $currency_symbol = $service_location->currency_symbol;

        // $currency_code = get_settings('currency_code');
        //Find the zone using the pickup coordinates & get the nearest drivers


        // fetch unit from zone
        $unit = $zone_type_detail->zone->unit;
        // Fetch user detail
        $user_detail = auth()->user();

        if($user_detail->ride_otp==null)
        {
            $user_detail->ride_otp=rand(1111, 9999);

        }

        $user_detail->timezone = $service_location->timezone;
        $user_detail->save();

        // Get last request's request_number
        // $request_number = $this->request->orderBy('created_at', 'DESC')->pluck('request_number')->first();
        // if ($request_number) {
        //     $request_number = explode('_', $request_number);
        //     $request_number = $request_number[1]?:000000;
        // } else {
        //     $request_number = 000000;
        // }
        // // Generate request number
        // $request_number = 'REQ_'.sprintf("%06d", $request_number+1);

        $current_timestamp = Carbon::now()->timestamp.rand(0, 99);

        $request_number = 'REQ_'.$current_timestamp;

        $eta_result = fractal($zone_type_detail, new EtaTransformer);

            
        $eta_result =json_decode($eta_result->toJson());

        $request_params = [
            'request_number'=>$request_number,
            'user_id'=>$user_detail->id,
            'zone_type_id'=>$request->vehicle_type,
            'payment_opt'=>$request->payment_opt,
            'unit'=>(string)$unit,
            'promo_id'=>$request->promocode_id,
            'requested_currency_code'=>$currency_code,
            'requested_currency_symbol'=>$currency_symbol,
            'service_location_id'=>$service_location->id,
            'timezone'=>$service_location->timezone,
            'ride_otp'=>$user_detail->ride_otp,
            'poly_line'=>$request->poly_line,
            'total_time'=>$eta_result->data->time,
            'total_distance'=>$eta_result->data->distance,
        ];

        if($request->has('card_token')){

            $request_params['card_token'] = $request->card_token;

        }
        if($request->has('is_pet_available')){

            $request_params['is_pet_available'] = $request->is_pet_available =='true' ? 1 : 0;
        }

        if($request->has('is_airport')){

            $request_params['is_airport'] = 1;
            $request_params['is_surge_applied'] = 1;

        }
        if($request->has('is_luggage_available')){

            $request_params['is_luggage_available'] = $request->is_luggage_available =='true' ? 1 : 0;
        }

        $request_params['offerred_ride_fare']=0;

        $app_for = config('app.app_for');


        if($app_for!='taxi' || $app_for!='delivery')
         {
            $request_params['transport_type']='taxi';
            
         }


        if($request->has('is_bid_ride') && $request->input('is_bid_ride')==1){

            $request_params['is_bid_ride']=1;
            $request_params['offerred_ride_fare']=$request->offerred_ride_fare;
        }


        if($request->has('rental_pack_id') && $request->rental_pack_id){

            $request_params['is_rental'] = true;

            $request_params['rental_package_id'] = $request->rental_pack_id;
        }

        if($request->has('myself') && $request->input('myself')==0){

            $request_params['book_for_other'] = 1;

            if(!$request->has('contact_no_other') || $request->input('contact_no_other')==null){

                $this->throwCustomException('please provide the valid contact');

            }
            $request_params['book_for_other_contact'] = $request->input('contact_no_other');

        }

        if(!$request->drop_lat){
            $request_params['is_without_destination'] = true;
        }
        $request_params['company_key'] = auth()->user()->company_key;

        if($request->has('discounted_total') && $request->discounted_total){

            $request_params['discounted_total'] = $request->discounted_total;

            $request_params['rental_package_id'] = $request->rental_pack_id;
        }
       
        if($request->has('request_eta_amount') && $request->request_eta_amount){

            $request_params['request_eta_amount'] = $request->request_eta_amount;

        }

        // Log::info($request_params);
        
        // store request details to db
        // DB::beginTransaction();
        // try {
        // $request_detail = $this->request->create($request_params);

        if($request->has('card_token') && !$request->is_bid_ride && get_payment_settings('enable_stripe_authorization') == '1'){
            $preAuthorize = $this->authorizeAmount($request->request_eta_amount,$currency_code,$request->card_token);
            if($preAuthorize){
                $request_params['payment_intent_id']=$preAuthorize->id;
                $request_detail = $this->request->create($request_params);
            }else{
                $this->throwCustomException('Insufficient Balance Amount');
            }
        }else{
            $request_detail = $this->request->create($request_params);
        }

               
        $this->storeEta($request_detail , $eta_result);

        if($request->promocode_id) {
            $promo = Promo::find($request->promocode_id);
            $promo->update([
                'total_uses' => $promo->total_uses+1,
            ]);
            $promo_params = [
                'promo_code_id' => $request->promocode_id,
                'request_id' => $request_detail->id,
                'user_id' => $user_detail->id,
            ];
            PromoUser::create($promo_params);
        }
          // To Store Request stops along with poc details
        if ($request->has('stops')) {

            // Log::info($request->stops);

            foreach (json_decode($request->stops) as $key => $stop) {
                $request_detail->requestStops()->create([
                'address'=>$stop->address,
                'latitude'=>$stop->latitude,
                'longitude'=>$stop->longitude,
                'poc_instruction'=>$stop->poc_instruction,
                'order'=>$stop->order]);

            }
        }
        
        // request place detail params
        $request_place_params = [
            'pick_lat'=>$request->pick_lat,
            'pick_lng'=>$request->pick_lng,
            'drop_lat'=>$request->drop_lat,
            'drop_lng'=>$request->drop_lng,
            'pickup_poc_instruction'=>$request->pickup_poc_instruction,
            'drop_poc_instruction'=>$request->drop_poc_instruction,
            'pick_address'=>$request->pick_address,
            'drop_address'=>$request->drop_address];
        // store request place details
        $request_detail->requestPlace()->create($request_place_params);

        // Add Request detail to firebase database
         $this->database->getReference('requests/'.$request_detail->id)->update(['request_id'=>$request_detail->id,'request_number'=>$request_detail->request_number,'service_location_id'=>$service_location->id,'user_id'=>$request_detail->user_id,'pick_address'=>$request->pick_address,'drop_address'=>$request->drop_address,'active'=>1,'date'=>$request_detail->converted_created_at,'updated_at'=> Database::SERVER_TIMESTAMP]);


        $request_detail = $this->request->where('id',$request_detail->id)->first();

        // Log::info($request_detail);

        $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('userDetail');


        if ($request->has('is_bid_ride') && $request->input('is_bid_ride')==1) {
                goto no_drivers_available;
        }

        $nearest_drivers =  $this->fetchDriversFromFirebase($request_detail,$this->database);

        // Send Request to the nearest Drivers
         if ($nearest_drivers==null) {
                goto no_drivers_available;
            }

        no_drivers_available:

        // @TODO send sms & email to the user
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error($e);
        //     Log::error('Error while Create new request. Input params : ' . json_encode($request->all()));
        //     return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        // }
        // DB::commit();
        $service_location = $zone_type_detail->zone->serviceLocation;
        $timezone = $service_location->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        // if(get_settings('enable_peak_zone_feature') == 1){
        //     dispatch(new ValidateAndGeneratePeakZone($request->pick_lat,$request->pick_lng,$zone_type_detail->zone_id,$timezone));
        // }



        return $this->respondSuccess($request_result, 'created_request_successfully');
    }

    /**
    * Create Ride later trip
    * @responseFile responses/requests/create-request.json
    */
    public function createRideLater(CreateTripRequest $request)
    {

        // Log::info("create Later _____ Request");
        /**
        * @TODO validate if the user has any trip with same time period
        *
        */
        // get type id
        $zone_type_detail = ZoneType::where('id', $request->vehicle_type)->first();
        $type_id = $zone_type_detail->type_id;

        $eta_result = fractal($zone_type_detail, new EtaTransformer);

        $eta_result =json_decode($eta_result->toJson());



        // Get currency code of Request
        $service_location = $zone_type_detail->zone->serviceLocation;
        $currency_code = $service_location->currency_code;
        $currency_symbol = $service_location->currency_symbol;
        // $currency_code = get_settings('currency_code');

        // fetch unit from zone
        $unit = $zone_type_detail->zone->unit;
        // Fetch user detail
        $user_detail = auth()->user();
        // Get last request's request_number
        // $request_number = $this->request->orderBy('created_at', 'DESC')->pluck('request_number')->first();
        // if ($request_number) {
        //     $request_number = explode('_', $request_number);
        //     $request_number = $request_number[1]?:000000;
        // } else {
        //     $request_number = 000000;
        // }
        // // Generate request number
        // $request_number = 'REQ_'.sprintf("%06d", $request_number+1);

        $current_timestamp = Carbon::now()->timestamp.rand(0, 99);

        $request_number = 'REQ_'.$current_timestamp;
        
        // Convert trip start time as utc format
        $timezone = $service_location->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        // Update timezone for user
        $user_detail->timezone = $service_location->timezone;

        if($user_detail->ride_otp==null)
        {
            $user_detail->ride_otp=rand(1111, 9999);

        }


        $user_detail->save();

        $trip_start_time = Carbon::parse($request->trip_start_time, $timezone)->setTimezone('UTC')->toDateTimeString();

        $request_params = [
            'request_number'=>$request_number,
            'user_id'=>$user_detail->id,
            'is_later'=>true,
            'trip_start_time'=>$trip_start_time,
            'zone_type_id'=>$request->vehicle_type,
            'payment_opt'=>$request->payment_opt,
            'unit'=>(string)$unit,
            'requested_currency_code'=>$currency_code,
            'requested_currency_symbol'=>$currency_symbol,
            'service_location_id'=>$service_location->id,
            'timezone'=>$service_location->timezone,
            'ride_otp'=>$user_detail->ride_otp,
            'poly_line'=>$request->poly_line,
            // 'transport_type'=>'taxi',
            'on_search'=>false,
            'total_time'=>$request->duration,
            'total_distance'=>$eta_result->data->distance,
            
        ];

        if($request->has('card_token')){

            $request_params['card_token'] = $request->card_token;

        }
        
        // Calculate ETA
        $request_eta_params=[
            'base_price'=>$eta_result->data->base_price,
            'base_distance'=>$eta_result->data->base_distance,
            'total_distance'=>$eta_result->data->distance,
            'total_time'=>$eta_result->data->time,
            'price_per_distance'=>$eta_result->data->price_per_distance,
            'distance_price'=>$eta_result->data->distance_price,
            'price_per_time'=>$eta_result->data->price_per_time,
            'time_price'=>$eta_result->data->time_price,
            'service_tax'=>$eta_result->data->tax_amount,
            'service_tax_percentage'=>$eta_result->data->tax,
            'promo_discount'=>$eta_result->data->discount_amount,
            'admin_commision'=>$eta_result->data->without_discount_admin_commision,
            'admin_commision_with_tax'=>($eta_result->data->without_discount_admin_commision + $eta_result->data->tax_amount),
            'total_amount'=>$eta_result->data->total,
            'requested_currency_code'=>$currency_code
        ];
        if($request->has('is_pet_available')){

            $request_params['is_pet_available'] = $request->is_pet_available =='true' ? 1 : 0;
        }


        if($request->has('is_luggage_available')){

            $request_params['is_luggage_available'] = $request->is_luggage_available =='true' ? 1 : 0;
        }


        $app_for = config('app.app_for');

        if($app_for!='taxi' || $app_for!='delivery')
         {
            $request_params['transport_type']='taxi';
            
         }
        if($request->has('discounted_total') && $request->discounted_total){

            $request_params['discounted_total'] = $request->discounted_total;

            $request_params['rental_package_id'] = $request->rental_pack_id;
        }

        if($request->has('is_out_station')){
        $request_params['is_bid_ride']=1;
        $request_params['is_out_station'] = $request->is_out_station;
        $request_params['offerred_ride_fare']= $request->offerred_ride_fare;

        if($request->has('is_round_trip'))
        {

        $return_time = Carbon::parse($request->return_time, $timezone)->setTimezone('UTC')->toDateTimeString();

        $request_params['return_time'] = $return_time;
        $request_params['is_round_trip'] = true;


        }


        }

        $request_params['company_key'] = auth()->user()->company_key;

        if(!$request->drop_lat){
            $request_params['is_without_destination'] = true;
        }

        if($request->has('request_eta_amount') && $request->request_eta_amount){

           $request_params['request_eta_amount'] = $request->request_eta_amount;

        }



        if($request->has('rental_pack_id') && $request->rental_pack_id){

            $request_params['is_rental'] = true;

            $request_params['rental_package_id'] = $request->rental_pack_id;
        }
        // Log::info($request_params);

        // store request details to db
        // DB::beginTransaction();
        // try {
            // $request_detail = $this->request->create($request_params);
         // Log::info("--------request_stops---------");
         // Log::info($request_params);
         // Log::info("--------request_stops---------");
              // To Store Request stops along with poc details


        if($request->has('card_token') && !$request->is_bid_ride && get_payment_settings('enable_stripe_authorization') == '1'){
            $preAuthorize = $this->authorizeAmount($request->request_eta_amount,$currency_code,$request->card_token);
            if($preAuthorize){
                $request_params['payment_intent_id']=$preAuthorize->id;
                $request_detail = $this->request->create($request_params);
            }else{
                $this->throwCustomException('Insufficient Balance Amount');
            }
        }else{
            $request_detail = $this->request->create($request_params);
        }

        if ($request->has('stops')) {

            // Log::info($request->stops);

            foreach (json_decode($request->stops) as $key => $stop) {
                $request_detail->requestStops()->create([
                'address'=>$stop->address,
                'latitude'=>$stop->latitude,
                'longitude'=>$stop->longitude,
                'order'=>$stop->order]);

            }
        }
            // request place detail params
            $request_place_params = [
            'pick_lat'=>$request->pick_lat,
            'pick_lng'=>$request->pick_lng,
            'drop_lat'=>$request->drop_lat,
            'drop_lng'=>$request->drop_lng,
            'pick_address'=>$request->pick_address,
            'drop_address'=>$request->drop_address];
            // store request place details
            $request_detail->requestPlace()->create($request_place_params);

            $request_detail->requestEtaDetail()->create($request_eta_params);


         $this->database->getReference('requests/'.$request_detail->id)->update(['request_id'=>$request_detail->id,'request_number'=>$request_detail->request_number,'service_location_id'=>$service_location->id,'user_id'=>$request_detail->user_id,'pick_address'=>$request->pick_address,'drop_address'=>$request->drop_address,'active'=>1,'date'=>$request_detail->converted_trip_start_time,'updated_at'=> Database::SERVER_TIMESTAMP]);

            $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('userDetail');

        if(get_settings('enable_peak_zone_feature') == 1){
            dispatch(new ValidateAndGeneratePeakZone($request->pick_lat,$request->pick_lng,$zone_type_detail->zone_id,$timezone));
        }
        return $this->respondSuccess($request_result,'created_request_successfully');
    }



    /**
     * Respond For Bid ride
     *
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     *
     * */
    public function respondForBid(ValidatorRequest $request){
// Log::info("request_params");

// Log::info($request->all());
        // Get Request Detail
        $request_detail = $this->request->where('id', $request->input('request_id'))->where('user_id',auth()->user()->id)->first();
        // Validate the request i,e the request is already accepted by some one and it is a valid request for accept or reject state.

        if($request_detail==null){
            $this->throwCustomException('unauthorized request');
        }

        $this->validateRequestDetail($request_detail);

        $driver = Driver::where('id',$request->driver_id)->first();

        $accepted_fare = $request->accepted_ride_fare;
        $offered_fare = $request->offerred_ride_fare;

        $updated_params = [
            'driver_id'=>$driver->id,
            'accepted_at'=>date('Y-m-d H:i:s'),
            // 'is_driver_started'=>true,
            'accepted_ride_fare'=>$accepted_fare,
            'offerred_ride_fare'=>$offered_fare,
        ];

        if($request_detail->is_out_station==0)
        {
            $updated_params['is_driver_started'] = true;
        }



        if($driver->owner_id){

                $updated_params['owner_id'] = $driver->owner_id;

                $updated_params['fleet_id'] = $driver->fleet_id;
            }

        $request_detail->update($updated_params);
        $request_detail->fresh();
// Log::info("updted Reques");

// Log::info($request_detail);


        $driver->available = false;
            $driver->save();

        $notifable_driver = $driver->user;

        // $title = custom_trans('ride_confirmed_by_user_title', [], $notifable_driver->lang);
        // $body = custom_trans('ride_confirmed_by_user_body', [], $notifable_driver->lang);
        

        // dispatch(new SendPushNotification($notifable_driver,$title,$body));

        $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Ride Confirmation') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $notifable_driver->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
                    dispatch(new SendPushNotification($notifable_driver, $title, $body));
                }

        return $this->respondSuccess();


    }



    /**
     * Validate the request detail
     * @return \Illuminate\Http\JsonResponse
    */
    public function validateRequestDetail($request_detail)
    {

        if ($request_detail->is_completed) {
            $this->throwCustomException('request completed already');
        }
        if ($request_detail->is_cancelled) {
            $this->throwCustomException('request cancelled');
        }
    }




}
