<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Models\Admin\Driver;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Admin\Fleet;
use Illuminate\Http\Request;
use App\Transformers\Driver\DriverTransformer;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use Kreait\Firebase\Contract\Database;
use App\Models\User;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


/**
 * @group Fleet-Owner-apis
 * @authenticated
 */
class FleetDriversController extends BaseController
{
    protected $user;
    protected $driver;
    protected $fleet;
    protected $imageUploader;
    protected $database;


    public function __construct(Driver $driver,Fleet $fleet,Database $database,ImageUploaderContract $imageUploader,User $user)
    {
        $this->driver = $driver;

        $this->fleet = $fleet;

        $this->database = $database;
        
        $this->imageUploader = $imageUploader;

        $this->user = $user;
    }


    /**
     * Add Driver
     * @bodyParam name string required name of the driver
     * @bodyParam email string required email of the driver
     * @bodyParam mobile string required mobile of the driver
     * @bodyParam address string required address of the driver
     * @bodyParam profile string required profile pic of the driver
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "driver_added_succesfully",
     * }
     * 
     * */
    public function addDriver(Request $request){

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'mobile'=>'required',
            'address'=>'required|min:10',
        ]);

        $owner_detail = auth()->user()->owner;

        $validate_exists_mobile = User::belongsTorole(Role::DRIVER)->where('mobile', $request->mobile)->exists();
        $validate_exists_email = User::belongsTorole(Role::DRIVER)->where('email', $request->email)->exists();

        if ($validate_exists_mobile) {
            $this->throwCustomException('Provided mobile has already been taken');
        }

        if ($validate_exists_email) {
            $this->throwCustomException('Provided email has already been taken');
        }

        $profile_picture = null;

        if ($uploadedFile = $this->getValidatedUpload('profile', $request)) {
            $profile_picture = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }

        DB::beginTransaction();
        try {

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $request->mobile,
            'mobile_confirmed' => true,
            'timezone'=>auth()->user()->timezone,
            'country'=>auth()->user()->country,
            'profile_picture'=>$profile_picture,
            'refferal_code'=>str_random(6),
        ]);

        $user->attachRole(Role::DRIVER);

        $created_params = $request->only(['name','mobile','email','address']);

        $created_params['service_location_id'] = $owner_detail->service_location_id;
        $created_params['owner_id'] = $owner_detail->id;

        if($request->has('transport_type')){

        $created_params['transport_type'] = $request->transport_type;
            

        }

        $driver = $user->driver()->create($created_params);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error while Registering a driver account. Input params : ' . json_encode($request->all()));
            return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        }
        DB::commit();

         $this->database->getReference('drivers/driver_'.$driver->id)->set(['id'=>$driver->id,'vehicle_type'=>'fleet-owner-type','active'=>0,'approve'=>0,'updated_at'=> Database::SERVER_TIMESTAMP]);

        return $this->respondSuccess(null,'driver_added_succesfully');

    }

    /**
     * Delete Drivers
     * 
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "driver_deleted_succesfully",
     * }
     * */
    public function deleteDriver(Driver $driver){

        $driver->fleetDetail()->update(['driver_id'=>null]);

        $this->database->getReference('drivers/driver_'.$driver->id)->remove();
        $driver->delete();

        $driver->user->delete();

        return $this->respondSuccess(null,'driver_deleted_succesfully');


    }

    

}
