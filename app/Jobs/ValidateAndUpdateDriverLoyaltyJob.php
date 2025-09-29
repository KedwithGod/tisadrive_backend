<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Admin\Driver;
use App\Models\Payment\DriverWallet;
use App\Models\Admin\DriverLevelUp;
use App\Models\Request\RequestBill;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Request\Request as RequestRequest;
use Illuminate\Support\Facades\Log;


class ValidateAndUpdateDriverLoyaltyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    protected $request_detail;

    /**
     * Create a new job instance.
     */
    public function __construct($request_detail)
    {
        $this->request_detail = $request_detail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $driver = Driver::find($this->request_detail->driver_id);
        $driver_user = $driver->user;

        $driver_vehicle_type = $driver->driverVehicleTypeDetail()->where('signed_vehicle',true)->first()->vehicle_type;
        $trip_vehicle_type = $this->request_detail->zoneType->type_id;

        if (!$driver_user || $trip_vehicle_type !== $driver_vehicle_type) {
            goto end;
        }

        if($driver->levelDetail()->exists() && $driver->levelDetail->levelDetail()->where('zone_type_id',$this->request_detail->zone_type_id)->exists()){
            $driver_level = $driver->levelDetail;
        }else{
            Driver::where('id',$driver->id)->update(['driver_level_up_id'=>null]);
            $driver = Driver::find($this->request_detail->driver_id);
            $driver_level = null;
        }
        $driver_level = $driver_level ?? $this->initializeDriverLevel($driver,$this->request_detail->zone_type_id);

        if ($driver_level) {
            $calculatedRewards = $this->fetchReward($driver_level, $driver,$this->request_detail);

            if ($driver_level->amount_rewarded && $driver_level->ride_rewarded) {
                $this->promoteDriverToNextLevel($driver, $driver_level, $this->request_detail);
            }
        }
        end:
    }
    
    /**
     * Calculate and allocate Rewards
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function fetchReward($driver_level,$driver,$request_detail) {

        $target_level = $driver_level->levelDetail;
        $bonus_ride_rewards = 0;
        $bonus_amount_rewards = 0;
        if(!$driver_level->ride_rewarded){
            if($target_level->is_min_ride_complete){
                $driver_completed_rides = RequestRequest::where('driver_id', $driver->id)->where('zone_type_id',$this->request_detail->zone_type_id)->where('is_completed', true)->count();
                if($target_level->min_ride_count <= $driver_completed_rides){
                    $bonus_ride_rewards = $target_level->ride_points;
                    $driver_level->ride_rewarded = true;
                    $driver_level->save();
                    $this->rewardDriver($target_level->ride_points,$driver,WalletRemarks::DRIVER_LEVEL_UP_BONUS,$this->request_detail->id);
                }
            }else{
                $driver_level->ride_rewarded = true;
                $driver_level->save();
            }
        }
        if(!$driver_level->amount_rewarded){
            if($target_level->is_min_ride_amount_complete){
                $driverSpentAmount = RequestBill::whereHas('requestDetail', function ($query) use ($driver) {
                    $query->where('driver_id', $driver->id)->where('zone_type_id',$this->request_detail->zone_type_id)
                          ->where('is_completed', 1);
                })->sum('driver_commision');
                if($target_level->min_ride_amount <= $driverSpentAmount){
                    $bonus_amount_rewards = $target_level->rideamount_points_points;
                    $driver_level->amount_rewarded = true;
                    $driver_level->save();
                    $this->rewardDriver($target_level->amount_points,$driver,WalletRemarks::DRIVER_LEVEL_UP_BONUS,$this->request_detail->id);
                }
            }else{
                $driver_level->amount_rewarded = true;
                $driver_level->save();
            }
        }
        return [
            'rewards'=>$bonus_amount_rewards + $bonus_ride_rewards,
            'bonus_ride_rewards'=>$bonus_ride_rewards,
            'bonus_amount_rewards'=>$bonus_amount_rewards,
        ];
    }
    /**
     * Reward Point rewards
     * 
     */
    public function rewardDriver($reward,$driver,$remarks,$request_id) {
        $driver_reward = $driver->loyaltyPoint;
        $driver_reward->points_added += $reward;
        $driver_reward->balance_reward_points += $reward;
        $driver_reward->save();

        // Add the history
        $driver->loyaltyHistory()->create([
            'reward_points'=>$reward,
            'request_id'=>$request_id,
            'remarks'=>$remarks,
            'is_credit'=>true,
        ]);
    }
    /**
     * Wallet Rewards
     * 
     */
    public function creditDriver($reward,$driver,$remarks,$credit = false) {
        $driver_wallet = DriverWallet::where('user_id', $driver->id)->first();
        
        $driver_wallet->amount_added += $reward;
        $driver_wallet->amount_balance += $reward;
        $driver_wallet->save();

        // Add the history
        $driver->driverWalletHistory()->create([
            'amount'=>$reward,
            'transaction_id'=>str_random(6),
            'remarks'=>$remarks,
            'is_credit'=>$credit,
        ]);
    }

    
    /**
     * Level Calculation
     * @return \App\Models\Admin\DriverLevelUp
     * 
     */
    private function initializeDriverLevel($driver,$zone_type_id) {
        $first_level = DriverLevelUp::where('zone_type_id',$zone_type_id)->orderBy('level', 'asc')->first();

        if(!$first_level) {
            return null;
        }
        $driver_level = $driver->levelHistory()->create(['level' => $first_level->level, 'level_id' => $first_level->id]);
        $driver->driver_level_up_id = $driver_level->id;
        $driver->save();

        return $driver->levelDetail;
    }
    
    /**
     * Level promotion
     * 
     */
    private function promoteDriverToNextLevel($driver, $driver_level, $request_detail) {
        $next_level = DriverLevelUp::where('zone_type_id',$this->request_detail->zone_type_id)->where('level', $driver_level->level + 1)->first();
    
        if ($next_level) {
            $next_driver_level = $driver->levelHistory()->create(['level' => $next_level->level, 'level_id' => $next_level->id]);
            $driver->driver_level_up_id = $next_driver_level->id;
            $driver->save();
            if ($next_level->reward_type == 'reward-cash') {
                $this->creditDriver($next_level->reward, $driver, WalletRemarks::DRIVER_LEVEL_UP, true);
            } elseif ($next_level->reward_type == 'reward-point') {
                $this->rewardDriver($next_level->reward, $driver, WalletRemarks::DRIVER_LEVEL_UP, $this->request_detail->id);
            }
        }
    }
}
