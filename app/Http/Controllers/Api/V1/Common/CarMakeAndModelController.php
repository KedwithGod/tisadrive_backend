<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Api\V1\BaseController;
use Carbon\Carbon;
use Sk\Geohash\Geohash;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use App\Helpers\Rides\FetchDriversFromFirebaseHelpers;
use App\Models\Admin\DriverAvailability;
use App\Models\Master\MobileAppSetting;
use App\Transformers\Requests\MobileAppSettingsTransformer;
use Illuminate\Support\Facades\DB;
use Config;

/**
 * @group Vehicle Management
 *
 * APIs for vehilce management apis. i.e types,car makes,models apis
 */
class CarMakeAndModelController extends BaseController
{
    use FetchDriversFromFirebaseHelpers;

    /**
     * The country model instance.
     *
     * @var \Kreait\Firebase\Contract\Database
     */
    protected $database;

    /**
     * CancellationReasonsController constructor.
     *
     * @param \Kreait\Firebase\Contract\Database $database;
     */
    public function __construct(Database $database)
    {
        $this->database = $database;

    }


    /**
    * Get App Modules
    * @response 
    * {
    *      "success": true,
    *      "message": "success",
    *      "enable_owner_login": "1",
    *      "enable_email_otp": "1",
    *      "enable_user_referral_earnings": null,
    *      "enable_driver_referral_earnings": null,
    *      "firebase_otp_enabled": false
    * }
    */
    public function getAppModule()
    {

        $enable_owner_login =  get_settings('shoW_owner_module_feature_on_mobile_app');

        $enable_email_otp =  get_settings('shoW_email_otp_feature_on_mobile_app');
     
        $firebase_otp_enabled =  get_sms_settings('enable_firebase_otp');

        $firebase_otp_enabled =  get_sms_settings('enable_firebase_otp');
// referral
        $enable_user_referral_earnings =  get_settings('enable_user_referral_earnings');

        $enable_driver_referral_earnings =  get_settings('enable_driver_referral_earnings');



        

        $firebase_otp = false;

        if($firebase_otp_enabled==1)
        {
            $firebase_otp = true;
        }


        return response()->json(['success'=>true,"message"=>'success','enable_owner_login'=>$enable_owner_login,
                                'enable_email_otp'=>$enable_email_otp,
                                'enable_user_referral_earnings'=>$enable_user_referral_earnings,
                                'enable_driver_referral_earnings'=>$enable_driver_referral_earnings,
                                'firebase_otp_enabled'=>$firebase_otp]);
    }


    public function mobileAppMenu()
    {
        $ride_modules = MobileAppSetting::whereActive(true)->orderBy('order_by', 'asc')->get();

        return $this->respondSuccess($ride_modules, 'ride_modules_listed');


    }
    /**
     * Test Api
     * @hideFromAPIDocumentation
     * 
     * */
    public function testApi()
    {
        $notificationData = [
            'id' => uniqid(),  // Generate a unique ID for the notification
            'body' => "New User Registered",  // Notification body text
            'title' => "New User Registered", // Notification title
            'read' => false,  // Default to unread
            'updated_at' => round(microtime(true) * 1000), // Unix timestamp in milliseconds
            'url' => "http://127.0.0.1:8000/admins" // URL to redirect when clicked
        ];
        
        // Insert data into Firebase's 'admin-notification' node with the unique ID
        $this->database->getReference('admin-notification/' . $notificationData['id'])
                       ->set($notificationData);
    
        return response()->json(['message' => 'Notification created successfully'], 201);
    }
    
}
