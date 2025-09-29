<?php

namespace App\Http\Controllers\Web;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request as ValidatorRequest;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Base\Constants\Masters\PushEnums;
use App\Models\Payment\OwnerWallet;
use App\Models\Payment\OwnerWalletHistory;
use App\Transformers\Payment\OwnerWalletTransformer;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Payment\UserWalletHistory;
use App\Models\Payment\DriverWalletHistory;
use App\Transformers\Payment\WalletTransformer;
use App\Transformers\Payment\DriverWalletTransformer;
use App\Http\Requests\Payment\AddMoneyToWalletRequest;
use App\Transformers\Payment\UserWalletHistoryTransformer;
use App\Transformers\Payment\DriverWalletHistoryTransformer;
use App\Models\Payment\UserWallet;
use App\Models\Admin\SubscriptionDetail;
use App\Models\Admin\Subscription;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Base\Constants\Auth\Role;
use Carbon\Carbon;
use App\Models\Request\Request as RequestModel;
use App\Models\User;
use Log;
use Kreait\Firebase\Contract\Database;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Base\Constants\Setting\Settings;

class StripeController extends Controller
{

    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function stripe(ValidatorRequest $request)
    {
// dd(config('stripe.pk'));

        // Retrieve URL parameters
        $amount = ($request->input('amount') * 100 );
        $payment_for = $request->input('payment_for');
        $user_id = $request->input('user_id');
        $request_id = $request->input('request_id');
        $plan_id = $request->input('plan_id');

        $user = User::find($user_id);

        $currency = $user->countryDetail->currency_code ?? "INR";

        // Pass parameters to the view
        return view('stripe.stripe', compact('amount', 'payment_for', 'currency', 'user_id','request_id','plan_id'));
    }

    public function stripeCheckout(ValidatorRequest $request)
    {

       if(get_payment_settings('stripe_environment') == 'test'){
            $key = get_payment_settings('stripe_test_secret_key');
       }else{
            $key = get_payment_settings('stripe_live_secret_key');
       }
        \Stripe\Stripe::setApiKey($key);


        $amount1=($request->input('amount') );
        $productname = $request->get('productname');
        $payment_for = $request->get('payment_for');
        $currency = $request->get('currency');
        $amount = $amount1;
        $user_id = $request->get('user_id');
        $request_id = $request->get('request_id');
        $plan_id = $request->input('plan_id');

$total1=($amount1*100);



        //dd($amount,$total,$amount1);
        $session = \Stripe\Checkout\Session::create([
            'line_items'  => [
                [
                    'price_data' => [
                        'currency'     => $currency,
                        'product_data' => [
                            "name" => $currency,
                        ],
                        'unit_amount'  => $total1,
                    ],
                    'quantity'   => 1,
                ],

            ],
            'mode'        => 'payment',
            'success_url' => route('checkout.success') . '?productname=' . urlencode($productname) . '&payment_for=' . urlencode($payment_for) . '&currency=' . urlencode($currency) . '&amount=' . urlencode($amount) . '&user_id=' . urlencode($user_id). '&request_id=' . urlencode($request_id). '&plan_id=' . urlencode($plan_id),
               'cancel_url'  => route('checkout.failure'),
        ]);

        return redirect()->away($session->url);
    }

    public function stripeCheckoutSuccess(ValidatorRequest $request)
    {

        $web_booking_value=0;


        $payment_for = $request->get('payment_for');
        $currency = $request->get('currency');
        $amount = $request->get('amount');
        $user_id = $request->get('user_id');
        $request_id = $request->get('request_id');
        $plan_id = $request->get('plan_id');


         if ($payment_for=="wallet") {

             $request_id = null;

             $user = User::find($user_id);

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

            $wallet_add_history_model::create([
                'user_id'=>$user_id,
                'amount'=>$amount,
                'transaction_id'=>$request->PayerID,
                'remarks'=>WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET,
                'is_credit'=>true]);


                    // $title = custom_trans('amount_credited_to_your_wallet_title');
                    // $body = custom_trans('amount_credited_to_your_wallet_body');

                    // dispatch(new SendPushNotification($user,$title,$body));

                    $notification = \DB::table('notification_channels')
                            ->where('topics', 'User Wallet Amount') // Match the correct topic
                            ->first();

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

                    if ($user->hasRole(Role::USER)) {
                    $result =  fractal($user_wallet, new WalletTransformer);
                    } elseif($user->hasRole(Role::DRIVER)) {
                        $result =  fractal($user_wallet, new DriverWalletTransformer);
                    }else{
                        $result =  fractal($user_wallet, new OwnerWalletTransformer);

                   }

                } elseif ($payment_for == 'subscription') {
                    $plan = Subscription::find($request->input('plan_id'));
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

            }else{

                $request_id = $request->get('request_id');


                 $request_detail = RequestModel::where('id', $request_id)->first();

                 $web_booking_value = $request_detail->web_booking;

                $request_detail->update(['is_paid' => true]);
                if(!$request_detail->is_parcel)
                {

                    $driver_commision = $request_detail->requestBill->driver_commision;
                    if($request_detail->driverDetail->owner()->exists())
                    {
                        $wallet_model = new OwnerWallet();
                        $wallet_add_history_model = new OwnerWalletHistory();
                        $user_id = $request_detail->driverDetail->owner_id;
                    }else{
                        $wallet_model = new DriverWallet();
                        $wallet_add_history_model = new DriverWalletHistory();
                        $user_id = $request_detail->driver_id;
                    }
                    /*wallet Modal*/
                    $user_wallet = $wallet_model::firstOrCreate([
                    'user_id'=>$user_id]);
                    $user_wallet->amount_added += $driver_commision;
                    $user_wallet->amount_balance += $driver_commision;
                    $user_wallet->save();
                    $user_wallet->fresh();
                    /*wallet history*/
                    $wallet_add_history_model::create([
                    'user_id'=>$user_id,
                    'amount'=>$driver_commision,
                    'transaction_id'=>$request->PayerID,
                    'remarks'=>WalletRemarks::TRIP_COMMISSION_FOR_DRIVER,
                    'is_credit'=>true]);


                    // $title = custom_trans('amount_credited_to_your_wallet_title');
                    // $body = custom_trans('amount_credited_to_your_wallet_body');

                    // dispatch(new SendPushNotification($request_detail->driverDetail->user,$title,$body));

                    $notification = \DB::table('notification_channels')
                            ->where('topics', 'User Wallet Amount') // Match the correct topic
                            ->first();

                        //    send push notification 
                            if ($notification && $notification->push_notification == 1) {
                                 // Determine the user's language or default to 'en'
                                $userLang = $request_detail->driverDetail->user->lang ?? 'en';
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
                                dispatch(new SendPushNotification($request_detail->driverDetail->user, $title, $body));
                            }
                            // 
                    if ($request_detail->promo_id){
                        $discount = $request_detail->requestBill->promo_discount;
        
                        if($discount>0) {
                            if($request_detail->driverDetail->owner()->exists())
                            {
                                $owner_wallet = $request_detail->driverDetail->owner->ownerWalletDetail;
                                $owner_wallet->amount_added += $discount;
                                $owner_wallet->amount_balance += $discount;
                                $owner_wallet->save();
                    
                                $owner_wallet_history = $request_detail->driverDetail->owner->ownerWalletHistoryDetail()->create([
                                    'amount'=>$discount,
                                    'transaction_id'=>str_random(6),
                                    'remarks'=>WalletRemarks::DISCOUNTED_AMOUNT,
                                    'is_credit'=>true
                                ]);
                            }else{
                                $driver_wallet = $request_detail->driverDetail->driverWallet;
                                $driver_wallet->amount_added += $discount;
                                $driver_wallet->amount_balance += $discount;
                                $driver_wallet->save();
                    
                                $driver_wallet_history = $request_detail->driverDetail->driverWalletHistory()->create([
                                    'amount'=>$discount,
                                    'transaction_id'=>str_random(6),
                                    'remarks'=>WalletRemarks::DISCOUNTED_AMOUNT,
                                    'is_credit'=>true
                                ]);
                            }
                        }
                    }    
                 $this->database->getReference('requests/'.$request_detail->id)->update(['is_paid'=>1]);
                }

            }




        return view('success',['success'],compact('web_booking_value','request_id'));
    }

    public function stripeCheckoutError(ValidatorRequest $request)
    {
        return view('failure',['failure']);

    }
}
