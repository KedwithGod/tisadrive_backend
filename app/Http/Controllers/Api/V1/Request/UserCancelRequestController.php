<?php

namespace App\Http\Controllers\Api\V1\Request;

use App\Jobs\NotifyViaMqtt;
use App\Jobs\NotifyViaSocket;
use App\Models\Request\RequestMeta;
use App\Base\Constants\Masters\UserType;
use App\Base\Constants\Masters\PushEnums;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Request\CancelTripRequest;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Base\Constants\Masters\WalletRemarks;
use App\Base\Constants\Masters\zoneRideType;
use App\Base\Constants\Masters\PaymentType;
use App\Models\Admin\CancellationReason;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Admin\PromoUser;
use Illuminate\Support\Facades\Log;
use App\Helpers\Rides\PaymentOptionCalculationHelper;
use App\Models\Request\Request as RequestRequest;
use App\Transformers\Requests\RequestBillTransformer;
use App\Models\Admin\NotificationChannel;
use App\Helpers\Rides\CancellationFeeHelper;
use App\Http\Controllers\Api\V1\Payment\Stripe\StripeController;

/**
 * @group User-trips-apis
 *
 * APIs for User-trips apis
 */
class UserCancelRequestController extends StripeController
{

    use PaymentOptionCalculationHelper;
    use CancellationFeeHelper;
    protected $database;



    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
    * User Cancel Request
    * @bodyParam request_id uuid required id of request
    * @bodyParam reason string optional reason provided by user
    * @bodyParam custom_reason string optional custom reason provided by user
    * @response 
    * {
    *     "success": true,
    *     "message": "success"
    * }
    */
    public function cancelRequest(CancelTripRequest $request)
    {
        /**
        * Validate the request which is authorised by current authenticated user
        * Cancel the request by updating is_cancelled true with reason if there is any reason
        * Available the driver who belongs to the request
        * Notify the driver that the user is cancelled the trip request
        */
        // Validate the request which is authorised by current authenticated user
        $user = auth()->user();
        $request_detail = $user->requestDetail()->where('id', $request->request_id)->first();
        // Throw an exception if the user is not authorised for this request
        if (!$request_detail) {
            $this->throwAuthorizationException();
        }
        $cancel_method = UserType::USER;


        $request_detail->update([
            'is_cancelled'=>true,
            'reason'=>$request->reason,
            'custom_reason'=>$request->custom_reason,
            'cancel_method'=>$cancel_method,
            'cancelled_at'=>date('Y-m-d H:i:s')
        ]);

        
        if($request_detail->payment_intent_id){
            $this->cancel($request_detail->payment_intent_id);
        }

        // Log::info("user cancel");

        $request_detail->fresh();
        /**
        * Apply Cancellation Fee
        */
        // $charge_applicable = false;

        // if ($request->custom_reason) {
        //     $charge_applicable = true;
        // }
        // if ($request->reason) {
        //     $reason = CancellationReason::find($request->reason);
        //     if($reason){

        //     if ($reason->payment_type=='free') {
        //         $charge_applicable=false;
        //     } else {
        //         $charge_applicable=true;
        //     }

        //     }else{

        //         $charge_applicable = false;
        //     }
            
        // }

        // /**
        //  * get prices from zone type
        //  */

        //     $ride_type = zoneRideType::RIDENOW;


        // if ($charge_applicable) {
        //     $zone_type_price = $request_detail->zoneType->zoneTypePrice()->where('price_type', $ride_type)->first();

        //     $cancellation_fee = $zone_type_price->cancellation_fee;
        //     if ($request_detail->payment_opt==PaymentType::WALLET) {
        //         $requested_user = $request_detail->userDetail;
        //         $user_wallet = $requested_user->userWallet;
        //         $user_wallet->amount_spent += $cancellation_fee;
        //         $user_wallet->amount_balance -= $cancellation_fee;
        //         $user_wallet->save();
        //         // Add the history
        //         $requested_user->userWalletHistory()->create([
        //             'amount'=>$cancellation_fee,
        //             'transaction_id'=>$request_detail->id,
        //             'remarks'=>WalletRemarks::CANCELLATION_FEE,
        //             'request_id'=>$request_detail->id,
        //             'is_credit'=>false]);
        //         $request_detail->requestCancellationFee()->create(['user_id'=>$request_detail->user_id,'is_paid'=>true,'cancellation_fee'=>$cancellation_fee,'paid_request_id'=>$request_detail->id]);
        //     } else {
        //         $request_detail->requestCancellationFee()->create(['user_id'=>$request_detail->user_id,'is_paid'=>false,'cancellation_fee'=>$cancellation_fee]);
        //     }
        // }

                // ✅ Call the helper to handle cancellation fee
                $this->handleCancellationFee($request_detail, $request);

                // // Free up the driver if exists
                $driver = $request_detail->driverDetail ?: optional($request_detail->requestMeta()->where('active', true)->first())->driver;

        // Available the driver who belongs to the request
        $request_driver = $request_detail->driverDetail;
        if ($request_driver) {
            $driver = $request_driver;
        } else {
            $request_meta_driver = $request_detail->requestMeta()->where('active', true)->first();
            if($request_meta_driver){
            $driver = $request_meta_driver->driver;

            }else{
                $driver=null;
            }
        }
        if($request_detail->promo_id){
            PromoUser::where('request_id',$request_detail->id)->delete();
        }

        // Delete from Firebase

    
        if ($driver) {

            $driver->available = true;
            $driver->save();
            $driver->fresh();
            // Notify the driver that the user is cancelled the trip request
            $notifiable_driver = $driver->user;
            $request_result =  fractal($request_detail, new TripRequestTransformer)->parseIncludes('userDetail');

            $push_request_detail = $request_result->toJson();
                // $title = custom_trans('trip_cancelled_by_user_title',[],$notifiable_driver->lang);
                // $body = custom_trans('trip_cancelled_by_user_body',[],$notifiable_driver->lang);              

            $push_data = ['success'=>true,'success_message'=>PushEnums::REQUEST_CANCELLED_BY_USER,'result'=>(string)$push_request_detail];


         $this->database->getReference('drivers/'.'driver_'.$driver->id)->update(['is_available'=>true,'updated_at'=> Database::SERVER_TIMESTAMP]);

           
            // dispatch(new SendPushNotification($notifiable_driver,$title,$body));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Trip Cancelled') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $notifiable_driver->lang ?? 'en';
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
                    dispatch(new SendPushNotification($notifiable_driver, $title, $body));
                }
        }
        // Delete meta records
        // RequestMeta::where('request_id', $request_detail->id)->delete();
        
        $request_detail->requestMeta()->delete();


        $this->database->getReference('requests/' . $request_detail->id)->update(['is_cancelled' => true, 'cancelled_by_user' => true]);
        $this->database->getReference('requests/' . $request_detail->id)->remove();
        $this->database->getReference('SOS/' . $request_detail->id)->remove();
        $this->database->getReference('request-meta/' . $request_detail->id)->remove();


        $this->database->getReference('bid-meta/'.$request_detail->id)->remove();

         Artisan::call('assign_drivers:for_regular_rides');
         
        return $this->respondSuccess();
    }
    /**
     * Update payment Method
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     */
    public function paymentMethod(Request $request)
    {

       $user = auth()->user();
       
       $request_detail = $user->requestDetail()->where('id', $request->request_id)->first();

        // dd($user);
        // Throw an exception if the user is not authorised for this request
        if (!$request_detail) {
            $this->throwAuthorizationException();
        }
        $request_detail->update([
            'payment_opt'=>$request->payment_opt,
        ]);

        if($request_detail->payment_opt == 0){

         $request_detail->update([
            'is_paid'=>false, 
        ]);

        }

        return $this->respondSuccess();

    }
    /**
     * Update payment Confirmation
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     */
    public function userPaymentConfirm(Request $request)
    {

       $user = auth()->user();
        $request_detail = $user->requestDetail()->where('id', $request->request_id)->first();
        // Throw an exception if the user is not authorised for this request
        if (!$request_detail) {
            $this->throwAuthorizationException();
        }
        if($request_detail->is_paid){
            $this->throwCustomException('Already Paid For the ride');
        }

        if ($this->handlePayment($request_detail) ) {
            $request_detail->update([
                'is_paid'=>true, 
            ]);
        }
        return $this->respondSuccess();

    }


    /**
     * Driver tips
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     */
    public function driverTip(Request $request) {
        $request->validate([
            'request_id' =>  'required',
            'tip_amount' =>  'required',
        ]);

        $request_detail = RequestRequest::where('id', $request->input('request_id'))->where('is_completed', true)->where('user_id',auth()->user()->id)->first();

        if(!$request_detail) {

            $this->throwAuthorizationException();
        }

        if($request_detail->is_paid) {
            $result = fractal($request_detail->requestBill, new RequestBillTransformer);

            return $this->respondSuccess($result);
        }

        $tips = (double) $request->tip_amount;

        $requestBill = $request_detail->requestBill;

        $request_detail->requestBill()->update(
            [
                'tips'=>$tips,
                'driver_commision'=>$requestBill->driver_commision + $tips,
            ]);

            $requestBill->fresh();

        $this->database->getReference('requests/' . $request_detail->id)->update(['driver_tips' => $tips]);

        $result = fractal($requestBill->fresh(), new RequestBillTransformer);

        // push notification

        $notification = NotificationChannel::where('topics', 'Driver Tips') // Match the correct topic
            ->first();
            
            if ($notification && $notification->push_notification == 1) {
                 // Determine the user's language or default to 'en'
                $userLang = $request_detail->driverDetail->user->lang ?? 'en';

                $translation = $notification->notificationChannelTranslationWords()
                    ->where(function($query) use ($userLang) {
                        $query->where('locale', $userLang)->orWhere('locale', 'en');
                    })
                    ->first();
                
                $title =  $translation->push_title ?? $notification->push_title;
                $body = strip_tags($translation->push_body ?? $notification->push_body);
                dispatch(new SendPushNotification($request_detail->driverDetail->user, $title, $body));
            }

        return $this->respondSuccess($result);

    }

}
