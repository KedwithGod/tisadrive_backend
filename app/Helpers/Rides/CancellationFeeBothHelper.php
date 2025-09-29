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

trait CancellationFeeBothHelper
{
    protected function handleCancellationFeeUnified($request_detail, $isDriver = false)
    {
        if ($isDriver) {
            $driver = auth()->user()->driver;

            // Update driver's availability
            $driver->available = true;
            $driver->save();
            $driver->fresh();

            $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();

            if (!$request_detail) {
                $this->throwAuthorizationException();
            }

            DriverRejectedRequest::create([
                'request_id'     => $request_detail->id,
                'is_after_accept'=> true,
                'driver_id'      => $driver->id,
                'reason'         => $request->reason,
                'custom_reason'  => $request->custom_reason,
            ]);

        } else {
            $user = auth()->user();

            $request_detail = $user->requestDetail()->where('id', $request->request_id)->first();

            if (!$request_detail) {
                $this->throwAuthorizationException();
            }

            $cancel_method = UserType::USER;

            $request_detail->update([
                'is_cancelled'   => true,
                'reason'         => $request->reason,
                'custom_reason'  => $request->custom_reason,
                'cancel_method'  => $cancel_method,
                'cancelled_at'   => now()
            ]);

            $request_detail->fresh();
        }

        // Determine if cancellation fee should be charged
        $charge_applicable = false;
        $reason = null;

        if ($request->reason) {
            $reason = CancellationReason::find($request->reason);

            if ($reason) {
                if ($reason->payment_type === 'free') {
                    $charge_applicable = false;
                } elseif (
                    $reason->payment_type === 'compensate' &&
                    $reason->compensate_from === ($isDriver ? 'driver' : 'user')
                ) {
                    $charge_applicable = true;
                }
            }
        }

        if ($request->custom_reason && !$request->reason) {
            $charge_applicable = true;
        }

        if (!$charge_applicable) {
            return;
        }

        $ride_type = zoneRideType::RIDENOW;
        $zoneTypePrice = $request_detail->zoneType->zoneTypePrice()
            ->where('price_type', $ride_type)
            ->first();

        if (!$zoneTypePrice) return;

        $totalFare = $request_detail->total ?? 0;
        $feePercent = $isDriver ? $zoneTypePrice->cancellation_fee_for_driver : $zoneTypePrice->cancellation_fee_for_user;
        $feeAmount = ($totalFare * $feePercent) / 100;

        $feeGoesTo = $zoneTypePrice->fee_goes_to ?? null;
        $driverShare = 0;
        $adminShare = 0;

        if (!$isDriver) {
            if ($feeGoesTo === 'driver') {
                $driverShare = $feeAmount;
            } elseif ($feeGoesTo === 'admin') {
                $adminShare = $feeAmount;
            } elseif ($feeGoesTo === 'partially_driver_admin') {
                $driverPercentage = $zoneTypePrice->driver_get_fee_percentage ?? 0;
                $adminPercentage = $zoneTypePrice->admin_get_fee_percentage ?? 0;
                $driverShare = ($feeAmount * $driverPercentage) / 100;
                $adminShare = ($feeAmount * $adminPercentage) / 100;
            }

            if ($request_detail->payment_opt == PaymentType::WALLET) {
                $wallet = $request_detail->userDetail->userWallet;
                if ($wallet && $wallet->amount_balance >= $feeAmount) {
                    $wallet->amount_spent += $feeAmount;
                    $wallet->amount_balance -= $feeAmount;
                    $wallet->save();

                    $wallet->walletHistory()->create([
                        'amount'         => $feeAmount,
                        'transaction_id' => $request_detail->id,
                        'remarks'        => WalletRemarks::CANCELLATION_FEE,
                        'request_id'     => $request_detail->id,
                        'is_credit'      => false,
                    ]);
                }
            }
        } else {
            $requested_driver = $request_detail->driverDetail;

            if ($requested_driver->owner()->exists()) {
                $wallet = $requested_driver->owner->ownerWalletDetail;
                $historyRelation = $requested_driver->owner->ownerWalletHistoryDetail();
            } else {
                $wallet = $requested_driver->driverWallet;
                $historyRelation = $wallet->walletHistory();
            }

            if ($wallet && $wallet->amount_balance >= $feeAmount) {
                $wallet->amount_spent += $feeAmount;
                $wallet->amount_balance -= $feeAmount;
                $wallet->save();

                $historyRelation->create([
                    'amount'         => $feeAmount,
                    'transaction_id' => $request_detail->id,
                    'remarks'        => WalletRemarks::CANCELLATION_FEE,
                    'request_id'     => $request_detail->id,
                    'is_credit'      => false,
                ]);
            }
        }

        // Save fee record
        $request_detail->requestCancellationFee()->create([
            'user_id'          => $isDriver ? null : $request_detail->user_id,
            'driver_id'        => $isDriver ? $request_detail->driver_id : null,
            'is_paid'          => !$isDriver && $request_detail->payment_opt == PaymentType::WALLET,
            'cancellation_fee' => $feeAmount,
            'driver_share'     => $driverShare,
            'admin_share'      => $adminShare,
            'paid_request_id'  => $request_detail->id,
        ]);
    }
}
