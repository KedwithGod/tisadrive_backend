<?php

namespace App\Http\Controllers\Api\V1\Payment\Mercadopago;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\ApiController;
use App\Models\Payment\UserWalletHistory;
use App\Models\Payment\DriverWalletHistory;
use App\Transformers\Payment\WalletTransformer;
use App\Transformers\Payment\DriverWalletTransformer;
use App\Http\Requests\Payment\AddMoneyToWalletRequest;
use App\Transformers\Payment\UserWalletHistoryTransformer;
use App\Transformers\Payment\DriverWalletHistoryTransformer;
use App\Models\Payment\UserWallet;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Base\Constants\Setting\Settings;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Jobs\NotifyViaMqtt;
use App\Base\Constants\Masters\PushEnums;
use App\Models\Payment\OwnerWallet;
use App\Models\Payment\OwnerWalletHistory;
use App\Transformers\Payment\OwnerWalletTransformer;
use App\Models\Request\Request as RequestModel;
use Kreait\Firebase\Contract\Database;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Admin\Subscription;
use App\Models\Admin\SubscriptionDetail;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

/**
 * @group Paystack Payment Gateway
 *
 * Payment-Related Apis
 */
class MercadoPagoController extends ApiController
{

     public function __construct(Database $database)
    {
        $this->database = $database;
    }
    /**
     * Initialize Payment
     * 
     * 
     * 
     * */
    public function makePayment(Request $request)
    {
        $request->validate([
            'payment_for' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();
        $environment =  get_payment_settings('mercadopago_environment');

        if ($environment=="test") {
            $public_key =  get_payment_settings('mercadopago_test_public_key');
            $token =  get_payment_settings('mercadopago_test_access_token');
        }else{
            $public_key =  get_payment_settings('mercadopago_live_public_key');
            $token =  get_payment_settings('mercadopago_live_access_token');
        }

        if (empty($token)) {
            Log::error("Mercado Pago access token is not configured for environment: {$environment}");
            return $this->throwCustomException('Payment gateway not configured correctly.');
        }

        try {
            // Add credentials
            MercadoPagoConfig::setAccessToken($token);

            $current_timestamp = Carbon::now()->timestamp;
            $description = $current_timestamp.'----'.$user->id.'----'.$request->payment_for.'----'.$request->amount;

            $currency = $user->countryDetail->currency_code ?? "USD";

            if($request->request_id){
                $description .= '----'.$request->request_id;
            }

            $client = new PreferenceClient();
            $preference = $client->create([
                "items" => [
                    [
                        "title" => $request->payment_for,
                        "description" => $description,
                        "quantity" => 1,
                        "currency_id"=>$currency,
                        "unit_price" => (float) $request->amount
                    ]
                ],
                "external_reference"=>$description,
                "payer"=>[
                    'name'=>$user->name ?? "test",
                    'email'=>$user->email ?? "test@test.com",
                ],
                "back_urls" =>array(
                    "success" => route('mercadopago.success'),
                    "failure"=>env('APP_URL').'/failure',
                    "pending"=>env('APP_URL').'/pending'
                ),
                
            ]);

            return $this->respondSuccess($preference,'Mercado Pago preference created successfully');

        } catch (MPApiException $e) {
            $error = $e->getApiResponse()->getContent();
            return $this->throwCustomException('Mercado Pago API Error: ' . $error['message']);
        } catch (\Exception $e) {
            return $this->throwCustomException('An unexpected error occurred.: '.$e->getMessage());
        }
        return response()->json(['data'=>$preference]);
    }


    // /**
    //  * Handles Mercado Pago IPN (Instant Payment Notification) webhooks.
    //  *
    //  * @param \Illuminate\Http\Request $request
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function handleWebhook(Request $request)
    // {

    //     $environment = get_payment_settings('mercadopago_environment');
    //     if ($environment == "test") {
    //         $token = get_payment_settings('mercadopago_test_access_token');
    //     } else {
    //         $token = get_payment_settings('mercadopago_live_access_token');
    //     }

    //     if (empty($token)) {
    //         return response()->json(['status' => 'error', 'message' => 'Internal configuration error'], 200);
    //     }

    //     MercadoPagoConfig::setAccessToken($token);

    //     $type = $request->input('type');
    //     $dataId = $request->input('data.id');

    //     if (empty($type) || empty($dataId)) {
    //         Log::warning('Mercado Pago Webhook: Missing type or data.id in payload.', $request->all());
    //         return response()->json(['status' => 'error', 'message' => 'Invalid webhook payload'], 200);
    //     }

    //     switch ($type) {
    //         case 'payment':

    //             $paymentClient = new PaymentClient();
    //             $payment = $paymentClient->get($dataId);
    //             Log::info("Fetched Mercado Pago Payment details for ID {$dataId}:", $payment->toArray());
    //             return response()->json(['status' => 'success', 'message' => 'Webhook type processed'], 200);
    //         // case 'authorized_payment': // For cases where payment is authorized but not captured yet
    //         //     // Implement if you have pre-authorization flows
    //         //     return response()->json(['status' => 'success', 'message' => 'Authorized payment webhook processed'], 200);
    //         // case 'refund':
    //         //     // You'd typically handle refunds here, marking orders as refunded
    //         //     return response()->json(['status' => 'success', 'message' => 'Refund webhook processed'], 200);
    //         // case 'chargebacks':
    //         //     // Handle chargebacks here to update order status accordingly
    //         //     return response()->json(['status' => 'success', 'message' => 'Chargeback webhook processed'], 200);
    //         // // Add other types as needed
    //         default:
    //             Log::info("Mercado Pago Webhook: Unhandled type '{$type}' received for ID '{$dataId}'.");
    //             return response()->json(['status' => 'success', 'message' => 'Webhook type not handled'], 200);
    //     }
    // }
/**
 * Process payment
 * 
 * 
 * */
public function processPayment($user,$payment_for,$request_id,$amount,$plan_id){


    //Handle the sucess payment  Here
           if ($payment_for=="wallet") {

             $request_id = null;

            if ($user->hasRole('user')) {
                $wallet_model = new UserWallet();
                $wallet_add_history_model = new UserWalletHistory();
                $user_id = $user->id;
            } elseif($user->hasRole('driver')) {
                        $wallet_model = new DriverWallet();
                        $wallet_add_history_model = new DriverWalletHistory();
                        $user_id = $user->driver->id;
            }else {
                        $wallet_model = new OwnerWallet();
                        $wallet_add_history_model = new OwnerWalletHistory();
                        $user_id = $user->owner->id;
            }

            $user_wallet = $wallet_model::firstOrCreate([
                'user_id'=>$user_id]);
            $user_wallet->amount_added += $amount;
            $user_wallet->amount_balance += $amount;
            $user_wallet->save();
            $user_wallet->fresh();


            // $currentTime = Carbon::now();


            // $transaction_id = $currentTime->timestamp;

            $wallet_add_history_model::create([
                'user_id'=>$user_id,
                'amount'=>$amount,
                'transaction_id'=>str_random(6),
                'remarks'=>WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET,
                'is_credit'=>true]);


                    $title = custom_trans('amount_credited_to_your_wallet_title');
                    $body = custom_trans('amount_credited_to_your_wallet_body');

                    dispatch(new SendPushNotification($user,$title,$body));

                    if ($user->hasRole(Role::USER)) {
                    $result =  fractal($user_wallet, new WalletTransformer);
                    } elseif($user->hasRole(Role::DRIVER)) {
                        $result =  fractal($user_wallet, new DriverWalletTransformer);
                    }else{
                        $result =  fractal($user_wallet, new OwnerWalletTransformer);

                   }


            } elseif ($payment_for == 'subscription') {
                $plan_id = $plan_id;
                $plan = Subscription::find($plan_id);

                $user = User::find($user_id);
                $params['transaction_id'] = str_random(6);
                $driver_wallet = $user->driver->DriverWallet;
                $driver_wallet->amount_spent += $amount;
                $driver_wallet->save();

                $user->driver->driverWalletHistory()->create([
                    'amount'=>$amount,
                    'transaction_id'=>str_random(6),
                    'remarks'=>WalletRemarks::SUBSCRIPTION_FEE,
                    'is_credit'=>false,
                ]);

                $driver = $user->driver;

                $expire_at = Carbon::parse(now())->addDay($plan->subscription_duration)->toDateString();
                $params = [
                    'driver_id' => $driver->id,
                    'subscription_id' => $plan_id,
                    'amount' => $amount,
                    'payment_opt' => 0,
                    'expired_at' => $expire_at,
                ];
                $params['transaction_id'] = str_random(6);
                $params['subscription_type'] = 1;
                $subscription = SubscriptionDetail::create($params);
                $driver->update([
                    'is_subscribed' => true,
                    'subscription_detail_id' => $subscription->id,
                ]);

                $result =  fractal($driver_wallet, new DriverWalletTransformer);

                $title = custom_trans('subscription_title', [], $user->lang);
                $body = custom_trans('subscription_body', [], $user->lang);
                

                dispatch(new SendPushNotification($user,$title,$body));
            }else{

                $request_id = $request_id;

                $request_detail = RequestModel::where('id', $request_id)->first();

                
                $web_booking_value = $request_detail->web_booking ?? 0;

              
                $request_detail->update(['is_paid' => true]);

                if(!$request_detail->is_parcel)
                    {
                      $driver_commission = $request_detail->requestBill->driver_commision;


                        $driver_commission = $request_detail->requestBill->driver_commision;

                        $wallet_model = new DriverWallet();
                        $wallet_add_history_model = new DriverWalletHistory();
                        $user_id = $request_detail->driver_id;
                        /*wallet Modal*/
                        $user_wallet = $wallet_model::firstOrCreate([
                        'user_id'=>$user_id]);
                        $user_wallet->amount_added += $amount;
                        $user_wallet->amount_balance += $amount;
                        $user_wallet->save();
                        $user_wallet->fresh();
                        /*wallet history*/
                        $wallet_add_history_model::create([
                        'user_id'=>$user_id,
                        'amount'=>$amount,
                        'transaction_id'=>str_random(6),
                        'remarks'=>WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET,
                        'is_credit'=>true]);


                        $title = custom_trans('amount_credited_to_your_wallet_title');
                        $body = custom_trans('amount_credited_to_your_wallet_body');

                        dispatch(new SendPushNotification($request_detail->driverDetail->user,$title,$body));

                      $this->database->getReference('requests/'.$request_detail->id)->update(['is_paid'=>1]);

                    }               

            }

}
   



}
