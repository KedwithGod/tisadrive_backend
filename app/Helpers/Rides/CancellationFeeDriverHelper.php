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
use App\Base\Constants\Masters\zoneRideType;
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
use App\Helpers\Rides\FetchDriversFromFirebaseHelpers;
use App\Http\Controllers\Api\V1\Payment\Stripe\StripeController;

trait CancellationFeeDriverHelper
{
    protected function handleDriverCancellationFee($request_detail, $request)
{
    $driver = auth()->user()->driver;

    // // Update the driver's available status
    // $driver->available = true;
    // $driver->save();
    // $driver->fresh();

    // // Fetch the request detail
    // $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();

    // if (!$request_detail) {
    //     $this->throwAuthorizationException();
    // }

    // Record driver rejection
    DriverRejectedRequest::create([
        'request_id'     => $request_detail->id,
        'is_after_accept'=> true,
        'driver_id'      => $driver->id,
        'reason'         => $request->reason,
        'custom_reason'  => $request->custom_reason,
    ]);

    /**
     * Apply Cancellation Fee
     */
    $charge_applicable = false;
    $reason = null;
    $compensate_from = null;

    Log::info('Cancellation Request Payload', $request->all());

    // Try to fetch reason if it's provided
    if (!empty($request->reason)) {
        $reason = CancellationReason::find($request->reason);
        Log::info('Loaded reason from DB:', $reason ? $reason->toArray() : ['Not found']);
        Log::info('Compensate from value: ' . ($reason ? $reason->compensate_from : 'N/A'));

        if ($reason) {
            if ($reason->payment_type === 'free') {
                $charge_applicable = false;
            } elseif ($reason->payment_type === 'compensate') {
                $compensate_from = $reason->compensate_from;
                $charge_applicable = true;
            }
        }
    }

    // If no reason ID, but custom_reason is present => treat as custom reason
    if (!$reason && !empty($request->custom_reason)) {
        $charge_applicable = true;
        Log::info('Custom reason only, treating as chargeable');
    }

    if (!$charge_applicable) {
        Log::info('No cancellation fee applicable');
        return;
    }

    $zoneTypePrice = $request_detail->zoneType->zoneTypePrice()->where('price_type', 1)->first();
    if (!$zoneTypePrice) return;

    $feePercent = $zoneTypePrice->cancellation_fee_for_driver;
    $totalFare = $request_detail->request_eta_amount ?? 0;
    $cancellation_fee = ($totalFare * $feePercent) / 100;

    $requested_driver = $request_detail->driverDetail;
    $is_paid = false;
    $paid_by = null;

    // ✅ Compensate from USER
    if ($reason && $reason->compensate_from === 'compensate_from_user') {
        // $user = $request_detail->userDetail;
        // $user_wallet = $user->userWallet;

        Log::info("Compensation to be taken from USER");
        // Log::info($user_wallet);

        // if ($request_detail->payment_opt === PaymentType::WALLET && $user_wallet && $user_wallet->amount_balance >= $cancellation_fee) {
        //     $user_wallet->amount_spent += $cancellation_fee;
        //     $user_wallet->amount_balance -= $cancellation_fee;
        //     $user_wallet->save();

        //     $user->userWalletHistory()->create([
        //         'amount' => $cancellation_fee,
        //         'transaction_id' => $request_detail->id,
        //         'remarks' => WalletRemarks::CANCELLATION_FEE,
        //         'request_id' => $request_detail->id,
        //         'is_credit' => false,
        //     ]);

        //     $is_paid = true;
        //     $paid_by = 'user';
        // }
        $is_paid = false;

        // Deduct from user's wallet if applicable
        if ($request_detail->payment_opt == PaymentType::WALLET) {
            $user = $request_detail->userDetail;
            $wallet = $user->userWallet;
            Log::info($wallet);
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


            // Apply fee to either owner or driver wallet
            $requested_driver = $request_detail->driverDetail;

            if ($requested_driver->owner()->exists()) {
                Log::info("Owner wallet");
                $owner_wallet = $requested_driver->owner->ownerWalletDetail;
                $owner_wallet->amount_added += $cancellation_fee;
                $owner_wallet->amount_balance += $cancellation_fee;
                $owner_wallet->save();

                // Owner wallet history
                $requested_driver->owner->ownerWalletHistoryDetail()->create([
                    'amount'         => $cancellation_fee,
                    'transaction_id' => $request_detail->id,
                    'remarks'        => WalletRemarks::USER_CANCELLATION_FEE,
                    'request_id'     => $request_detail->id,
                    'is_credit'      => true,
                ]);
            } else {
                Log::info("driver wallet");
                $driver_wallet = $requested_driver->driverWallet;
                $driver_wallet->amount_added += $cancellation_fee;
                $driver_wallet->amount_balance += $cancellation_fee;
                $driver_wallet->save();

                // Driver wallet history
                $requested_driver->driverWalletHistory()->create([
                    'amount'         => $cancellation_fee,
                    'transaction_id' => $request_detail->id,
                    'remarks'        => WalletRemarks::USER_CANCELLATION_FEE,
                    'request_id'     => $request_detail->id,
                    'is_credit'      => true,
                ]);
            }

    // ✅ Compensate from DRIVER or custom reason
    } elseif (
        ($reason && $reason->compensate_from === 'compensate_from_driver') ||
        (!$reason && !empty($request->custom_reason))
    ) {
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
    }

    $data = [
        'cancellation_reason_id' => $request->reason,
        'custom_reason' => $request->custom_reason,
        'is_paid' => $is_paid,
        'cancellation_fee' => $cancellation_fee,
        'paid_request_id' => $request_detail->id,
    ];

    if ($reason && $reason->compensate_from === 'compensate_from_user') {
        $data['user_id'] = $request_detail->user_id;
    } else {
        $data['driver_id'] = $request_detail->driver_id;
    }

    $request_detail->requestCancellationFee()->create($data);

    Log::info("Cancellation fee charged", $data);
}
}
