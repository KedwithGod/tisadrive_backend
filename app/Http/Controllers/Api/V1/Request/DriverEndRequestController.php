<?php

namespace App\Http\Controllers\Api\V1\Request;

use App\Jobs\NotifyViaMqtt;
use App\Models\Admin\Promo;
use App\Jobs\NotifyViaSocket;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
use App\Models\Admin\PromoUser;
use App\Base\Constants\Masters\UnitType;
use App\Base\Constants\Masters\PushEnums;
use App\Base\Constants\Masters\PaymentType;
use App\Base\Constants\Masters\WalletRemarks;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Request\DriverEndRequest;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Models\Admin\ZoneTypePackagePrice;
use Illuminate\Support\Facades\Log;
use App\Models\Request\RequestCancellationFee;
use App\Base\Constants\Setting\Settings;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Master\MailTemplate;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Mails\SendMailNotification;
use App\Jobs\Mails\SendInvoiceMailNotification;
use Illuminate\Http\Request;
use App\Models\Request\Request as RequestRequest;
use App\Models\Request\RequestBill;
use App\Models\Request\RequestStop; 
use App\Models\User;
use App\Helpers\Rides\RidePriceCalculationHelpers;
use App\Helpers\Rides\PaymentOptionCalculationHelper;
use App\Models\Admin\Incentive;
use App\Models\Payment\DriverIncentiveHistory;
use App\Models\Payment\DriverWallet;
use App\Models\Admin\Driver;
use App\Models\Admin\DriverLevelUp;
use App\Helpers\Rides\EndRequestHelper;
use App\Mail\UserInvoiceMail;
use App\Models\Admin\Setting;
use App\Models\Admin\InvoiceConfiguration;
use App\Models\ThirdPartySetting;
use App\Mail\DriverInvoiceMail;
use App\Jobs\Mails\SendUserInvoiceMailNotification;
use App\Jobs\Mails\SendDriverInvoiceMailNotification;
use App\Jobs\ValidateAndUpdateIncentivesJob;
use App\Jobs\ValidateAndUpdateDriverLoyaltyJob;
use App\Http\Controllers\Api\V1\Payment\Stripe\StripeController;
use App\Helpers\Payment\PaymentReferenceHelper;

/**
 * @group Driver-trips-apis
 *
 * APIs for Driver-trips apis
 */
class DriverEndRequestController extends StripeController
{
    use RidePriceCalculationHelpers,PaymentOptionCalculationHelper,EndRequestHelper,PaymentReferenceHelper;
   
    protected $database;
    
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    /**
    * Driver End Request
    * @bodyParam request_id uuid required id request
    * @bodyParam distance double required distance of request
    * @bodyParam drop_lat double required drop lattitude of request
    * @bodyParam drop_lng double required drop longitude of request
    * @bodyParam drop_address double required drop drop Address of request
    * @responseFile responses/requests/request_bill.json
    *
    */
    public function endRequest(DriverEndRequest $request)
    {   
        // Get Driver Detail
        $driver = auth()->user()->driver;

        // Get Request Detail
        $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();

        if (!$request_detail) {
            $this->throwAuthorizationException();
        }

        // Validate Trip request data
        if ($request_detail->is_completed) {

            $request_result = fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');
            return $this->respondSuccess($request_result, 'request_ended');
        }
        if ($request_detail->is_cancelled) {
            $this->throwCustomException('request cancelled');
        }


        // Collecting drop location detail & update to request_place table
        $firebase_request_detail = $this->database->getReference('requests/'.$request_detail->id)->getValue();

        $request_place_params = ['drop_lat'=>$request->drop_lat,'drop_lng'=>$request->drop_lng,'drop_address'=>$request->drop_address];

        if ($firebase_request_detail) {
            if(array_key_exists('lat_lng_array',$firebase_request_detail)){
                $locations = $firebase_request_detail['lat_lng_array'];
                $request_place_params['request_path'] = $locations;
            }
        }

        // Update Droped place details
        $request_detail->requestPlace->update($request_place_params);
        // Update Driver state as Available
        $request_detail->driverDetail->update(['available'=>true]);

         // Get currency code of Request
        $service_location = $request_detail->zoneType->zone->serviceLocation;

        $currency_code = $service_location->currency_code;

        $requested_currency_symbol = $service_location->currency_symbol;


        // Get the Price Details

        $zone_type = $request_detail->zoneType;

        $zone_type_price = $zone_type->zoneTypePrice()->first();

        // Calulate Distance & duration
        $distance = (double)$request->distance;

        Log::info('app-distance for '.$request_detail->request_number);
        Log::info($distance);

        $distance_and_duration = $this->calculateDistanceAndDuration($distance,$request_detail);

        $distance = $distance_and_duration['distance'];
        $duration = $distance_and_duration['duration'];


        $request_params = [
            'is_completed'=>true,
            'completed_at'=>date('Y-m-d H:i:s'),
            'total_distance'=>$distance,
            'total_time'=>$duration,
            ];

        if($request->poly_line !=null){

            $request_params['poly_line'] = $request->poly_line;

        }
        $request_detail->update($request_params);

        // Calulate Waiting Time
        $before_trip_start_waiting_time = $request->input('before_trip_start_waiting_time');
        $after_trip_start_waiting_time = $request->input('after_trip_start_waiting_time');

        $waiting_time = $before_trip_start_waiting_time + $after_trip_start_waiting_time;

        // Get/Validate Coupon Detail
        $promo_detail =null;

        if ($request_detail->promo_id) {
            $user_id = $request_detail->userDetail->id;
            $service_location_id = $request_detail->service_location_id;
            $promo_detail = $this->validateAndGetPromoDetail($request_detail->promo_id,$user_id,$request_detail);
        }


        // Collect Request pickup & drop coords
        $pick_lat = $request_detail->pick_lat;
        $drop_lat = $request_detail->drop_lat;
        $pick_lng = $request_detail->pick_lng;
        $drop_lng = $request_detail->drop_lng;

        $timezone = $request_detail->serviceLocationDetail->timezone;

        // $airport_surge_fee = 0;

        // if ( $request_detail->is_airport)
        // {
        //     $airport_surge_fee =  $zone_type->airport_surge;
        // }
        $request_place = $request_detail->requestPlace;

        $airport_surge = find_airport($request_place->pick_lat,$request_place->pick_lng);
        if($airport_surge==null)
        {
            $airport_surge = find_airport($request_place->drop_lat,$request_place->drop_lng);
        }

        $airport_surge_fee = 0;

        if($airport_surge){

            $airport_surge_fee =  $zone_type->airport_surge ?? 0;

        }
        // Calculate Bill of a Ride
        $calculated_bill = $this->calculateBillForARide($pick_lat,$pick_lng,$drop_lat,$drop_lng,$distance, $duration, $zone_type, $zone_type_price, $promo_detail,$timezone,null,$waiting_time,$request_detail,$driver,$airport_surge_fee);


         if($request_detail->is_rental && $request_detail->rental_package_id){

            $zone_type_price = ZoneTypePackagePrice::where('zone_type_id',$request_detail->zone_type_id)->where('package_type_id',$request_detail->rental_package_id)->first();

            $calculated_bill =  $this->calculateRentalRideFares($zone_type_price, $distance, $duration, $waiting_time, $promo_detail,$request_detail,$airport_surge_fee);

        }


        $calculated_bill['before_trip_start_waiting_time'] = $before_trip_start_waiting_time;
        $calculated_bill['after_trip_start_waiting_time'] = $after_trip_start_waiting_time;
        $calculated_bill['calculated_waiting_time'] = $waiting_time;
        $calculated_bill['waiting_charge_per_min'] = $zone_type_price->waiting_charge ?? 0;
        $calculated_bill['requested_currency_code'] = $currency_code;
        $calculated_bill['requested_currency_symbol'] = $requested_currency_symbol;

        if($request_detail->additional_charges_amount > 0) {
            $calculated_bill['additional_charges_reason'] = $request_detail->additional_charges_reason;
            $calculated_bill['additional_charges_amount'] = $request_detail->additional_charges_amount;
        }
        // Store Bill detail
        $bill = $request_detail->requestBill()->create($calculated_bill);


        if($request_detail->transport_type=='delivery' && $request_detail->payment_opt==1){

            $this->handlePayment($request_detail);
             
        }

        // Incentives & Driver Rewards/Level
        $incentive_feature = get_settings('show_incentive_feature_for_driver');
       
        if($incentive_feature==1)
        {
            if($driver->owner_id==null)
            {
                dispatch(new ValidateAndUpdateIncentivesJob($request_detail));
            }

        }

        $driver_level_feature = get_settings('show_driver_level_feature');
       
        if($driver_level_feature==1 && !$driver->owner_id)
        {
            dispatch(new ValidateAndUpdateDriverLoyaltyJob($request_detail));
        }


        if ($request_detail->payment_opt == PaymentType::WALLET) {

            if ($this->handlePayment($request_detail) ) {
                $request_detail->update([ 'is_paid'=>true, ]);
            }else{
                $request_detail->update([ 'payment_opt' => PaymentType::CASH, ]);
            }
        }   

        $user = $request_detail->userDetail;


        // Collect Payment from card if ride has card token
        if($request_detail->payment_opt==PaymentType::CARD && $request_detail->card_token){

            if($request_detail->payment_intent_id){
                $requested_amount = $request_detail->requestBill->total_amount;
                
                if($this->updateAmount($request_detail->payment_intent_id,$requested_amount)){

                    if($this->capture($request_detail->payment_intent_id)){

                        $request_detail->is_paid = true;
            
                        $request_detail->save();
            
                        $request_detail->fresh();
                    }

                }else{
                    
                    $this->cancel($request_detail->payment_intent_id);

                    $requested_amount = $request_detail->requestBill->total_amount;

                    $requested_currency = $request_detail->requested_currency_code;
            
                    $conditional_description = 'for-ride-cost';
            
                    $description = $this->generatePaymentReference($user->id,$conditional_description);
            
                    $customer_id = $user->stripe_customer_id;
            
                    $payment_method = $request_detail->card_token;
            
                    $conditional_description = $request_detail->id;
            
                    $stripe = $this->makePaymentByStripe($user,$requested_amount,$requested_currency,$description,$customer_id,$payment_method,$conditional_description);
            
                    if(!$stripe){
            
                        $request_detail->payment_opt = 1;
            
                        $request_detail->save();
            
                        $request_detail->fresh();
                    }
                }

            }else{

                $requested_amount = $request_detail->requestBill->total_amount;

                $requested_currency = $request_detail->requested_currency_code;
        
                $conditional_description = 'for-ride-cost';
        
                $description = $this->generatePaymentReference($user->id,$conditional_description);
        
                $customer_id = $user->stripe_customer_id;
        
                $payment_method = $request_detail->card_token;
        
                $conditional_description = $request_detail->id;
        
                $stripe = $this->makePaymentByStripe($user,$requested_amount,$requested_currency,$description,$customer_id,$payment_method,$conditional_description);
        
                if(!$stripe){
        
                    $request_detail->payment_opt = 1;
        
                    $request_detail->save();
        
                    $request_detail->fresh();
                }
        
            }

        }

        
        // Send push notification to the user
        $request_result = fractal($request_detail, new TripRequestTransformer)->parseIncludes(['requestBill','userDetail','driverDetail']);

        if ($request_detail->if_dispatch || $request_detail->user_id==null ) {
            goto end;
        }
        // Send Push notification to the user
        
        if($user){
            // $title = custom_trans('trip_completed_title',[],$user->lang);
            // $body = custom_trans('trip_completed_body',[],$user->lang);
            // dispatch(new SendPushNotification($user,$title,$body));
            $notification = \DB::table('notification_channels')
            ->where('topics', 'Invoice For End of the Ride User') // Match the correct topic
            ->first();

            if(!empty($user?->email)){

                $data = fractal($request_detail, new TripRequestTransformer)->parseIncludes(['userDetail', 'driverDetail', 'requestBill', 'rejectedDrivers'])->toArray(); 
                $logo = Setting::where('name', 'logo')->first();
                $invoice = ThirdPartySetting::where('module', 'mail_config')->pluck('value', 'name')->toArray();
                $data['formatted_completed_at'] = isset($request_detail['completed_at']) 
                ? Carbon::parse($request_detail['completed_at'])
                    ->setTimezone(env('SYSTEM_DEFAULT_TIMEZONE', 'Asia/Kolkata'))
                    ->format('M j, Y - h:i A') 
                : null;

                dispatch(new SendUserInvoiceMailNotification($user, $data, $logo, $invoice));

            } 


            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($user, $title, $body));
                }
        }

        
        $driver = $request_detail->driverDetail;


        if($driver && $driver->email){

            $data = fractal($request_detail, new TripRequestTransformer)->parseIncludes(['userDetail', 'driverDetail', 'requestBill', 'rejectedDrivers'])->toArray(); 
            $logo = Setting::where('name', 'logo')->first();
            $invoice = ThirdPartySetting::where('module', 'mail_config')->pluck('value', 'name')->toArray();
            $data['formatted_completed_at'] = isset($request_detail['completed_at']) 
            ? Carbon::parse($request_detail['completed_at'])
                ->setTimezone(env('SYSTEM_DEFAULT_TIMEZONE', 'Asia/Kolkata'))
                ->format('M j, Y - h:i A') 
            : null;

            dispatch(new SendDriverInvoiceMailNotification($driver, $data, $logo, $invoice));

        }

        end:
        
        return $this->respondSuccess($request_result, 'request_ended');
    }

    

    

    

    /**
    * Validate & Apply Promo code
    * @return \Illuminate\Http\JsonResponse
    *
    */
    public function validateAndGetPromoDetail($promo_code_id,$user_id,$request_detail)
    {
        $current_date = Carbon::today()->toDateTimeString();


            $transport_type = request()->transport_type;
            $expired = Promo::where('id', $promo_code_id)->where('service_location_id',$request_detail->service_location_id)->where(function($query)use($request_detail){
            $query->where('transport_type',$request_detail->transport_type)->orWhere('transport_type','both');
            })->where('to', '>', $current_date)->where('active',true)->first();

        if($expired)
        {

            if($expired->user_specific){
                $validate_promo_code = $expired->promoCodeUsers()->where('user_id',$request_detail->user_id)->first();
                if(!$validate_promo_code){
                    return null;
                }
            }
            $exceed_usage = PromoUser::where('promo_code_id', $expired->id)->where('user_id', $user_id)->count();

            if ($exceed_usage > $expired->uses_per_user) {
                return null;
            }
            else{
                return $expired;
            }
        }
        else{
            return null;
        }

    }

    /**
     * Payment Confirmation
     *
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     *
     * */
    public function paymentConfirm(Request $request)
    {

       $driver = auth()->user()->driver;


        $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();

        // Throw an exception if the user is not authorised for this request
        if (!$request_detail) {
            $this->throwAuthorizationException();
        }

         if($request_detail->transport_type=='delivery' && $request_detail->paid_at=="Sender" && $request_detail->payment_opt==1){

            $request_detail->update([
                'is_paid'=>true,
            ]);

        return $this->respondSuccess();
            
        }

        if ($this->handlePayment($request_detail) ) {
            $request_detail->update([
                'is_paid'=>true,
            ]);
        }

        return $this->respondSuccess();

    }
    /**
     * Payment Method update
     *
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     *
     * */
    public function paymentMethod(Request $request)
    {

       $driver = auth()->user()->driver;


        $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();

        // dd($user);
        // Throw an exception if the user is not authorised for this request
        if (!$request_detail) {
            $this->throwAuthorizationException();
        }
        $request_detail->update([
            'payment_opt'=>$request->payment_opt,
            'is_paid'=>true,

        ]);
        return $this->respondSuccess();
    }
    /**
     * Trip end for stop
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     */
    public function tripEndBystop(Request $request)
    {
        // Log::info("tripEndBystop");
        // Log::info($request->all());

        $request_stops = RequestStop::where('id', $request->stop_id)->update(['completed_at' => now()]);



        // Log::info($request_stops);

        return $this->respondSuccess();

    }

    public function stopOtpVerify(Request $request)
    {
        $request->validate([
        'stop_id' => 'required|exists:request_stops,id',
        'ride_otp'=>'required'
        ]);

        $request_stop = RequestStop::find( $request->stop_id);

        if(!$request_stop || !$request_stop->request()->exists()){
            $this->throwAuthorizationException();
        }

        if($request_stop->request->ride_otp != $request->ride_otp){

            $this->throwCustomException('provided otp is invalid');
        }


        if($request_stop->request->transport_type == 'delivery' && get_settings('show_delivery_ride_drop_otp_feature')=='1' && get_settings('show_delivery_ride_pick_otp_feature')=='1'){
            $request_stop->request()->update(['ride_otp' => rand(1111, 9999)]);
        }

        return $this->respondSuccess();        
    }

    

    /**
     * Additional Charges
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     */
    public function tripMeterRideUpdate(Request $request) {
        $request->validate([
            'request_id' =>  'required',
            'fare_amount' =>  'required',
        ]);

        $request_detail = RequestRequest::where('id', $request->input('request_id'))->first();

        $request_detail->update(['is_trip_meter'=>true,'accepted_ride_fare'=>$request->fare_amount]);
        return $this->respondSuccess();

    }

    /**
     * Additional Charges
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     */
    public function additionalChargeUpdate(Request $request) {
        $request->validate([
            'request_id' =>  'required',
            'additional_charges_reason' =>  'required|string',
            'additional_charges_amount' =>  'required',
        ]);

        $request_detail = RequestRequest::where('id', $request->input('request_id'))->first();

        $request_detail->update(['additional_charges_reason'=>$request->additional_charges_reason,'additional_charges_amount'=>$request->additional_charges_amount]);
        return $this->respondSuccess();

    }

}
