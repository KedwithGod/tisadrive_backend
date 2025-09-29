<?php

namespace App\Helpers\Rides;

use Illuminate\Support\Facades\Log;
use App\Base\Constants\Masters\PaymentType;
use App\Base\Constants\Masters\WalletRemarks;

trait PaymentOptionCalculationHelper
{



    /**
     * Calculate and charge Ride fare
     * @param Request $request_detail with generated bill
     * 
     */
    //
    protected function handlePayment($request_detail)
    {
        if ($request_detail->payment_opt==PaymentType::CARD) {
            if(!$request_detail->is_parcel)
            {
                return true;
            }
        }
        $bill_detail = $request_detail->requestBill;
        
        if ($request_detail->payment_opt==PaymentType::WALLET) {
            $chargable_amount = $bill_detail->total_amount;
            $user_wallet = $request_detail->userDetail->userWallet;

            if ($chargable_amount<=$user_wallet->amount_balance) {
                $user_wallet->amount_balance -= $chargable_amount;
                $user_wallet->amount_spent += $chargable_amount;
                $user_wallet->save();

                $user_wallet_history = $request_detail->userDetail->userWalletHistory()->create([
                'amount'=>$chargable_amount,
                'transaction_id'=>str_random(6),
                'request_id'=>$request_detail->id,
                'remarks'=>WalletRemarks::SPENT_FOR_TRIP_REQUEST,
                'is_credit'=>false]);
            }else{
                return false;
            }
        }

        
        if ($request_detail->payment_opt==PaymentType::CASH){

            $total_admin_commission = $bill_detail->admin_commision_with_tax;

            if($request_detail->driverDetail->owner()->exists())
            {
                $owner_wallet = $request_detail->driverDetail->owner->ownerWalletDetail;
                $owner_wallet->amount_spent += $total_admin_commission;
                $owner_wallet->amount_balance -= $total_admin_commission;
                $owner_wallet->save();
    
                $owner_wallet_history = $request_detail->driverDetail->owner->ownerWalletHistoryDetail()->create([
                    'amount'=>$total_admin_commission,
                    'transaction_id'=>str_random(6),
                    'remarks'=>WalletRemarks::ADMIN_COMMISSION_FOR_REQUEST,
                    'is_credit'=>false
                ]);
            }else{
                $driver_wallet = $request_detail->driverDetail->driverWallet;
                $driver_wallet->amount_spent += $total_admin_commission;
                $driver_wallet->amount_balance -= $total_admin_commission;
                $driver_wallet->save();
    
                $driver_wallet_history = $request_detail->driverDetail->driverWalletHistory()->create([
                    'amount'=>$total_admin_commission,
                    'transaction_id'=>str_random(6),
                    'remarks'=>WalletRemarks::ADMIN_COMMISSION_FOR_REQUEST,
                    'is_credit'=>false
                ]);
            }

            if ($request_detail->promo_id){
                $discount = $bill_detail->promo_discount;

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
        }else{

            $driver_commision = $bill_detail->driver_commision;
            if($request_detail->driverDetail->owner()->exists())
            {
                $owner_wallet = $request_detail->driverDetail->owner->ownerWalletDetail;
                $owner_wallet->amount_added += $driver_commision;
                $owner_wallet->amount_balance += $driver_commision;
                $owner_wallet->save();
    
                $owner_wallet_history = $request_detail->driverDetail->owner->ownerWalletHistoryDetail()->create([
                    'amount'=>$driver_commision,
                    'transaction_id'=>str_random(6),
                    'remarks'=>WalletRemarks::TRIP_COMMISSION_FOR_DRIVER,
                    'is_credit'=>true
                ]);
            }else{
                $driver_wallet = $request_detail->driverDetail->driverWallet;
                $driver_wallet->amount_added += $driver_commision;
                $driver_wallet->amount_balance += $driver_commision;
                $driver_wallet->save();
    
                $driver_wallet_history = $request_detail->driverDetail->driverWalletHistory()->create([
                    'amount'=>$driver_commision,
                    'transaction_id'=>str_random(6),
                    'remarks'=>WalletRemarks::TRIP_COMMISSION_FOR_DRIVER,
                    'is_credit'=>true
                ]);
            }
        }
        return true;
    }

    
}
