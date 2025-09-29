<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Subscription;
use App\Models\Admin\Driver;
use App\Models\Admin\SubscriptionDetail;
use App\Models\Admin\Zone;
use App\Models\Admin\VehicleType;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Models\Request\Request as RequestRequest;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use Kreait\Firebase\Contract\Database;
use App\Transformers\Driver\SubscriptionTransformer;
use Carbon\Carbon;
use Log;

/**
 * @group Subscription
 * @authenticated
 * APIs for Manage Subscription
 */
class SubscriptionController extends Controller
{

    /**
     * 
     * Driver Subscription List
     * 
     * 
     * @params plan_id
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "subscriptions-listed",
     *     "data": [
     *          {
     *              "id": 3,
     *              "name": "Harmonious Hatchback",
     *              "description": "Subscribe and Save Drive without limit",
     *              "duration": 60,
     *              "currency_symbol": "₹",
     *              "amount": 10,
     *              "vehicle_type_id": "e42b124a-d309-4484-8dcd-f2b6cbc6b9f7",
     *              "vehicle_type_name": "Hatchback"
     *          }
     *      ]
     * }
     * 
     * */
    public function listOfSubscription() {
        $vehicle_types = auth()->user()->driver->driverVehicleTypeDetail()->where('signed_vehicle',true)->pluck('vehicle_type');
        $plans = Subscription::active()->whereIn('vehicle_type_id',$vehicle_types)->get();
        $result = fractal($plans, new SubscriptionTransformer);
        return $this->respondSuccess($result, 'subscriptions-listed');
    }

    /**
     * 
     * Driver Subscription
     * 
     * 
     * @params plan_id
     * @params payment_opt
     * @params transaction_id(for card)
     * @params amount
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "subscribed_successfully",
     * }
     * 
     * */

    public function addSubscription(){
        $driver = auth()->user()->driver;
        if($driver->is_subscribed){
            $this->throwCustomException('Driver already subscribed');
        }

        if(request()->payment_opt != 2){
            $this->throwAuthorizationException();
        }
        $plan_id = request()->plan_id;

        $vehicle_types = $driver->driverVehicleTypeDetail()->where('signed_vehicle',true)->pluck('vehicle_type');

        $plan = Subscription::active()->where('id',$plan_id)->whereIn('vehicle_type_id',$vehicle_types)->first();

        if(!$plan){
             $this->throwCustomException('Subscription is not Valid or Incorrect');
        }

        $amount = floatval($plan->amount);

        $expire_at = Carbon::parse(now())->addDay($plan->subscription_duration)->toDateString();
        $params = [
            'driver_id' => $driver->id,
            'subscription_id' => $plan_id,
            'amount' => $amount,
            'payment_opt' => request()->payment_opt,
            'expired_at' => $expire_at,
        ];
        $params['transaction_id'] = str_random(6);
        $driver_wallet = $driver->DriverWallet;
        if($amount > $driver_wallet->amount_balance){
            $this->throwCustomException('Insufficient Wallet Balance');
        }
        $driver_wallet->amount_balance -= $amount;
        $driver_wallet->amount_spent += $amount;
        $driver_wallet->save();
        $driver->driverWalletHistory()->create([
            'amount'=>$amount,
            'transaction_id'=>str_random(6),
            'remarks'=>WalletRemarks::SUBSCRIPTION_FEE,
            'is_credit'=>false,
        ]);
        $params['subscription_type'] = 1;
        $subscription = SubscriptionDetail::create($params);
        Driver::where('id',$driver->id)->update([
            'is_subscribed' => true,
            'subscription_detail_id' => $subscription->id,
        ]);

        $data = [
            'is_subscribed' => 1,
            'driver_id' => $driver->id,
        ];
        return $this->respondSuccess($data, 'subscribed_successfully');
    }
}
