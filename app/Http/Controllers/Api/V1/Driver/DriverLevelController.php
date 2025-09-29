<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Driver;
use App\Base\Constants\Auth\Role;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Models\Request\Request as RequestRequest;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use Kreait\Firebase\Contract\Database;
use App\Models\Admin\DriverLevelDetail;
use App\Models\Admin\DriverLevelUp;
use App\Transformers\Driver\LevelTransformer;
use App\Transformers\Payment\RewardHistoryTransformer;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ZoneType;
use Carbon\Carbon;
/**
 * @group Loyalty And Reward History
 * @authenticated
 *
 * APIs for Listing Loyalty and Reward history
 */
class DriverLevelController extends Controller
{
    protected $request;

    protected $database;


    public function __construct(Request $request,Database $database)
    {
        $this->request = $request;
        $this->database = $database;
        
    }

    /**
     * List Driver levels and targets for the next level
     *
     * **Authorization**: Requires a Bearer token in the Authorization header.
     *
     * **Header Example**:
     * ```
     * Authorization: Bearer <your_token_here>
     * ```
     * 
     * @authenticated
     * 
     * @group Loyalty And Reward History
     * @return \Illuminate\Http\JsonResponse
     * 
     * @response 
     * {
     *      "success": true,
     *      "message": "levels-listed",
     *      "data": [
     *          {
     *              "id": "0300b763-673a-420b-b740-04e0e8ed1624",
     *              "level": 1,
     *              "min_ride_count": 2,
     *              "min_ride_amount": 10,
     *              "is_min_ride": 1,
     *              "is_min_earning": 1,
     *              "level_icon": "http://localhost/new_vue_tagxi/public/storage/uploads/driver/levels/1KFhqrwdf6NByKYUHbVHBKMF5WSJSEheIp2v6Qn5.jpg",
     *              "created_at": "2024-10-28 04:41 PM",
     *              "levelDetails": {
     *                  "data": {
     *                      "id": "38dfeae2-a0ef-4081-b28c-e81cb953ae0a",
     *                      "level": 1,
     *                      "level_id": "0300b763-673a-420b-b740-04e0e8ed1624",
     *                      "is_min_ride_completed": 1,
     *                      "is_min_earning_completed": 1,
     *                      "level_icon": "http://localhost/new_vue_tagxi/public/storage/uploads/driver/levels/1KFhqrwdf6NByKYUHbVHBKMF5WSJSEheIp2v6Qn5.jpg",
     *                      "created_at": "2024-11-14 02:28 PM"
     *                  }
     *              }
     *          }
     *      ],
     *      "meta": {
     *          "pagination": {
     *              "total": 1,
     *              "count": 1,
     *              "per_page": 15,
     *              "current_page": 1,
     *              "total_pages": 1,
     *              "links": {}
     *          }
     *      }
     *  }
    */
    public function listLevel() {
        $levels = [];
        $driver = auth()->user()->driver;
        $type = $driver->driverVehicleTypeDetail()->where('signed_vehicle',true)->first();

        $lastRide = $driver->requestDetail()->orderBy('created_at','DESC')->whereHas('zoneType', function($typeQuery) use ($type) {
            $typeQuery->where('type_id',$type->vehicle_type);
        })->first();

        if ($lastRide) 
        {

            $zoneType = $lastRide->zoneType()->where('type_id',$type->vehicle_type)->first();

            if(!$zoneType){
                $levels = [];
            }else{
                $levels = DriverLevelUp::where('zone_type_id',$zoneType->id)->orderBy('level')->get();
            }

        }
        $result = fractal($levels, new LevelTransformer);
        return $this->respondSuccess($result, 'levels-listed');
    }
    /**
     * List User reward History
     *
     * Retrieve the reward history of a user or driver based on their role.
     *
     * **Authorization**: Requires a Bearer token in the Authorization header.
     *
     * **Header Example**:
     * ```
     * Authorization: Bearer <your_token_here>
     * ```
     * 
     * @authenticated
     * 
     * @group Loyalty And Reward History
     * @return \Illuminate\Http\JsonResponse

     * @response 
     * {
     *      "success": true,
     *      "message": "rewards-listed",
     *      "data": [
     *          {
     *              "id": "8f89240a-98e5-410c-80c8-8ddbb912ba03",
     *              "request_id": null,
     *              "is_credit": true,
     *              "amount": 0,
     *              "reward_points": 15,
     *              "remarks": "Driver Level Up Bonus Reward",
     *              "created_at": "14th Nov 02:28 PM"
     *          },
     *      ],
     *      "meta": {
     *          "pagination": {
     *              "total": 9,
     *              "count": 9,
     *              "per_page": 15,
     *              "current_page": 1,
     *              "total_pages": 1,
     *              "links": {}
     *          }
     *      }
     *  }
    */
    public function listRewards() {
        if (auth()->user()->hasRole(Role::USER)) {
            $history = auth()->user()->rewardHistory()->orderBy('created_at','desc')->paginate();
        } elseif (auth()->user()->hasRole(Role::DRIVER)) {
            $history = auth()->user()->driver->loyaltyHistory()->orderBy('created_at','desc')->paginate();
        }
        $result = fractal($history, new RewardHistoryTransformer);
        return $this->respondSuccess($result, 'rewards-listed');
    }

}
