<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Payment\CardInfo;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\ApiController;
use App\Models\Payment\UserWalletHistory;
use App\Models\Payment\DriverWalletHistory;
use App\Transformers\Payment\WalletTransformer;
use App\Base\Payment\BrainTreeTasks\BraintreeTask;
use App\Transformers\Payment\DriverWalletTransformer;
use App\Http\Requests\Payment\AddMoneyToWalletRequest;
use App\Transformers\Payment\UserWalletHistoryTransformer;
use App\Transformers\Payment\DriverWalletHistoryTransformer;
use App\Transformers\Payment\WalletWithdrawalRequestsTransformer;
use App\Models\Payment\WalletWithdrawalRequest;
use App\Http\Controllers\Api\V1\BaseController;
use App\Base\Constants\Masters\WithdrawalRequestStatus;
use App\Base\Constants\Setting\Settings;
use App\Models\Payment\OwnerWalletHistory;
use App\Transformers\Payment\OwnerWalletHistoryTransformer;
use App\Models\Payment\UserWallet;
use App\Models\Payment\DriverWallet;
use App\Models\Payment\OwnerWallet;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Jobs\Notifications\SendPushNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletAmountTransferMail;
use App\Jobs\Mails\SendAmountTransferMailNotification;
/**
 * @group Payment
 * @authenticated
 * Payment-Related Apis
 */
class PaymentController extends BaseController
{
    protected $gateway;

    protected $callable_gateway_class;


    public function __construct()
    {
        $this->gateway = env('PAYMENT_GATEWAY');
        $this->callable_gateway_class = app(config('base.payment_gateway.' . $this->gateway . '.class'));
    }

    /**
     * List cards
     * @return \Illuminate\Http\JsonResponse
     * @response {
     * "success": true,
     * "message": "card_listed_succesfully",
     * "data": [
     * {
     * "id": "33f6a61d-4ddc-47dc-a601-250672dbc405",
     * "customer_id": "customer_765_6",
     * "merchant_id": "pwc2hd46g93s4zy2",
     * "card_token": "79dhmq",
     * "last_number": 521,
     * "card_type": "VISA",
     * "user_id": 6,
     * "is_default": 0,
     * "user_role": "driver",
     * "valid_through":"12/2021",
     * "created_at": "2019-05-06 13:17:40",
     * "updated_at": "2019-05-06 13:17:40",
     * "deleted_at": null
     * }
     * ]
     * }
     */
    public function listCards()
    {
        $result = CardInfo::where('user_id', auth()->user()->id)->get();
        
        return $this->respondSuccess($result, 'card_listed_succesfully');
    }

    /**
     * Make card as default card
     * @bodyParam card_id uuid required card id choosen by user
     * @response {
     * "success": true,
     * "message": "card_made_default_succesfully"
     * }
     */
    public function makeDefaultCard(Request $request)
    {
          $card_info = CardInfo::where('user_id', auth()->user()->id)->where('is_default', true)->first();

        if ($card_info) {
            $card_info->is_default = false;

            $card_info->save();
        }

        CardInfo::where('id', $request->card_id)->where('user_id', auth()->user()->id)->update(['is_default'=>true]);

        return $this->respondSuccess($data = null, 'card_made_default_succesfully');
}


    /**
     * Delete Card
     * @response {
     * "success": true,
     * "message": "card_deleted_succesfully"
     * }
     */
    public function deleteCard(CardInfo $card)
    {
        // if ($card->is_default) {
        //     $this->throwCustomException('you cannot delete your default card');
        // }

        $card->delete();

        return $this->respondSuccess($data = null, 'card_deleted_succesfully');    
    }

    /**
     * Wallet history
     * @responseFile responses/payment/wallet_added_history.json
     */
    public function walletHistory()
    {
        if (access()->hasRole(Role::USER)) {
            $query = UserWalletHistory::where('user_id', auth()->user()->id);
            // $result = fractal($query, new UserWalletHistoryTransformer);
            $result = filter($query, new UserWalletHistoryTransformer)->defaultSort('-created_at')->paginate();

            $user_wallet = auth()->user()->userWallet;

            $wallet_balance = number_format($user_wallet->amount_balance,2);
            
            $currency_code = auth()->user()->countryDetail->currency_code;
            $currency_symbol = auth()->user()->countryDetail->currency_symbol;

            // $currency_code = get_settings('currency_code');
            // $currency_symbol = get_settings('currency_symbol');
            $default_card = CardInfo::where('user_id', auth()->user()->id)->where('is_default', true)->first();
            $default_card_id = null;
            if ($default_card) {
                $default_card_id = $default_card->id;
            }

        } elseif (access()->hasRole(Role::DRIVER)) {
            $query = DriverWalletHistory::where('user_id', auth()->user()->driver->id)->orderBy('created_at', 'desc');
            $result = filter($query, new DriverWalletHistoryTransformer)->defaultSort('-created_at')->paginate();

            $driver_wallet = auth()->user()->driver->driverWallet;

            $wallet_balance = $driver_wallet->amount_balance;

            $currency_code = auth()->user()->countryDetail->currency_code;
            $currency_symbol = auth()->user()->countryDetail->currency_symbol;

            // $currency_code = get_settings('currency_code');
            // $currency_symbol = get_settings('currency_symbol');

            $default_card = CardInfo::where('user_id', auth()->user()->id)->where('is_default', true)->first();
            $default_card_id = null;
            if ($default_card) {
                $default_card_id = $default_card->id;
            }
        } else {

            $query = OwnerWalletHistory::where('user_id', auth()->user()->owner->id)->orderBy('created_at', 'desc');
            $result = filter($query, new OwnerWalletHistoryTransformer)->defaultSort('-created_at')->paginate();

            $owner_wallet = auth()->user()->owner->ownerWalletDetail;

            if (!$owner_wallet) {

                $wallet_balance = 0;

            } else {
                $wallet_balance = $owner_wallet->amount_balance;

            }

            $currency_code = auth()->user()->countryDetail->currency_code;
            $currency_symbol = auth()->user()->countryDetail->currency_symbol;

            // $currency_code = get_settings('currency_code');
            // $currency_symbol = get_settings('currency_symbol');

            $default_card = CardInfo::where('user_id', auth()->user()->id)->where('is_default', true)->first();
            $default_card_id = null;
            if ($default_card) {
                $default_card_id = $default_card->id;
            }
        }

        $bank_info_exists = false;

        if (auth()->user()->bankInfo()->exists()) {

            $bank_info_exists = true;
        }


        // $cashfree_image = asset('assets/payment_gateway/cashfree.jpeg');
     
        $settings = [
                'enable_paystack' => get_payment_settings('enable_paystack'),
                'enable_cashfree' => get_payment_settings('enable_cashfree'),
                'enable_mercadopago' => get_payment_settings('enable_mercadopago'),
                'enable_stripe' => get_payment_settings('enable_stripe'),
                'enable_flutterwave' => get_payment_settings('enable_flutterwave'),
                'enable_razorpay' => get_payment_settings('enable_razorpay'),
                'enable_khalti' => get_payment_settings('enable_khalti'),
                'enable_xendit' => get_payment_settings('enable_xendit'),
                'enable_flexpaie' => get_payment_settings('enable_flexpaie'),
            ];

            $flags = [];

            foreach ($settings as $flag => $settingKey) {
            // dd($flag);

                $flags[$flag] = get_payment_settings($flag) == '1';
            }


            $images = [
                'flutterwave' => asset('assets/img/flutterwave.png'),
                'mercadopago' => asset('assets/img/mercadepago.png'),
                'cashfree' => asset('assets/img/cashfree.png'),
                'paystack' => asset('assets/img/paystack.png'),
                'razorpay' => asset('assets/img/razor.png'),
                'stripe' => asset('assets/img/stripe.png'),
                'khalti' => asset('assets/img/khalti.png'),
                'xendit' => asset('assets/img/xendit-logo.jpg'),
                'flexpaie' => asset('assets/img/flexpaie.png'),
            ];

            $url = env('APP_URL');

            $payment_gateways = [];


            $car_details = auth()->user()->userCards;

            if($car_details){

                foreach ($car_details as $key => $car_detail) {
                    
               $payment_gateways[] = [
                'is_card'=>true,
                'gateway'=>$car_detail->last_number,
                'enabled'=>true,
                'image'=>$car_detail->card_type,
                'url'=>$car_detail->card_token
               ]; 


                }
                
            }

            foreach ($images as $gateway => $image) 
            {
                $payment_gateways[] = [
                    'is_card'=>false,
                    'gateway' => $this->toCamelCase($gateway),
                    'enabled' => $flags["enable_{$gateway}"] ?? false,
                    'image' => $image,
                    'url' => route($gateway),
                ];
            }

        return response()->json(['success' => true,
            'message' => 'wallet_history_listed',
            'wallet_balance' => $wallet_balance,
            'default_card_id' => $default_card_id,
            'currency_code' => $currency_code,
            'currency_symbol' => $currency_symbol,
            'wallet_history' => $result,
            'bank_info_exists' => $bank_info_exists,
            'enable_save_card'=> $flags["enable_stripe"],
            'payment_gateways' => $payment_gateways,
            'minimum_amount_added_to_wallet' => get_settings('minimum_amount_added_to_wallet'),
        ]);

        // return $this->respondSuccess($result, 'wallet_history_listed');
    }

     public function toCamelCase($string)
    {
        // Remove non-alphanumeric characters (optional)
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);

        // Convert to lowercase and split into words
        $words = explode(' ', strtolower($string));

        // Capitalize the first letter of each word except the first one
        $camelCaseString = array_shift($words); // Remove and get the first word
        foreach ($words as $word) {
            $camelCaseString .= ucfirst($word);
    }

        return $camelCaseString;
    }
    /**
     * Wallet Withdrawal Requests LIst
     *
     * @response
     *  {
     *      "success": true,
     *      "message": "withdrawal-requests-listed",
     *      "withdrawal_history": {
     *          "data": [
     *              {
     *                  "id": 2,
     *                  "requested_amount": 10,
     *                  "driver_id": 2,
     *                  "owner_id": null,
     *                  "driver_name": "bala",
     *                  "driver_mobile": "9790200663",
     *                  "owner_name": "",
     *                  "owner_mobile": "",
     *                  "created_at": "25th Nov 12:05 PM",
     *                  "updated_at": "25th Nov 12:05 PM",
     *                  "payment_status": "requested",
     *                  "status": "Requested"
     *              }
     *          ],
     *          "meta": {
     *              "pagination": {
     *                  "total": 1,
     *                  "count": 1,
     *                  "per_page": 10,
     *                  "current_page": 1,
     *                  "total_pages": 1,
     *                  "links": {}
     *              }
     *          }
     *      },
     *      "wallet_balance": 610
     *  }
     * */
    public function withDrawalRequests()
    {
        if (access()->hasRole(Role::USER)) {

            $user = auth()->user();

            $query = WalletWithdrawalRequest::where('user_id', $user->id);

            $result = filter($query, new WalletWithdrawalRequestsTransformer)->defaultSort('-created_at')->paginate();

            // $result = fractal($query, new WalletWithdrawalRequestsTransformer);

            $user_wallet = auth()->user()->userWallet;
            $wallet_balance = $user_wallet->amount_balance;


        } elseif (access()->hasRole(Role::DRIVER)) {

            $user = auth()->user()->driver;

            $query = WalletWithdrawalRequest::where('driver_id', $user->id);

            $result = filter($query, new WalletWithdrawalRequestsTransformer)->defaultSort('-created_at')->paginate();


            // $result = fractal($query, new WalletWithdrawalRequestsTransformer);

            $driver_wallet = auth()->user()->driver->driverWallet;

            $wallet_balance = $driver_wallet->amount_balance;

        } else {

            $user = auth()->user()->owner;

            $query = WalletWithdrawalRequest::where('owner_id', $user->id);

            $result = filter($query, new WalletWithdrawalRequestsTransformer)->defaultSort('-created_at')->paginate();


            // $result = fractal($query, new WalletWithdrawalRequestsTransformer);

            $owner_wallet = auth()->user()->owner->ownerWalletDetail;

            $wallet_balance = $owner_wallet->amount_balance;
        }

        return response()->json(['success' => true, 'message' => 'withdrawal-requests-listed', 'withdrawal_history' => $result, 'wallet_balance' => $wallet_balance]);

    }


    /**
     * Request for withdrawal
     * @bodyParam requested_amount double required  amount entered by user
     *
     * @response
     * {
     *      "success": true,
     *      "message": "wallet_withdrawal_requested"
     *  }
     * */
    public function requestForWithdrawal(Request $request)
    {

        $created_params = $request->all();
        $created_params['payment_status'] = "requested";


        if (access()->hasRole(Role::USER)) {

            $user_info = auth()->user();

            $currency_code = auth()->user()->countryDetail->currency_code;
            $currency_symbol = auth()->user()->countryDetail->currency_symbol;

            // $currency_code = get_settings('currency_code');

            $created_params['requested_currency'] = $currency_code;
            $created_params['user_id'] = auth()->user()->id;


            $user_wallet = auth()->user()->userWallet;
            $wallet_balance = $user_wallet->amount_balance;

            if ($wallet_balance <= 0) {

                $this->throwCustomException('Your wallet balance is too low');

            }
            if ($wallet_balance < $request->requested_amount) {

                $this->throwCustomException('Your wallet balance is too low than your requested amount');

            }

            $user_info->withdrawalRequestsHistory()->where('status', WithdrawalRequestStatus::REQUESTED)->exists();
            if ($user_info) {
                $this->throwCustomException('You cannot make multiple request. please wait for your existing request approval');
            }

        } elseif (access()->hasRole(Role::DRIVER)) {

            $user_info = auth()->user()->driver;

            // $currency_code = get_settings('currency_symbol');
            $currency_code = auth()->user()->countryDetail->currency_code;
            $currency_symbol = auth()->user()->countryDetail->currency_symbol;

            $created_params['requested_currency'] = $currency_code;
            $created_params['driver_id'] = auth()->user()->driver->id;

            $driver_wallet = auth()->user()->driver->driverWallet;

            $wallet_balance = $driver_wallet->amount_balance;

            if ($wallet_balance <= 0) {

                $this->throwCustomException('Your wallet balance is too low');

            }

            if ($wallet_balance < $request->requested_amount) {

                $this->throwCustomException('Yout wallet balance is too low than your requested amount');

            }

            // $user_info->withdrawalRequestsHistory()->where('status',0)->exists();

            $exists_request = WalletWithdrawalRequest::where('driver_id', $user_info->id)->where('status', 0)->exists();

            if ($exists_request == true) {
                $this->throwCustomException('You cannot make multiple request. please wait for your existing request approval');
            }

        } else {

            $user_info = auth()->user()->owner;

            // $currency_code = get_settings('currency_symbol');
            $currency_code = auth()->user()->countryDetail->currency_code;
            $currency_symbol = auth()->user()->countryDetail->currency_symbol;

            $created_params['requested_currency'] = $currency_code;
            $created_params['owner_id'] = auth()->user()->owner->id;

            $owner_wallet = auth()->user()->owner->ownerWalletDetail;

            $wallet_balance = $owner_wallet->amount_balance;

            if ($wallet_balance <= 0) {

                $this->throwCustomException('Your wallet balance is too low');

            }

            if ($wallet_balance < $request->requested_amount) {

                $this->throwCustomException('Yout wallet balance is too low than your requested amount');

            }

            // $user_info->withdrawalRequestsHistory()->where('status',0)->exists();

            $exists_request = WalletWithdrawalRequest::where('owner_id', $user_info->id)->where('status', 0)->exists();

            if ($exists_request == true) {
                $this->throwCustomException('You cannot make multiple request. please wait for your existing request approval');
            }

        }


        WalletWithdrawalRequest::create($created_params);

        return $this->respondSuccess(null, 'wallet_withdrawal_requested');


    }
   /**
     * Transfer money from wallet
     * @bodyParam mobile mobile required mobile of the user
     * @bodyParam role role required role of the user
     * @bodyParam amount amount required role of the user
     *
     * @response 
     * {
     *     "success": true,
     *     "transfer_remarks": false,
     *     "receiver_remarks": false
     * }
     * */
   public function transferMoneyFromWallet(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'role' => 'required',
            'amount' => 'required'
        ]);
        if($request->amount < 0) {
            $this->throwCustomException('Invalid Amount');
        }
        $user = auth()->user();
        
        $invalid_mobile = false;

        if($user->hasRole('user') && $request->role=='user'){
            
            $invalid_mobile = true;

        }
        if($user->hasRole('driver') && $request->role=='driver'){
            
            $invalid_mobile = true;

        }
         if($user->hasRole('owner') && $request->role=='owner'){
            
            $invalid_mobile = true;

        }
        if ($request->mobile == $user->mobile && $invalid_mobile) {

            //Throw exception
            $this->throwCustomException('Invalid Mobile Number');

        }
        if (access()->hasRole('user')) {
            $wallet_model = new UserWallet();
            $wallet_history_model = new UserWalletHistory();
            $user_id = auth()->user()->id;
        } elseif ($user->hasRole('driver')) {
            $wallet_model = new DriverWallet();
            $wallet_history_model = new DriverWalletHistory();
            $user_id = $user->driver->id;
        } else {
            $wallet_model = new OwnerWallet();
            $wallet_history_model = new OwnerWalletHistory();
            $user_id = $user->owner->id;
        }

        $user_wallet = $wallet_model::whereUserId($user_id)->first();

        $minimum_wallet_amount = get_settings(Settings::MINIMUM_WALLET_AMOUNT_FOR_TRANSFER);

        if ($user_wallet && $user_wallet->amount_balance < $minimum_wallet_amount) {

            //Throw exception
            $this->throwCustomException('Insufficient balance to transfer money to wallet');

        }

        $mobile_number = $request->mobile;
        $role = $request->role;
        $amount_to_transfer = $request->amount;

        // Validate requested amount with current balance

        if ($user_wallet->amount_balance < $amount_to_transfer) {
            // Throw exception
            $this->throwCustomException('Insufficient Balance');
        }

        // Find Receiver Wallet

        $receiver_user = User::belongsTorole($role)->where('mobile', $mobile_number)->first();

        if (!$receiver_user) {
            $this->throwCustomException('Mobile Number Does Not Exists');
        }

        $transaction_id = str_random(6);

        if ($role == 'user') {

            $receiver_wallet = $receiver_user->userWallet;
            if($receiver_wallet==null){
                $this->throwCustomException('This user Does Not have an E-Wallet');
            }
            $receiver_wallet_history_model = new UserWalletHistory();
            $receiver_wallet->amount_added += $amount_to_transfer;
            $receiver_wallet->amount_balance += $amount_to_transfer;
            $receiver_wallet->save();

            $receiver_user->userWalletHistory()->create([
                'transaction_id' => $transaction_id,
                'amount' => $amount_to_transfer,
                'is_credit' => true,
                'remarks' => 'transferred-from-' . $user->name
            ]);

        } elseif ($role == 'driver') {

            $receiver_wallet = $receiver_user->driver->driverWallet;
            if($receiver_wallet==null){
                $this->throwCustomException('This user Does Not have an E-Wallet');
            }
            $receiver_wallet_history_model = new DriverWalletHistory();
            $receiver_wallet->amount_added += $amount_to_transfer;
            $receiver_wallet->amount_balance += $amount_to_transfer;
            $receiver_wallet->save();

            $receiver_user->driver->driverWalletHistory()->create([
                'transaction_id' => $transaction_id,
                'amount' => $amount_to_transfer,
                'is_credit' => true,
                'remarks' => 'transferred-from-' . $user->name
            ]);

        } elseif ($role == 'owner') {

            $receiver_wallet = $receiver_user->owner->ownerWalletDetail;
            if($receiver_wallet==null){
                $this->throwCustomException('This user Does Not have an E-Wallet');
            }
            $receiver_wallet_history_model = new OwnerWalletHistory();
            $receiver_wallet->amount_added += $amount_to_transfer;
            $receiver_wallet->amount_balance += $amount_to_transfer;
            $receiver_wallet->save();

            $receiver_user->owner->ownerPaymentWalletHistoryDetail()->create([
                'transaction_id' => str_random(6),
                'amount' => $amount_to_transfer,
                'is_credit' => true,
                'remarks' => 'transferred-from-' . $user->name
            ]);

        }

        // $title = custom_trans('you_have_received_a_money_from_title', [], $receiver_user->lang);

        // $body = custom_trans('you_have_received_a_money_from_body', [], $receiver_user->lang);

        // dispatch(new SendPushNotification($receiver_user,$title,$body));

        $user_wallet->amount_spent -= $amount_to_transfer;
        $user_wallet->amount_balance -= $amount_to_transfer;
        $user_wallet->save();

        $wallet_history_model::create([
            'user_id' => $user_id,
            'amount' => $request->amount,
            'transaction_id' => $transaction_id,
            'remarks' => 'transfered-to-' . $receiver_user->name,
            'is_credit' => false]);
        $transfer_remarks = $wallet_history_model->update(['remarks']);
        $receiver_remarks = $receiver_wallet_history_model->update(['remarks']);
//        return $this->respondSuccess($remarks, 'transferred');

        $currency = $user->countryDetail()->pluck('currency_symbol')->first();

            $notification = \DB::table('notification_channels')
                    ->where('topics', 'User Amount Transfer') // Match the correct topic
                    ->first();
            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $receiver_user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($receiver_user, $title, $body));
                }
                SendAmountTransferMailNotification::dispatch($user, $transaction_id, $currency, $request->amount, $user_wallet,$receiver_user);
        return response()->json(['success' => true, 'transfer_remarks' => $transfer_remarks, 'receiver_remarks' => $receiver_remarks]);
    }
    /**
     * 
     * Convert Points to Wallet Points
     * @authenticated
     * 
     * @bodyParam amount number Example: 100 
     * @response 
     * {
     *     "success": true,
     *     "wallet_remarks": {
     *         "user_id": 2,
     *         "amount": "0.50",
     *         "transaction_id": "GASdCW",
     *         "remarks": "conversion-from-point",
     *         "is_credit": true,
     *         "id": "29d600fa-fd5d-4afb-a284-2917797f843a",
     *         "updated_at": "2024-11-25T06:44:56.000000Z",
     *         "created_at": "2024-11-25T06:44:56.000000Z",
     *         "converted_created_at": "25th Nov 12:14 PM"
     *     },
     *     "loyalty_remarks": {
     *         "reward_points": "10",
     *         "is_credit": false,
     *         "remarks": "conversion-from-point",
     *         "user_id": 3,
     *         "id": "bf871067-6830-4c9b-80c8-4942913b7232",
     *         "updated_at": "2024-11-25T06:44:56.000000Z",
     *         "created_at": "2024-11-25T06:44:56.000000Z"
     *     }
     * }
     */
    public function transferCreditFromPoints(Request $request)
    {
        $user = auth()->user();
        $rewards_to_transfer = $request->amount;
        if($rewards_to_transfer > $user->rewardPoint->balance_reward_points){
            $this->throwCustomException('Insufficient Balance');
        }

        
        $user->rewardPoint->balance_reward_points -= $rewards_to_transfer;
        $user->rewardPoint->points_spend += $rewards_to_transfer;
        $user->rewardPoint->save();


        $loyalty_remarks = $user->rewardHistory()->create([
            'reward_points' => $rewards_to_transfer,
            'is_credit' => false,
            'remarks' => "conversion-from-point",
        ]);
        $amount_to_transfer = number_format($rewards_to_transfer / get_settings('reward_point_value'),2);

        if ($user->hasRole('user')) {
            $wallet_model = new UserWallet();
            $wallet_history_model = new UserWalletHistory();
            $user_id = $user->id;

        } elseif ($user->hasRole('driver')) {
            $wallet_model = new DriverWallet();
            $wallet_history_model = new DriverWalletHistory();
            $user_id = $user->driver->id;

        }

        $user_wallet = $wallet_model::whereUserId($user_id)->first();
        $user_wallet->amount_spent += $amount_to_transfer;
        $user_wallet->amount_balance += $amount_to_transfer;
        $user_wallet->save();

        $wallet_history = $wallet_history_model::create([
            'user_id' => $user_id,
            'amount' => $amount_to_transfer,
            'transaction_id' => str_random(6),
            'remarks' => "conversion-from-point",
            'is_credit' => true
        ]);

        // $title = custom_trans('conversion_credited_title', [], $user->lang);

        // $body = custom_trans('conversion_credited_body', [], $user->lang);

        // dispatch(new SendPushNotification($user,$title,$body));

         $notification = \DB::table('notification_channels')
                ->where('topics', 'User Transfer Credit Points') // Match the correct topic
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

        return response()->json(['success' => true, 'wallet_remarks' => $user_wallet, 'loyalty_remarks' => $loyalty_remarks]);

    }


    public function paymentGatewaysForRide(){
        
     
        $settings = [
            'enable_paystack' => get_payment_settings('enable_paystack'),
            'enable_cashfree' => get_payment_settings('enable_cashfree'),
            'enable_mercadopago' => get_payment_settings('enable_mercadopago'),
            'enable_stripe' => get_payment_settings('enable_stripe'),
            'enable_flutterwave' => get_payment_settings('enable_flutterwave'),
            'enable_razorpay' => get_payment_settings('enable_razorpay'),
            'enable_khalti' => get_payment_settings('enable_khalti'),
            'enable_xendit' => get_payment_settings('enable_xendit'),
        ];

        $flags = [];

        foreach ($settings as $flag => $settingKey) {
        // dd($flag);

            $flags[$flag] = get_payment_settings($flag) == '1';
        }


        $images = [
            'flutterwave' => asset('assets/img/flutterwave.png'),
            'mercadopago' => asset('assets/img/mercadepago.png'),
            'cashfree' => asset('assets/img/cashfree.png'),
            'paystack' => asset('assets/img/paystack.png'),
            'razorpay' => asset('assets/img/razor.png'),
            'stripe' => asset('assets/img/stripe.png'),
            'khalti' => asset('assets/img/khalti.png'),
            'xendit' => asset('assets/img/xendit-logo.jpg'),
            'flexpaie' => asset('assets/img/flexpaie.png'),
        ];


        

        $url = env('APP_URL');

        $payment_gateways = [];

        foreach ($images as $gateway => $image) 
        {
            $payment_gateways[] = [
                'gateway' => $this->toCamelCase($gateway),
                'enabled' => $flags["enable_{$gateway}"] ?? false,
                'image' => $image,
                'url' => "{$url}{$gateway}",
            ];
        }

        return $this->respondSuccess($payment_gateways,'Payment_gateways_listed');
    }

    public function paymentGateways(){
        
     
        $settings = [
            'enable_paystack' => get_payment_settings('enable_paystack'),
            'enable_cashfree' => get_payment_settings('enable_cashfree'),
            'enable_mercadopago' => get_payment_settings('enable_mercadopago'),
            'enable_stripe' => get_payment_settings('enable_stripe'),
            'enable_flutterwave' => get_payment_settings('enable_flutterwave'),
            'enable_razorpay' => get_payment_settings('enable_razorpay'),
            'enable_khalti' => get_payment_settings('enable_khalti'),
            'enable_xendit' => get_payment_settings('enable_xendit'),
            'enable_flexpaie' => get_payment_settings('enable_flexpaie'),
        ];

        $flags = [];

        foreach ($settings as $flag => $settingKey) {
        // dd($flag);

            $flags[$flag] = get_payment_settings($flag) == '1';
        }




        $images = [
            'flutterwave' => asset('assets/img/flutterwave.png'),
            'mercadopago' => asset('assets/img/mercadepago.png'),
            'cashfree' => asset('assets/img/cashfree.png'),
            'paystack' => asset('assets/img/paystack.png'),
            'razorpay' => asset('assets/img/razor.png'),
            'stripe' => asset('assets/img/stripe.png'),
            'khalti' => asset('assets/img/khalti.png'),
            'xendit' => asset('assets/img/xendit-logo.jpg'),
        ];



        // $url = env('APP_URL');

         $payment_gateways = [];


            $car_details = auth()->user()->userCards;

            if($car_details){

                foreach ($car_details as $key => $car_detail) {
                    
               $payment_gateways[] = [
                'is_card'=>true,
                'gateway'=>$car_detail->last_number,
                'enabled'=>true,
                'image'=>$car_detail->card_type,
                'url'=>$car_detail->card_token
               ]; 


                }
                
            }

        foreach ($images as $gateway => $image) 
        {
            $payment_gateways[] = [
                'is_card'=>false,
                'gateway' => $this->toCamelCase($gateway),
                'enabled' => $flags["enable_{$gateway}"] ?? false,
                'image' => $image,
                'url' => route($gateway),
            ];
        }

        return $this->respondSuccess($payment_gateways,'Payment_gateways_listed');
    }
}