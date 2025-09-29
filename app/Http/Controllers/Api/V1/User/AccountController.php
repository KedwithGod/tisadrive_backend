<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\User;
use App\Models\Admin\Driver;
use App\Models\Admin\Owner;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\ApiController;
use App\Transformers\User\UserTransformer;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Owner\OwnerProfileTransformer;
use App\Models\Chat;
use App\Models\Request\Request;
use App\Models\Request\RequestBill;
use App\Models\Admin\DriverLevelUp;
use App\Base\Constants\Masters\WalletRemarks;
use Carbon\Carbon;
use Log;
use App\Models\Admin\DriverNeededDocument;
use App\Models\Admin\OwnerNeededDocument;


class AccountController extends ApiController
{
    /**
     * Get the current logged in user.
     * @group User-Management
     * @return \Illuminate\Http\JsonResponse
    * @responseFile responses/auth/authenticated_driver.json
    * @responseFile responses/auth/authenticated_user.json
     */
    public function me()
    {

        $user = auth()->user();

        if (auth()->user()->hasRole(Role::DRIVER)) {

            $driver_details = $user->driver;
            $includes = ['onTripRequest.userDetail','onTripRequest.requestBill','metaRequest.userDetail','driverVehicleType'];
            if(get_settings('driver_register_module') !== 'commission' ) {
                $includes [] = 'subscription';
            }
            if(get_settings('show_driver_level_feature')) {
                $includes [] = 'level';
            }

            // $request =  $driver_details->requestDetail()->where(function($query){
            //     $query->where('is_cancelled', false)->where('driver_rated', false)
            //         ->where(function($subQuery){
            //             $subQuery->where('is_driver_started', true)
            //                     ->orwhere(function($deliveryQuery){
            //                         $deliveryQuery->where('transport_type','delivery')->where('is_driver_arrived',true);
            //                     });
            //         });
            // })->first();

            // if(!$request) {
            // $driver_details->update(['approve' => false]);
                
            // }


            $user = fractal($driver_details, new DriverProfileTransformer)->parseIncludes($includes);

        } else if(auth()->user()->hasRole(Role::USER)) {

            // if(!$user->zone_id){

                $zone = find_zone($user->current_lat,$user->current_lng);

                if(!$user->service_location_id && $zone){

                    $service_location_id = $zone->serviceLocation->id;

                    $user->update(['service_location_id'=>$service_location_id]);
                    

                   }

            // }

            

            $user = fractal($user, new UserTransformer)->parseIncludes(['onTripRequest.driverDetail','onTripRequest.requestBill','metaRequest.driverDetail','favouriteLocations','laterMetaRequest.driverDetail']);
        }else{

            $owner_details = $user->owner;

            // $owner_details = $this->accountValidation($user);

            $user = fractal($owner_details, new OwnerProfileTransformer);
        }

        if(auth()->user()->hasRole(Role::DISPATCHER)){

            $user = User::where('id',auth()->user()->id)->first();
            $user->chat_id = "";
            $get_chat_data = Chat::where('user_id',$user->id)->first();
            if($get_chat_data)
            {
                $user->chat_id = $get_chat_data->id;
            }  
        }
      
        return $this->respondOk($user);
    }

    public function accountValidation($user) {
        $profile = $user;
        if($user->hasRole(Role::DRIVER)) {
            $driver_details = $user->driver;
            if(!$driver_details->owner_id) {
                $neededDoc = DriverNeededDocument::active()->where(function($query) {
                    $query->where('account_type','individual')->orWhere('account_type','both');
                })->count();
                $uploadedDoc = $driver_details->driverDocument()->count();
            }else{
                $neededDoc = DriverNeededDocument::active()->where(function($query) {
                    $query->where('account_type','fleet_driver')->orWhere('account_type','both');
                })->count();
                $uploadedDoc = $driver_details->driverDocument()->count();
            }

            if( $neededDoc > $uploadedDoc){
                Driver::where('id',$driver_details->id)->update([
                    'approve' => 0,
                    'active' => 0
                ]);
            }
            $profile = $user->driver;
        }else {

            $owner_details = $user->owner;

            $neededDoc = OwnerNeededDocument::active()->count();
            $uploadedDoc = $owner_details->ownerDocument()->count();

            if( $neededDoc > $uploadedDoc){
                Owner::where('id',$driver_id)->update([
                    'approve' => 0,
                    'active' => 0
                ]);
            }

            $profile = $user->owner;

        }
        return $profile;
    }
}
