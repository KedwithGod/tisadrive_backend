<?php

namespace App\Http\Controllers\Api\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\User\UserTransformer;
use App\Transformers\Driver\DriverTransformer;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\UpdateDriverProfileRequest;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Constants\Auth\Role;
use App\Models\User;
use App\Models\Request\FavouriteLocation;
use App\Models\Payment\UserBankInfo;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Log;
use App\Models\Chat;
use Carbon\Carbon;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Owner\OwnerProfileTransformer;

/**
 * @group Profile-Management
 *
 * APIs for Profile-Management
 */
class ProfileBackupController extends ApiController
{
    /**
     * ImageUploader instance.
     *
     * @var ImageUploaderContract
     */
    protected $imageUploader;

    protected $user;


    /**
     * ProfileController constructor.
     *
     * @param ImageUploaderContract $imageUploader
     */
    public function __construct(ImageUploaderContract $imageUploader,User $user,Database $database)
    {
        $this->imageUploader = $imageUploader;

        $this->user = $user;

        $this->database = $database;

    }

    /**
     * Update user profile.
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $request->only(['name', 'email', 'last_name','mobile']);

        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $data['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }
        $user = auth()->user();

        $mobile = $request->mobile;

        if($mobile){
             $validate_exists_mobile = $this->user->belongsTorole(Role::USER)->where('mobile', $mobile)->where('id','!=',$user->id)->exists();

        if ($validate_exists_mobile) {
            $this->throwCustomException('Provided mobile has already been taken');
        }

        }


        $user->update($data);
        $user = fractal($user->fresh(), new UserTransformer);

        return $this->respondSuccess($user);
    }

    /**
    * Update Driver Profile
    *
    */
    public function updateDriverProfile(UpdateDriverProfileRequest $request)
    {
        // Log the entire request for debugging
        // Log::info("profile_update_params");
        // Log::info($request->all());
    
        // Extract user-related parameters
        $user_params = $request->only(['name', 'email', 'last_name', 'mobile', 'gender']);
        $user = auth()->user(); // Get the authenticated user
        $owner = $user->owner()->exists(); // Determine if the user is an owner
        $mobile = $request->mobile;
        $email = $request->email;
    
        // Owner and Driver-specific logic
        if (!$owner) {
            // Handle validation for drivers (checking mobile and email)
            if ($mobile) {
                $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->where('id', '!=', $user->id)->exists();
                if ($validate_exists_mobile) {
                    $this->throwCustomException('Provided mobile has already been taken');
                }
            }
            if ($email) {
                $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $email)->where('id', '!=', $user->id)->exists();
                if ($validate_exists_email) {
                    $this->throwCustomException('Provided email has already been taken');
                }
            }
        } else {
            // Handle validation for owners (checking mobile and email)
            $validate_exists_mobile = $this->user->belongsTorole(Role::OWNER)->where('mobile', $mobile)->where('id', '!=', $user->id)->exists();
            if ($validate_exists_mobile) {
                $this->throwCustomException('Provided mobile has already been taken');
            }
            if ($email) {
                $validate_exists_email = $this->user->belongsTorole(Role::OWNER)->where('email', $email)->where('id', '!=', $user->id)->exists();
                if ($validate_exists_email) {
                    $this->throwCustomException('Provided email has already been taken');
                }
            }
        }
    
        // Update driver-specific parameters
        // $driver_params = $request->only(['vehicle_type', 'car_make', 'car_model', 'car_color', 'car_number', 'name', 'email', 'vehicle_year', 'custom_make', 'custom_model']);
        $driver_params = array_filter(
            $request->only(['vehicle_type', 'car_make', 'car_model', 'car_color', 'car_number', 'name', 'email', 'vehicle_year', 'custom_make', 'custom_model']),
            function ($value) {
                return !is_null($value); // Keep only non-null values
            }
        );



        $driver_params['approve'] = false;
        if (env('APP_FOR')=='demo') {
            $driver_params['approve'] = true;
        }
        if (!$owner) {
            $driver_params['reason'] = null;
            $driver_params['gender'] = $request->gender;
        }
    
        // Handle profile picture upload
        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $user_params['profile_picture'] = $this->imageUploader->file($uploadedFile)->saveProfilePicture();
            $driver_params['approve'] = false; // Mark as unapproved if picture is uploaded
            if (!$owner) {
                $driver_params['reason'] = null;
            }
        }
    
        // Check gender and update if present
        if ($request->has('gender')) {
            $user_params['gender'] = $request->gender;
        }
    
        // Log the parameters
        // Log::info("driver_params");
        // Log::info($driver_params);
        // Log::info("driver_params");
        // Log::info($user_params);
    
        // Update user details
        $user->update($user_params);
    
        // Handle demo mode approval
        if (env('APP_FOR') == 'demo') {
            $driver_params['approve'] = true;
        }
    
        // Handle vehicle type updates for drivers
        $driver_details = $user->driver;
        if ($request->has('vehicle_type')) {
            // Delete old vehicle types and add new ones
            $driver_details->driverVehicleTypeDetail()->delete();

            $driver_details->driverVehicleTypeDetail()->create(['vehicle_type' => $request->vehicle_type]);
        
            
        }
    
        // Update Firebase approval status if necessary
        if ($driver_params['approve'] == false && !$owner) {
            $this->database->getReference('drivers/driver_' . $user->driver->id)->update(['approve' => 0, 'updated_at' => Database::SERVER_TIMESTAMP]);
        }
        if($request->has('service_location_id'))
        {
            $driver_params['service_location_id'] = $request->service_location_id;

        }
        // dd($driver_params);
        // Update driver or owner details
        if (!$owner) {
            $user->driver()->update($driver_params);
        } else {
            $driver_params['owner_name'] = $request->name;
            $driver_params['name'] = $request->name;
            $driver_params['company_name'] = $request->company_name;
            $driver_params['address'] = $request->address;
            $driver_params['postal_code'] = $request->postal_code;
            $driver_params['city'] = $request->city;
            $driver_params['no_of_vehicles'] = $request->no_of_vehicles ?? 0;
            $driver_params['tax_number'] = $request->tax_number;

            $user->owner()->update($driver_params);
        }
    
        if (!$owner) {
            $result = fractal($driver_details, new DriverProfileTransformer);
        } else {
            $result = fractal($user->owner, new OwnerProfileTransformer);
      
        }

        // Return updated driver details with relationships
     
        return $this->respondSuccess($result);
    }
    
    /**
     * Update user password.
     * @bodyParam old_password password required old_password provided user
     * @bodyParam password password required password provided user
     * @bodyParam password_confirmation password required  confirmed password provided user
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
     */
    public function updatePassword(ChangePasswordRequest $request)
    {
        $oldPassword = $request->input('old_password');
        $password = $request->input('password');

        $user = auth()->user();

        if (!hash_check($oldPassword, $user->password)) {
            $this->throwCustomValidationException('Invalid old password entered.', 'old_password');
        }

        $user->forceFill(['password' => bcrypt($password)])->save();

        return $this->respondSuccess();
    }

    /**
    * Update My Language
    * @bodyParam lang string required language provided user
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
    */
    public function updateMyLanguage(Request $request)
    {

        // Log::info("Lang update");
        // Log::info($request->all());

        // Validate Request id
        $request->validate([
        'lang' => 'required',
        ]);
        $user = auth()->user();
        $user->forceFill(['lang' => $request->lang])->save();
        return $this->respondSuccess();
    }

    /**
    * Add favourite location
    * @bodyParam pick_lat double required pikup lat of the user
    * @bodyParam pick_lng double required pikup lng of the user
    * @bodyParam drop_lat double optional drop lat of the user
    * @bodyParam drop_lng double optional drop lng of the user
    * @bodyParam pick_address string required pickup address of the favourite location
    * @bodyParam drop_address string optional drop address of the favourite location
    * @bodyParam address_name string required address name of the favourite location
    * @bodyParam landmark string optional drop address of the favourite location
    * @response
     * {
     *"success": true,
     *"message": "address added successfully"
     *}
    */
    public function addFavouriteLocation(Request $request){

        // Validate Request id
        $request->validate([
            'pick_lat'  => 'required',
            'pick_lng'  => 'required',
            'pick_address'=>'required',
            'drop_lat'  =>'sometimes|required',
            'drop_lng'  =>'sometimes|required',
            'drop_address'=>'sometimes|required',
            'address_name'=>'required',
        ]);

        $created_params = $request->all();

        $created_params['user_id'] = auth()->user()->id;

        $locations = FavouriteLocation::where('user_id',auth()->user()->id)->get()->count();

        if($locations==4){
            $this->throwCustomException('You have reached your limits');
        }
        FavouriteLocation::create($created_params);

        return $this->respondSuccess(null,'address added successfully');



    }

    /**
    * List Favourite Locations
    *
    */
    public function FavouriteLocationList()
    {
        $user = auth()->user();

        $locations = FavouriteLocation::where('user_id',$user->id)->get();

        return $this->respondSuccess($locations,'address listed successfully');

    }

    /**
     * Delete Favourite Location
     *
     * @response
     * {
     * "success": true,
     * "message": "favorite location deleted successfully"
     * }
     * */
    public function deleteFavouriteLocation(FavouriteLocation $favourite_location){

        $favourite_location->delete();

        return $this->respondSuccess(null,'favorite location deleted successfully');


    }

    /**
     * Add/Update Bank Info
     * @bodyParam account_name string required name of the account
     * @bodyParam account_no integer required Number of the account
     * @bodyParam bank_code string required Bank code of the account
     * @bodyParam bank_name string required Bank name of the account
     * 
     * 
     * */
    public function updateBankinfo(Request $request)
    {
        $user = auth()->user();

        $bankInfo = $user->bankInfo;

        if($bankInfo){

           $user->bankInfo()->update($request->all());

        }else{
            $bankInfo = $user->bankInfo()->create($request->all());

        }

        return $this->respondSuccess(null,'bank info updated successfully');

    }

    /**
     * Get Bank info
     * 
     * 
     * */
    public function getBankInfo()
    {
        $user = auth()->user();

        $bankInfo = $user->bankInfo;

        return response()->json(['success'=>true,'message'=>'bank info listed successfully','data'=>$bankInfo]);


    }
     /**
     * user Account Delete
     * 
     * 
     * */
    public function userDeleteAccount()
    {

        // $user_id = auth()->user()->id;

        // $chat_exists = Chat::join("chat_messages", "chat.id", "chat_messages.chat_id")->where('user_id', $user_id)->delete();

        // $user = auth()->user()->delete();

        $user = auth()->user();

        $today = date('Y-m-d');
        if(!($user->hasRole('owner')))
        {
            if ($user->is_deleted_at!=null)
            {
                $this->throwCustomException('Your Account Delete operation is Processing');

            }else{
          
                $user->update(['is_deleted_at'=> $today]);

            }    
        }else{
            auth()->user()->delete();
        }

        return response()->json(['success'=>true,'message'=>'User Account deleted successfully']);

    }

    /**
    * Update My Location
    * @bodyParam lang string required location provided user
     * @return \Illuminate\Http\JsonResponse
     * @response
     * {
     *"success": true,
     *"message": "success"
     *}
    */
    public function updateLocation(Request $request)
    {
        $request->validate([
        'current_lat' => 'required',
        'current_lng' => 'required',
        ]);
        
        auth()->user()->update(['current_lat'=>$request->current_lat, 'current_lng'=>$request->current_lng]);


        return $this->respondSuccess();
    }
}
