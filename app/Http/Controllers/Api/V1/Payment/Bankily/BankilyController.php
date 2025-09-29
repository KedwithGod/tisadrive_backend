<?php

namespace App\Http\Controllers\Api\V1\Payment\Bankily;

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

/**
 * @group Payment Gateway
 *
 * Payment-Related Apis
 */
class BankilyController extends ApiController
{

     public function __construct(Database $database)
    {
        $this->database = $database;
    }

    function authenticate(Request $request) {
        $user = auth()->user();
        $client = new \GuzzleHttp\Client();
        
        $response = $client->post('https://ebankily-tst.appspot.com/authentification', [  
            'headers' => [  
                'Content-Type' => 'application/x-www-form-urlencoded',  
            ],  
            'form_params' => [
                'grant_type' =>'password',
                'username'=>$user->name,
                'password' => $request->password,
                'client_id' => 'ebankily',
            ],
        ]);
        dd($response);

        $result = $response->getBody()->getContents();

        return $this->respondSuccess($result);

    }


    function refresh(Request $request) {
        $user = auth()->user();
        $client = new \GuzzleHttp\Client();
        
        $response = $client->post('https://ebankily-tst.appspot.com/authentification', [  
            'headers' => [  
                'Content-Type' => 'application/x-www-form-urlencoded',  
            ],  
            'form_params' => [
                'grant_type' =>'refresh_token',
                'refresh_token'=>'refresh_token',
                'client_id' => 'ebankily',
            ],
        ]);
        dd($response);

        $result = $response->getBody()->getContents();

        return $this->respondSuccess($result);

    }
    
    /**
     * Initialize Payment
     * 
     * 
     * 
     * */
    public function makePayment(Request $request){

        $client = new \GuzzleHttp\Client();  

        $user = auth()->user();

        $mobile = $user->mobile;

        $otp = $request->otp;

        $transaction_id = str_random(10);
          // dd($otp);

        // dd($mobile);

        $amount = $request->amount;

        $response = $client->post('https://ebankily-tst.appspot.com/payment/', [  
            'headers' => [  
                'Content-Type' => 'application/x-www-form-urlencoded',  
            ],
            'form_params' => [
                'amount' =>$amount,
                'clientPhone'=>$mobile,
                'passcode' => $otp,
                'operationId' => $transaction_id,
                'language' => strtoupper($user->lang),
            ],
        ]);  



        $result = $response->getBody()->getContents();

        dd($result);

        $parsedData = $this->parseXmlResponse($result);

        // dd($parsedData['status']);


        if($parsedData['status']==200){

            // Add money to wallet or subscription



            $payment_for = $request->payment_for;


            $request_id = $request->request_id;

            $plan_id = $request->plan_id;

            $this->processPayment($user,$payment_for,$request_id,$amount,$plan_id);


            return response()->json(['success'=>true,'status'=>200,'message'=>'payment-done']);
        }

        return response()->json($parsedData);




    }





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
