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
use App\Models\Admin\ServiceLocation;
use App\Models\User;
use App\Models\Request\FavouriteLocation;
use App\Models\Payment\UserBankInfo;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Facades\Log;
use App\Models\Chat;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Owner\OwnerProfileTransformer;

/**
 * @group Profile-Management
 *
 * APIs for Profile-Management
 */
class ProfileController extends ApiController
{
    /**
     * ImageUploader instance.
     *
     * @var ImageUploaderContract
     */
    protected $imageUploader;

    protected $user;

    protected $database;



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
        Log::info("profile",$request->all());
        $data = $request->only(['name', 'email', 'last_name','mobile','gender']);



        if($request->has('fcm_token'))
        {
            $data['fcm_token'] = $request->fcm_token;
        }

        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $data['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }
        if($request->has('map_type')){
            $data['map_type'] = $request->map_type;
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
     * @param UpdateDriverProfileRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
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
    
        $driver_params = array_filter(
            $request->only(['car_make', 'car_model', 'car_color', 'car_number', 'name', 'email', 'vehicle_year', 'custom_make', 'custom_model','transport_type']),
            function ($value) {
                return !is_null($value); // Keep only non-null values
            }
        );


        if (!$owner) {
            $driver_params['reason'] = null;
        }
    
        // Handle profile picture upload
        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $user_params['profile_picture'] = $this->imageUploader->file($uploadedFile)->saveProfilePicture();
            if (!$owner) {
                $driver_params['reason'] = null;
            }
        }
    
        // Check gender and update if present
        if ($request->has('gender')) {
            $user_params['gender'] = $request->gender;
            $driver_params['gender'] = $request->gender;
        }
        if($request->has('fcm_token'))
        {
            $user_params['fcm_token'] = $request->fcm_token;
        }
    
        if($request->has('map_type')){
            $user_params['map_type'] = $request->map_type;
        }
        // Update user details
        $user->update($user_params);
    
        // Handle demo mode approval
        // if (env('APP_FOR') == 'demo') {
        //     $driver_params['approve'] = true;
        // }
    
        // Handle vehicle type updates for drivers
        $driver_details = $user->driver;

        if ($request->has('vehicle_type')) {
            // Delete old vehicle types and add new ones
            $driver_details->driverVehicleTypeDetail()->delete();

            $driver_details->driverVehicleTypeDetail()->create(['vehicle_type' => $request->vehicle_type]);
           $driver_details->update(['driver_level_up_id'=>null]);
        
        }
        if ($request->has('sub_vehicle_type')) {  /// [vt1,vt2]

            $sub_vehicle_type_ids = array_unique($request->input('sub_vehicle_type'));

            $driver_details->driverVehicleTypeDetail()->where('signed_vehicle', false)->delete();
            
            $signedVehicleIds = $driver_details->driverVehicleTypeDetail()
                ->where('signed_vehicle', true)
                ->pluck('vehicle_type') // Assuming 'vehicle_type' holds the ID
                ->toArray();
            
            // Remove signed vehicle IDs from $sub_vehicle_type_ids
            $filtered_vehicle_type_ids = array_diff($sub_vehicle_type_ids, $signedVehicleIds);
           
            $signed_vehicle = false;
            
            foreach($filtered_vehicle_type_ids as $sub_vehicle_type_id) {
                $driver_details->driverVehicleTypeDetail()->create([
                    'vehicle_type' => $sub_vehicle_type_id,
                    'signed_vehicle' =>$signed_vehicle, 
                ]);
            }
        }
    
        if($request->has('service_location_id'))
        {
            $driver_params['service_location_id'] = $request->service_location_id;

            $timezone = ServiceLocation::where('id', $request->input('service_location_id'))->pluck('timezone')->first();

            $user->update(['timezone'=>$timezone]);


        }
        // dd($driver_params);
        // Update driver or owner details
        if (!$owner) {

            if (get_settings('enable_driver_profile_disapprove_on_update') == 1 && !$request->has('sub_vehicle_type')) {
                $driver_params['approve'] = false;
                $this->database->getReference('drivers/driver_' . $driver_details->id)
                ->update(['approve' => false, 'is_active' => true, 'available'=>false, 'updated_at' => Database::SERVER_TIMESTAMP]);
            }

            $driver_details->update($driver_params);
        } else {

            $request->validate([
                'company_name' => [
                    'sometimes',
                    'required',
                    Rule::unique('owners', 'company_name')
                        ->whereNull('deleted_at'),
                ],
                'name' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|required|string|min:10|max:500',
                'postal_code' => 'sometimes|required|numeric|digits_between:4,8',
                'city' => 'sometimes|required|string|max:255',
                'service_location_id' => 'sometimes|required|exists:service_locations,id',
                'tax_number' => 'sometimes|required|alpha_num|max:20',
                'no_of_vehicles' => 'nullable|integer|min:0',
            ]);


            $owner_params = $request->only(['name','service_location_id','company_name','address','postal_code','city','tax_number','no_of_vehicles','email','transport_type']);
            if($request->name){
                $owner_params['owner_name'] = $request->name;
            }

            $user->owner()->update($owner_params);
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
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 
     * {
     *     "success": true,
     *     "message": "address listed successfully",
     *     "data": [
     *          "pick_lat": 37.774929, 
     *          "pick_lng": -122.419416, 
     *          "drop_lat": 34.052235, 
     *          "drop_lng": -118.243683, 
     *          "pick_address": "1 Market St, San Francisco, CA 94105, USA", 
     *          "drop_address": "123 Main St, Los Angeles, CA 90015, USA", 
     *          "address_name": "Home to Office"
     *     ]
     * }
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
     * @response
     * {
     *"success": true,
     *"message": "bank info updated successfully"
     *}
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
     * @return \Illuminate\Http\JsonResponse
     * {
     *     "success": true,
     *     "message": "bank info listed successfully",
     *     "data": [
     *         'account_name' => 'John Doe',
     *         'account_no' => '1234567890',
     *         'bank_code' => '001',
     *         'bank_name' => 'First National Bank'
     *     ]
     * }
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
     * @return \Illuminate\Http\JsonResponse
     * 
     * */
    public function userDeleteAccount()
    {



        $user = auth()->user();
        // dd($user);

        $today = Carbon::now();

        try {
            if ($user->is_deleted_at === null) {
                $user->update(['is_deleted_at' => $today]);
                $user->update(['active'=>false]);
                if($user->hasRole('driver')){
                    $user->driver()->update(['active'=>false,'available'=>false]);
                }
            } else {
                $this->throwCustomException('Your Account Delete operation is Processing');
            }
        } catch (\Exception $e) {
            Log::error('Error updating user:', ['error' => $e->getMessage()]);
            // Handle the exception as needed
        }
        

        return response()->json(['success' => true, 'user' => $user]);


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


        Log::info("location", $request->all());
        $zone = find_zone($request->current_lat,$request->current_lng);
        // Log::info(auth()->user()->id);
        
        if($zone){
            auth()->user()->update(['zone_id'=>$zone->id,'current_lat'=>$request->current_lat, 'current_lng' =>$request->current_lng]);

        }


        return $this->respondSuccess();
    }
}
