<?php

namespace App\Helpers\Rides;

use Kreait\Firebase\Contract\Database;
use Sk\Geohash\Geohash;
use Carbon\Carbon;
use App\Models\Request\RequestMeta;
use Illuminate\Support\Facades\DB;
use App\Models\Request\Request;
use Illuminate\Support\Facades\Log;
use App\Base\Constants\Setting\Settings;
use App\Models\Admin\Driver;
use App\Models\Admin\ZoneTypePrice;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Request\DriverRejectedRequest;
use App\Models\Admin\ZoneSurgePrice;
use App\Models\Request\RequestCancellationFee;
use App\Base\Constants\Masters\UserType;
use App\Models\Admin\CancellationReason;
use App\Models\Request\Request as RequestRequest;
use App\Transformers\Requests\TripRequestTransformer;
use App\Base\Constants\Masters\PaymentType;
use App\Jobs\NotifyViaMqtt;
use App\Jobs\NotifyViaSocket;
use App\Base\Constants\Masters\PushEnums;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Request\CancelTripRequest;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Base\Constants\Masters\WalletRemarks;
use App\Base\Constants\Masters\zoneRideType;
use Illuminate\Support\Facades\Artisan;
use App\Models\Admin\PromoUser;
use App\Helpers\Rides\PaymentOptionCalculationHelper;
use App\Transformers\Requests\RequestBillTransformer;
use App\Models\Admin\NotificationChannel;
use App\Http\Controllers\Api\V1\Payment\Stripe\StripeController;

trait CancellationFeeHelper
{
    protected function handleCancellationFee($request_detail , $request)
    {

        Log::info('user cancel helper');
        Log::info($request->all());
    
        $cancel_method = UserType::USER;
    
        $request_detail->update([
            'is_cancelled'   => true,
            'reason'         => $request->reason,
            'custom_reason'  => $request->custom_reason,
            'cancel_method'  => $cancel_method,
            'cancelled_at'   => now()
        ]);
    
        $request_detail->fresh();
    
        $charge_applicable = false;
        $reason = null;
    
        if ($request->reason) {
            $reason = CancellationReason::find($request->reason);
    
            if ($reason) {
                if ($reason->payment_type === 'free') {
                    $charge_applicable = false;
                } elseif ($reason->payment_type === 'compensate') {
                    $charge_applicable = true;
                }
            }
        }
    
        // if ($request->custom_reason && !$request->reason) {
        //     $charge_applicable = true; // custom reason = chargeable
        // }
         // If no reason ID, but custom_reason is present => treat as custom reason
    if (!$reason && !empty($request->custom_reason)) {
        $charge_applicable = true;
        Log::info('Custom reason only, treating as chargeable');
    }

    
        if (!$charge_applicable) {
            return;
        }
    
        $zoneTypePrice = $request_detail->zoneType->zoneTypePrice()
            ->where('price_type', 1)
            ->first();
    
        if (!$zoneTypePrice) return;
    
        $totalFare = $request_detail->request_eta_amount ?? 0;
        $feePercent = $zoneTypePrice->cancellation_fee_for_user;
        $cancellation_fee = ($totalFare * $feePercent) / 100;
    
        $requested_driver = $request_detail->driverDetail;
        $is_paid = false;
        $paid_by = null;
    
        // ====> New: Handle driver compensation
        if ($reason && empty($request->custom_reason) && $reason->compensate_from === 'compensate_from_driver') {
            Log::info("Compensation to be taken from DRIVER/OWNER");
    
            if ($requested_driver->owner()->exists()) {
                $owner_wallet = $requested_driver->owner->ownerWalletDetail;
                if ($owner_wallet && $owner_wallet->amount_balance >= $cancellation_fee) {
                    $owner_wallet->amount_spent += $cancellation_fee;
                    $owner_wallet->amount_balance -= $cancellation_fee;
                    $owner_wallet->save();
    
                    $requested_driver->owner->ownerWalletHistoryDetail()->create([
                        'amount' => $cancellation_fee,
                        'transaction_id' => $request_detail->id,
                        'remarks' => WalletRemarks::CANCELLATION_FEE,
                        'request_id' => $request_detail->id,
                        'is_credit' => false,
                    ]);
    
                    $is_paid = true;
                    $paid_by = 'owner';
                }
            } else {
                $driver_wallet = $requested_driver->driverWallet;
                if ($driver_wallet && $driver_wallet->amount_balance >= $cancellation_fee) {
                    $driver_wallet->amount_spent += $cancellation_fee;
                    $driver_wallet->amount_balance -= $cancellation_fee;
                    $driver_wallet->save();
    
                    $requested_driver->driverWalletHistory()->create([
                        'amount' => $cancellation_fee,
                        'transaction_id' => $request_detail->id,
                        'remarks' => WalletRemarks::CANCELLATION_FEE,
                        'request_id' => $request_detail->id,
                        'is_credit' => false,
                    ]);
    
                    $is_paid = true;
                    $paid_by = 'driver';
                }
            }
    
            $request_detail->requestCancellationFee()->create([
                'driver_id' => $request_detail->driver_id,
                'cancellation_reason_id' => $request->reason,
                'custom_reason' => $request->custom_reason,
                'is_paid' => $is_paid,
                'cancellation_fee' => $cancellation_fee,
                'paid_request_id' => $request_detail->id,
            ]);
    
            return;
        }
    
        // ====> Existing logic: Fee taken from USER
        $feeGoesTo = $zoneTypePrice->fee_goes_to;
        $driverShare = 0;
        $adminShare = 0;
    
        if ($feeGoesTo === 'driver') {
            if ($requested_driver->owner()->exists()) {
                Log::info("Owner wallet");
                $owner_wallet = $requested_driver->owner->ownerWalletDetail;
                $owner_wallet->amount_added += $cancellation_fee;
                $owner_wallet->amount_balance += $cancellation_fee;
                $owner_wallet->save();
    
                $requested_driver->owner->ownerWalletHistoryDetail()->create([
                    'amount' => $cancellation_fee,
                    'transaction_id' => $request_detail->id,
                    'remarks' => WalletRemarks::USER_CANCELLATION_FEE,
                    'request_id' => $request_detail->id,
                    'is_credit' => true,
                ]);
            } else {
                Log::info("Driver wallet");
                $driver_wallet = $requested_driver->driverWallet;
                $driver_wallet->amount_added += $cancellation_fee;
                $driver_wallet->amount_balance += $cancellation_fee;
                $driver_wallet->save();
    
                $requested_driver->driverWalletHistory()->create([
                    'amount' => $cancellation_fee,
                    'transaction_id' => $request_detail->id,
                    'remarks' => WalletRemarks::USER_CANCELLATION_FEE,
                    'request_id' => $request_detail->id,
                    'is_credit' => true,
                ]);
            }
    
            $driverShare = $cancellation_fee;
    
        } elseif ($feeGoesTo === 'admin') {
            $adminShare = $cancellation_fee;
    
        } elseif ($feeGoesTo === 'partially_driver_admin') {
            $driverPercentage = $zoneTypePrice->driver_get_fee_percentage ?? 0;
            $adminPercentage = $zoneTypePrice->admin_get_fee_percentage ?? 0;
            $driverShare = ($cancellation_fee * $driverPercentage) / 100;
            $adminShare = ($cancellation_fee * $adminPercentage) / 100;
        }
    
        // Deduct from USER's wallet
        if ($request_detail->payment_opt == PaymentType::WALLET) {
            $user = $request_detail->userDetail;
            $wallet = $user->userWallet;
            if ($wallet && $wallet->amount_balance >= $cancellation_fee) {
                $wallet->amount_spent += $cancellation_fee;
                $wallet->amount_balance -= $cancellation_fee;
                $wallet->save();
    
                $user->userWalletHistory()->create([
                    'amount' => $cancellation_fee,
                    'transaction_id' => $request_detail->id,
                    'remarks' => WalletRemarks::CANCELLATION_FEE,
                    'request_id' => $request_detail->id,
                    'is_credit' => false,
                ]);
    
                $is_paid = true;
            }
        }
    
        $paid_id = $is_paid ? $request_detail->id : null;
    
        $request_detail->requestCancellationFee()->create([
            'user_id' => $request_detail->user_id,
            'is_paid' => $is_paid,
            'cancellation_fee' => $cancellation_fee,
            'driver_share' => $driverShare,
            'admin_share' => $adminShare,
            'paid_request_id' => $paid_id,
            'cancellation_reason_id' => $request->reason,
            'custom_reason' => $request->custom_reason,
        ]);
    }
}
