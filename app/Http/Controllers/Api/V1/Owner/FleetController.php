<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Models\Admin\Driver;
use Illuminate\Support\Carbon;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Admin\Fleet;
use Illuminate\Http\Request;
use App\Transformers\Driver\DriverTransformer;
use App\Transformers\Owner\FleetTransformer;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Models\Admin\FleetNeededDocument;
use App\Transformers\Owner\FleetNeededDocumentTransformer;
use App\Models\Admin\FleetDocument;
use App\Jobs\Notifications\AndroidPushNotification;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
use Log;

/**
 * @group Fleet-Owner-apis
 * @authenticated
 */
class FleetController extends BaseController
{
    protected $driver;
    protected $fleet;

    protected $imageUploader;

    protected $database;



    public function __construct(Driver $driver,Fleet $fleet,ImageUploaderContract $imageUploader,Database $database)
    {
        $this->driver = $driver;

        $this->fleet = $fleet;

        $this->imageUploader = $imageUploader;

        $this->database = $database;

    }

    /**
    * List Fleets
    * @group Fleet-Owner-apis
    * @return \Illuminate\Http\JsonResponse
    * @response
    * {
    *       "success": true,
    *       "message": "fleet_listed",
    *       "data": [
    *           {
    *               "id": "343efadd-a67a-4a56-955e-402a79ca321d",
    *               "owner_id": 39,
    *               "driver_id": null,
    *               "driver_name": null,
    *               "license_number": "TN 35 W 5486",
    *               "vehicle_type": "Sedan",
    *               "brand": "Suxuki",
    *               "model": "Swift",
    *               "approve": 0,
    *               "car_color": "White",
    *               "type_icon": "http://localhost/new_vue_tagxi/public/storage/uploads/types/images/VrD7TsjDJgx2ypg3QadEGfvvAr15yzShsygQXpTo.jpeg",
    *               "status": "waiting",
    *               "total_earnings": 0,
    *               "total_driver_earnings": 0,
    *               "total_admin_earnings": 0,
    *               "total_trips": 0,
    *               "completed_requests": 0,
    *               "average_user_rating": 0,
    *               "driverDetail": null
    *           }
    *       ]
    * }
    */
    public function index()
    {
        $fleets = Fleet::where('owner_id',auth()->user()->id)->get();

        $result = fractal($fleets, new FleetTransformer)->parseIncludes(['driverDetail']);


        return $this->respondSuccess($result,'fleet_listed');
    }


    /**
     * Store Fleets
     * 
     * @bodyParam vehicle_type integer required The ID of the vehicle type. Example: 1
     * @bodyParam car_color string required The color of the car. Example: "Red"
     * @bodyParam car_make integer optional The ID of the car make (brand). Example: 10
     * @bodyParam car_model integer optional The ID of the car model. Example: 20
     * @bodyParam custom_make string optional A custom car make if not listed in predefined options. Example: "CustomBrand"
     * @bodyParam custom_model string optional A custom car model if not listed in predefined options. Example: "CustomModelX"
     * @bodyParam car_number string required The license plate number of the car. Example: "ABC-1234"
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     * */
    public function storeFleet(Request $request){

        $created_params = $request->only(['vehicle_type','car_color']);

        $created_params['brand'] = $request->car_make;
        $created_params['model'] = $request->car_model;
        $created_params['custom_make']=$request->custom_make;
        $created_params['custom_model']=$request->custom_model;
        $created_params['license_number'] = $request->car_number;
        $created_params['owner_id'] = auth()->user()->id;

        $fleet = $this->fleet->create($created_params);

        return $this->respondSuccess();
    }



    /**
     * List Drivers For Assign Drivers
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * @responseFile responses/owner/listDriver.json
     * */
    public function listDrivers()
    {
        $owner_id = auth()->user()->owner->id;

// Log::info(request()->all());
        $drivers = Driver::where('owner_id','=',$owner_id)->get();

// Log::info($drivers);


        if(request()->has('fleet_id') && request()->fleet_id){

        $drivers = Driver::where('owner_id','=',$owner_id)->where('approve',true)->where(function($query) use ($owner_id){
            $query->where('fleet_id','!=',request()->fleet_id)->orWhere('fleet_id',null);
        })->get();

        }

        $result = fractal($drivers, new DriverTransformer);

        return $this->respondOk($result);

    }

    /**
     * Assign Drivers
     * 
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     * */
    public function assignDriver(Request $request,Fleet $fleet)
    {
        $driver = Driver::whereId($request->driver_id)->first();
        
        $request->validate([
        'driver_id' => 'required',
        ]);

        if($fleet->driver_id==$request->driver_id){
            
            return $this->respondSuccess();

        }
        if($fleet->driverDetail){

            $fleet_driver = $fleet->driverDetail;
           
            // $title = custom_trans('fleet_removed_from_your_account_title',[],$fleet_driver->user->lang);
            // $body = custom_trans('fleet_removed_from_your_account_body',[],$fleet_driver->user->lang);

            $this->database->getReference('drivers/'.'driver_'.$fleet_driver->id)->update(['fleet_changed'=>1,'updated_at'=> Database::SERVER_TIMESTAMP]);

            $notifable_driver = $fleet_driver->user;
            // dispatch(new SendPushNotification($notifable_driver,$title,$body));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Fleet Account Removed') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $fleet_driver->user->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
                    dispatch(new SendPushNotification($notifable_driver, $title, $body));
                }

            $fleet->driverDetail()->update(['fleet_id'=>null,'vehicle_type'=>null,
            'car_make' => null,
            'car_model' => null,
            'car_color' => null,
            'custom_make'=>null,
            'custom_model'=>null  ,         
        ]);


        }

        $fleet->fresh();

        if($driver->fleetDetail){

            $driver->fleetDetail()->update(['driver_id'=>null]);

        }

        $driver->fresh();

        $fleet->update(['driver_id'=>$request->driver_id]);


        $driver->update([
            'fleet_id' => $fleet->id,
            'vehicle_type' => $fleet->vehicle_type,
            'transport_type' => $fleet->vehicleTypeDetail ? $fleet->vehicleTypeDetail->is_taxi : 'both',
            'car_make' => $fleet->brand,
            'car_model' => $fleet->model,
            'car_color' => $fleet->car_color,

        ]);

        $driver->fresh();

        // $title = custom_trans('new_fleet_assigned_title');
        // $body = custom_trans('new_fleet_assigned_body');

        $notifable_driver = $driver->user;
        // $title = custom_trans('new_fleet_assigned_title',[],$driver->lang);
        // $body = custom_trans('new_fleet_assigned_body',[],$driver->lang);

        // dispatch(new SendPushNotification($notifable_driver,$title,$body));

        $notification = \DB::table('notification_channels')
                ->where('topics', 'New Fleet Assigned') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $driver->lang ?? 'en';
                    // dd($userLang);
    
                    // Fetch the translation based on user language or fall back to 'en'
                    $translation = \DB::table('notification_channels_translations')
                        ->where('notification_channel_id', $notification->id)
                        ->where('locale', $userLang)
                        ->first();
    
                    // If no translation exists, fetch the default language (English)
                    if (!$translation) {
                        $translation = \DB::table('notification_channels_translations')
                            ->where('notification_channel_id', $notification->id)
                            ->where('locale', 'en')
                            ->first();
                    }            
                    
                    $title =  $translation->push_title ?? $notification->push_title;
                    $body = strip_tags($translation->push_body ?? $notification->push_body);
                    dispatch(new SendPushNotification($notifable_driver, $title, $body));
                }

        $this->database->getReference('drivers/driver_'.$driver->id)->update(['fleet_changed'=>1,'updated_at'=> Database::SERVER_TIMESTAMP]);

        return $this->respondSuccess();
        
    }

    /**
     * List of Fleet Needed Documents
     * 
     * @response
     * {
     *      "success": true,
     *      "message": "success",
     *      "enable_submit_button": false,
     *      "data": [
     *          {
     *              "id": 1,
     *              "name": "RC Book",
     *              "doc_type": "image",
     *              "has_identify_number": false,
     *              "has_expiry_date": true,
     *              "active": 1,
     *              "identify_number_locale_key": null,
     *              "is_uploaded": false,
     *              "document_status": 2,
     *              "document_status_string": "Not Uploaded",
     *              "fleet_document": null
     *          }
     *      ]
     *  }
     * */
    public function neededDocuments()
    {

        $ownerneededdocumentQuery  = FleetNeededDocument::where('active', true)->get();

        $neededdocument =  fractal($ownerneededdocumentQuery, new FleetNeededDocumentTransformer);

            foreach (FleetNeededDocument::active()->get() as $key => $needed_document) {
            if (FleetDocument::where('fleet_id', request()->fleet_id)->where('document_id', $needed_document->id)->exists()) {
                $uploaded_document = true;
            } else {
                $uploaded_document = false;
            }
        }

        $formated_document = $this->formatResponseData($neededdocument);

//dd($formated_document);


        return response()->json(['success'=>true,"message"=>'success','enable_submit_button'=>$uploaded_document,'data'=>$formated_document['data']]);

    }


}
