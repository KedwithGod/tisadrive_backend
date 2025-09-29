<?php

namespace App\Http\Controllers\Web;
use Illuminate\Http\Request as ValidatorRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
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
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Base\Constants\Auth\Role;
use Carbon\Carbon;
use App\Models\Request\Request as RequestModel;
use App\Models\User;
use Log;
use Kreait\Firebase\Contract\Database;

class RazorPayController extends Controller
{
    protected $api;
    protected $database;

    public function __construct(Database $database)
    {
        $environment =  get_payment_settings('razor_pay_environment');

        if ($environment=="test") {
            $keySecret =  get_payment_settings('razor_pay_test_secrect_key');
            $keyId =  get_payment_settings('razor_pay_test_api_key');
        }else{
            $keySecret =  get_payment_settings('razor_pay_secrect_key');
            $keyId =  get_payment_settings('razor_pay_live_api_key');
        }
// dd($keyId);
        $this->database = $database;

        $this->api = new Api($keyId, $keySecret);
    }

    public function razorpay(Request $request)
    {
        // Log::info("test api v1");
        // Log::info($request->all());

    //  $keyId = "rzp_test_b444CSYRGAtdnV";  // Replace with your test key ID
    //  $keySecret = "Q8ABpY18WPsyJ8LGvqGZR70l"; // Replace with your test key secret

        $environment =  get_payment_settings('razor_pay_environment');
     
        if ($environment=="test") {
            $keySecret =  get_payment_settings('razor_pay_test_secrect_key');
            $keyId =  get_payment_settings('razor_pay_test_api_key');
        }else{
            $keySecret =  get_payment_settings('razor_pay_secrect_key');
            $keyId =  get_payment_settings('razor_pay_live_api_key');
        }
        // dd($environment,$keyId);

// dd($this->api->order);

        $user = User::find(request()->input('user_id'));

        // Extract parameters from the request
        $amount = ($request->input('amount') * 100 );
        $name = $user->name ?? 'bala';
        $email = $user->email ?? 'balathemask@gmail.com';
        $mobile = $user->mobile ?? '9790200663';
        $currency = $user->countryDetail->currency_code ?? "INR";

        $payment_for = $request->input('payment_for');
        $request_id = $request->input('request_id') ?? "test";


        // Create an order
        $order = $this->api->order->create([
            'amount' => $amount, // amount in paisa
            'currency' => $currency,
            'receipt' => 'order_' . time(),
            'payment_capture' => 1 // auto capture
        ]);

        // Ensure that the order ID is passed correctly to the view
        return view('Razorpay.razorpay', ['order' => $order, 'key' => $keyId, 'payment_for' => $payment_for, 'request_id' => $request_id, 'user' => $user, 'amount'=>$request->input('amount'), 'currency' => $currency]);
    }


    public function razorpay_success(ValidatorRequest $request)
    {
// Log::info("razor pay sucess from api/v1");

// Log::info(request()->all());
// dd(request()->all());


        $web_booking_value = 0;

       $payment_for = request()->input('payment_for');

        if ($payment_for=="wallet") {

        $request_id = null;


         $user = User::find(request()->input('user_id'));

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
        $user_wallet->amount_added += request()->input('amount');
        $user_wallet->amount_balance += request()->input('amount');
        $user_wallet->save();
        $user_wallet->fresh();

        $wallet_add_history_model::create([
            'user_id'=>$user_id,
            'amount'=>request()->input('amount'),
            'transaction_id'=>request()->input('order_id'),
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
                $plan_id = $request->input('plan_id');
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

                // $title = custom_trans('subscription_title', [], $user->lang);
                // $body = custom_trans('subscription_body', [], $user->lang);
                

                // dispatch(new SendPushNotification($user,$title,$body));

                $notification = \DB::table('notification_channels')
                            ->where('topics', 'User Subscription') // Match the correct topic
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

                
        }else{

            $request_id = request()->input('request_id');
            // Log::info($request_id);

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


            return view('success',['success'],compact('web_booking_value','request_id'));

    }

}
