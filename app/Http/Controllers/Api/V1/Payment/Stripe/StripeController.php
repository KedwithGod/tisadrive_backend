<?php

namespace App\Http\Controllers\Api\V1\Payment\Stripe;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Base\Constants\Setting\Settings;
use Kreait\Firebase\Contract\Database;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Webhook;
use Stripe\Event;
use Illuminate\Support\Facades\Log;
use App\Helpers\Payment\PaymentReferenceHelper;
use App\Models\User;
use App\Http\Controllers\Api\V1\BaseController; 



/**
 * @group Stripe Payment Gateway
 *
 * Payment-Related Apis
 */
class StripeController extends BaseController
{
    use PaymentReferenceHelper;


    public function __construct(Database $database)
    {
        $this->database = $database;
    }


     /**
     * Setup a StripeIntent
     * @response{"success":true,"message":"stripe_key_listed_success","data":{"client_secret":"seti_1QNX13SBCHfacuRqIglT6i8V_secret_RG37JHWQgOWjGNcXs4WPNTKiD57J0Nb","customer_id":null,"test_environment":true}}
     *
     */
    public function createStripeIntent(Request $request){

        if(get_payment_settings(Settings::STRIPE_ENVIRONMENT)=='test'){

            $secret_key = get_payment_settings(Settings::STRIPE_TEST_SECRET_KEY);

            $test_environment = true;


        }else{

            $secret_key = get_payment_settings(Settings::STRIPE_LIVE_SECRET_KEY);


            $test_environment = false;


        }

        \Stripe\Stripe::setApiKey($secret_key);
        $user = auth()->user();

        if (!$user->stripe_customer_id) {
        // Create a new Stripe customer
        $customer = Customer::create([
        'email' => $user->email,
        'name' => $user->name,
        ]);

        // Save the customer ID in the database
        $user->update(['stripe_customer_id' => $customer->id]);

        }

        $customerId = $user->stripe_customer_id;

        $setupIntent = \Stripe\SetupIntent::create([
        'customer' => $customerId,

        ]);

         return $this->respondSuccess([
                "client_secret" => $setupIntent->client_secret,
                "customer_id"=>$customerId,
                "test_environment"=>$test_environment,
            ],'stripe_key_listed_success');

        
    }

    /**
     * Save Payment Method Id
     * @bodyParam payment_method_id string required payment method id after saved the card from stripe
     * @bodyParam last_number integer required last 4 numbers of the card
     * @bodyParam card_type string required card type of the card
     * @bodyParam valid_through string required expiry date of the card
     * @response {
    "success": true,
    "message": "Card saved successfully."}
     * 
     * */
    public function saveCard(Request $request)
    {

        Log::info($request->all());

         $request->validate([
            'payment_method_id' => 'required|string',
            'last_number' => 'required',
            'card_type' => 'required|string',
            'valid_through' => 'required|string'
        ]);

        $user = auth()->user();


        if(get_payment_settings(Settings::STRIPE_ENVIRONMENT)=='test'){

            $secret_key = get_payment_settings(Settings::STRIPE_TEST_SECRET_KEY);

            $test_environment = true;


        }else{

            $secret_key = get_payment_settings(Settings::STRIPE_LIVE_SECRET_KEY);


            $test_environment = false;


        }

        \Stripe\Stripe::setApiKey($secret_key);

        PaymentMethod::retrieve($request->payment_method_id)->attach([
        'customer' => $user->stripe_customer_id,
        ]);

        $is_default = false;

        if(!$user->userCards()->exists()){

            $is_default = true;

        }


        // Store the cardInfo in the database
        $user->userCards()->create([
            'customer_id'=>$user->stripe_customer_id,
            'merchant_id'=>'from-stripe',
            'card_token'=>$request->payment_method_id,
            'valid_through'=>$request->valid_through,
            'last_number'=>$request->last_number,
            'card_type'=>$request->card_type,
            'is_default'=>$is_default
        ]);


        return $this->respondSuccess(null, 'Card saved successfully.');


    }

    /**
     * Add Money to Wallet By Stripe
     * 
     * 
     * */
    public function addMoneyToWalletByStripe(Request $request)
    {
        $user = auth()->user();

        $requested_amount = $request->amount;

        $requested_currency = $user->countryDetail->currency_code;

        $conditional_description = 'add-money-to-wallet';

        if($request->plan_id){

            $conditional_description = 'subscription';
        }

        $description = $this->generatePaymentReference($user->id,$conditional_description);


        $customer_id = $user->stripe_customer_id;

        // $customer_id = 'cus_RGoxxFoWRBCDk0';

        $payment_method = $request->card_token;



        return $this->makePaymentByStripe($user,$requested_amount,$requested_currency,$description,$customer_id,$payment_method,$conditional_description,$request->plan_id);

    }


    /**
     * Stripe Webhooks
     * 
     * 
     * */
    public function makePaymentByStripe($user,$requested_amount,$requested_currency,$description,$customer_id,$payment_method,$conditional_description,$plan_id=null){


        if(get_payment_settings(Settings::STRIPE_ENVIRONMENT)=='test'){

            $secret_key = get_payment_settings(Settings::STRIPE_TEST_SECRET_KEY);

        }else{

            $secret_key = get_payment_settings(Settings::STRIPE_LIVE_SECRET_KEY);

        }


        $stripe = new \Stripe\StripeClient($secret_key);

        
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => ($requested_amount * 100),
            'currency' => $requested_currency,
            'customer' => $customer_id,
            'payment_method' => $payment_method,
            'off_session' => true,
            'confirm' => true,
            'description' => $description,
            'shipping' => [
        'name' => 'John Doe',
        'address' => [
            'line1' => '123 Main Street',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94111',
            'country' => 'US',
        ],
    ],
        ]);

            $transaction_id = $paymentIntent->id;


        if ($paymentIntent->status === 'succeeded') {
            
            if ($transaction_id) {
                
            if($conditional_description=='add-money-to-wallet'){

                return $this->addMoneyToWallet($user,$requested_amount,$transaction_id);    
            }elseif ($conditional_description=='subscription') {
                
               return $this->makePaymentForSubscription($user,$plan_id,$requested_amount);
                
            }

            else{

                //conditional_description should be a request_id
                return $this->makePaymentForRide($conditional_description,$transaction_id);

            }
            

                
            } else {

                return $this->respondFailed('Payment failed successfully.');

                
            }

        } else {

            if($conditional_description!='add-money-to-wallet'){

                return false;
            }
        // Handle failure
                return $this->respondFailed('Payment failed successfully.');

        }

    

    }

    /**listen Webhook
     * 
     * 
     * */
    public function listenWebHooks(Request $request)
    {
         $endpointSecret = 'whsec_ivRsbfbFlhAwVHTPPgYjAeG2phhpB0Hd';

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');


         try {

        $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );

        switch ($event->type) {
                case 'charge.succeeded':
                $paymentIntent = $event->data->object;
                return $this->handlePaymentSuccess($paymentIntent);
                break;

                case 'charge.expired':
                $paymentIntent = $event->data->object;

                return $this->respondFailed('Payment Expired successfully.');
                        
                break;

                case 'charge.failed':
                $paymentIntent = $event->data->object;
                return $this->respondFailed('Payment failed successfully.');
                        
                break;
                

        }

        return $this->respondSuccess(null, 'Payment Done successfully.');


    }catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }






    }


    /**
     * Handle Payment Success from webhook
     * 
     * */
    public function handlePaymentSuccess($paymentIntent){

        $reference = $paymentIntent->description;

        $exploded_reference = explode('--', $reference);

        $request_for = $exploded_reference[0];
        
        $user_id = $exploded_reference[2];

        $transaction_id = $paymentIntent->id;

        $requested_amount = ($paymentIntent->amount/100);

        $user = User::find($user_id);

        if($user==null){
            goto end;
        }

        if($request_for!='add-money-to-wallet'){

            return $this->makePaymentForRide($request_for,$transaction_id);
        
        }else{


           return $this->addMoneyToWallet($user,$requested_amount,$transaction_id);
        }

        end:

        return $this->respondFailed('Payment failed successfully.');


        
    }


    public function authorizeAmount($amount, $requested_currency, $payment_method) {

        $user = auth()->user();

        $customerId = $user->stripe_customer_id;



        $stripe = new \Stripe\StripeClient($this->getStripeSecretKey());

        $amount *= 100;

        $description = $this->generatePaymentReference($user->id,'for-ride-cost');

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => $requested_currency,
            'customer' => $customerId,
            'payment_method' => $payment_method,
            'capture_method' => 'manual',
            'automatic_payment_methods' => ['enabled' => true,'allow_redirects'=>'never'],
            'description' => $description,
            'shipping' => [
                'name' => 'John Doe',
                'address' => [
                    'line1' => '123 Main Street',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'postal_code' => '94111',
                    'country' => 'US',
                ],
            ],
        ]);

        if ($paymentIntent->status === 'requires_confirmation') {
            
            return $paymentIntent;

        } else {
            return false;

        }
    }

    public function capture($paymentIntentId)
    {
        $stripe = new \Stripe\StripeClient($this->getStripeSecretKey());

        $paymentIntent = $stripe->paymentIntents->capture($paymentIntentId);

        return $paymentIntent->status == "succeeded";
    }

    public function updateAmount($paymentIntentId,$finalAmount) {
        
        $stripe = new \Stripe\StripeClient($this->getStripeSecretKey());

        $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

        $originalAmount = $paymentIntent->amount;

        $finalAmountCents = $finalAmount * 100;

        // If final amount is less than original, update it
        if ($finalAmountCents < $originalAmount) {
            $stripe->paymentIntents->update($paymentIntentId, [
                'amount' => $finalAmountCents,
            ]);

            $stripe->paymentIntents->confirm($paymentIntent->id);

            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

            return $paymentIntent->status == "requires_capture";

        }else{
            return false;
        }

    }

    public function cancel($paymentIntentId)
    {
        $stripe = new \Stripe\StripeClient($this->getStripeSecretKey());

        $paymentIntent = $stripe->paymentIntents->cancel($paymentIntentId);

        return $paymentIntent->status == "canceled";

    }


   
    public function getStripeSecretKey() : string {
        if(get_payment_settings(Settings::STRIPE_ENVIRONMENT)=='test'){

            $secret_key = get_payment_settings(Settings::STRIPE_TEST_SECRET_KEY);

        }else{

            $secret_key = get_payment_settings(Settings::STRIPE_LIVE_SECRET_KEY);

        }

        return $secret_key;
    }

    
}
