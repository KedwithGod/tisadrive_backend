<?php

namespace App\Http\Controllers\Api\V1\Driver;

use Illuminate\Http\Request;
use App\Models\Admin\Driver;
use App\Models\Admin\DriverDocument;
use App\Models\Admin\DriverNeededDocument;
use App\Models\Admin\OwnerNeededDocument;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Api\V1\Driver\OnlineOfflineController;
use App\Base\Constants\Masters\DriverDocumentStatus;
use App\Transformers\DriverNeededDocumentTransformer;
use App\Transformers\Driver\DriverDocumentTransformer;
use App\Http\Requests\Driver\DriverDocumentUploadRequest;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Transformers\Owner\OwnerNeededDocumentTransformer;
use App\Base\Constants\Auth\Role;
use App\Models\Admin\OwnerDocument;
use App\Models\Admin\FleetDocument;
use App\Models\Admin\Fleet;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\User;
use App\Base\Constants\Masters\PushEnums;
use App\Models\Admin\DriverBankInfo;
use  App\Models\Admin\FleetNeededDocument;
use App\Models\Admin\OwnerBankInfo;
use App\Models\Method;
use App\Models\Field;
use App\Transformers\BankInfoTransformer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Jobs\Mails\SendAccountDisapprovedMailNotification;
use App\Models\Admin\NotificationChannel;
use Log;

/**
 * @group Driver Document Management
 * @authenticated
 *
 * APIs for DriverNeededDocument's
 */
class DriverDocumentController extends OnlineOfflineController
{
    /**
    * ImageUploader instance.
    *
    * @var ImageUploaderContract
    */
    protected $imageUploader;

    protected $database;

    /**
     * DriverDocumentController constructor.
     *
     * @param ImageUploaderContract $imageUploader
     */
    public function __construct(ImageUploaderContract $imageUploader,Database $database)
    {
        $this->imageUploader = $imageUploader;
        $this->database = $database;

    }
    /**
    * Get All documents needed to be uploaded
    * @responseFile responses/driver/ListAllDocumentNeededWithUploadedDocuments.json
    */
    public function index()
    {   
        $uploaded_document = false;

        if (auth()->user()->hasRole(Role::DRIVER)) {

        $driver = auth()->user()->driver;



        if($driver->owner_id){
            $driverneededdocumentQuery  = DriverNeededDocument::active()->where(function($query){
                $query->where('account_type','fleet_driver')->orWhere('account_type','both');
            })->get();
        }else{

        $driverneededdocumentQuery  = DriverNeededDocument::active()->where(function($query){
                $query->where('account_type','individual')->orWhere('account_type','both');
            })->get();
            
        }

        $neededdocument =  fractal($driverneededdocumentQuery, new DriverNeededDocumentTransformer);

        $driver_needed_docs = DriverNeededDocument::whereActive(true)->get();
    
        $driver_approved_docs = $driver->driverDocument()->where('document_status',DriverDocumentStatus::UPLOADED_AND_APPROVED)->count();
        
        $driver_pending_docs = $driver->driverDocument()->whereIn('document_status',[
            DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL,
            DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL
        ])->count();

        if(count($driver_needed_docs) !== $driver_approved_docs){
            if($driver_pending_docs == count($driver_needed_docs) - $driver_approved_docs){
                $uploaded_document = true;
            }
        }


        }else{

            $owner_id = auth()->user()->owner->id;

            $ownerneededdocumentQuery  = OwnerNeededDocument::active()->get();

            if($ownerneededdocumentQuery->isEmpty())
            {
                return $this->throwCustomException("Configuration mis match from Admin");
            }

            $neededdocument =  fractal($ownerneededdocumentQuery, new OwnerNeededDocumentTransformer);


            $owner_approved_docs = OwnerDocument::where('owner_id', $owner_id)->where('document_status',DriverDocumentStatus::UPLOADED_AND_APPROVED)->count();
            $owner_pending_docs = OwnerDocument::where('owner_id', $owner_id)
                                    ->whereIn('document_status',[
                                        DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL,
                                        DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL
                                    ])->count();
            if(count($ownerneededdocumentQuery) !== $owner_approved_docs) {
                if($owner_pending_docs == count($ownerneededdocumentQuery) - $owner_approved_docs){
                    $uploaded_document = true;
                }
            }

        }
        

        $formated_document = $this->formatResponseData($neededdocument);

        return response()->json(['success'=>true,"message"=>'success','enable_submit_button'=>$uploaded_document,'data'=>$formated_document['data']]);
    }

    /**
     * Upload Driver's Document
     * @bodyParam document_id integer required id of the documents needed uploaded
     * @bodyParam identify_number string optional identify number of the document, required sometimes depends on the document
     * @bodyParam expiry_date date required expiry date of the document, the date should be in the format "date_format:Y-m-d H:i:s", eg:2020-08-13 00:00:00
     * @bodyParam document image required document file provided by user
     * @response 
     * {
     *   "success": true,
     *   "message": "success"
     * }
     */
    public function uploadDocuments(DriverDocumentUploadRequest $request)
    {
        $created_params = $request->only(['document_id','identify_number','expiry_date']);

        $enable_driver_auto_approval = get_settings('enable_document_auto_approval');

        // Log::info("enable_driver_auto_approval");
        // Log::info($enable_driver_auto_approval);


        if (auth()->user()->hasRole(Role::DRIVER)) {

        $created_params['document_status'] =DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL;

        $document_exists = auth()->user()->driver->driverDocument()->where('document_id', $request->document_id)->exists();

        if ($document_exists) {
            $created_params['document_status'] =DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL;
        }
        $driver_id = auth()->user()->driver->id;

        $created_params['driver_id'] = $driver_id;

        if ($uploadedFile = $this->getValidatedUpload('document', $request)) {
            $created_params['image'] = $this->imageUploader->file($uploadedFile)
                ->saveDriverDocument($driver_id);
        }

        if($request->has('back_image'))
        {
            if ($uploadedFile = $this->getValidatedUpload('back_image', $request)) {
                $created_params['back_image'] = $this->imageUploader->file($uploadedFile)
                    ->saveDriverDocument($driver_id);
            }
        }

        // Check if document exists
        $driver_documents = DriverDocument::where('driver_id', $driver_id)->where('document_id', $request->input('document_id'))->first();

        if ($driver_documents) {
            DriverDocument::where('driver_id', $driver_id)->where('document_id', $request->input('document_id'))->update($created_params);
        } else {
            DriverDocument::create($created_params);
        }

        $driver_documents = DriverDocument::where('driver_id', $driver_id)->get();

            Driver::where('user_id',auth()->user()->id)->update(['approve' => false]);
            $this->database->getReference('drivers/driver_' . $driver_id)
            ->update(['approve' => false, 'is_active' => true, 'available'=>false, 'updated_at' => Database::SERVER_TIMESTAMP]);
    }else{

        if($request->has('fleet_id') && $request->fleet_id)
        {
            $created_params['document_status'] =DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL;

            $fleet = Fleet::where('id',$request->fleet_id)->first();

            $document_exists = $fleet->fleetDocument()->where('document_id', $request->document_id)->exists();

            if ($document_exists) {
                $created_params['document_status'] =DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL;
            }

            $created_params['fleet_id'] = $fleet->id;

            if ($uploadedFile = $this->getValidatedUpload('document', $request)) {
                $created_params['image'] = $this->imageUploader->file($uploadedFile)
                    ->saveFleetDocument($fleet->id);
            }
            if($request->has('back_image'))
            {
                if ($uploadedFile = $this->getValidatedUpload('back_image', $request)) {
                    $created_params['back_image'] = $this->imageUploader->file($uploadedFile)
                        ->saveFleetDocument($fleet->id);
                }
            }

            // Check if document exists
            $fleet_documents = FleetDocument::where('fleet_id', $fleet->id)->where('document_id', $request->input('document_id'))->first();

            if ($fleet_documents) {
                FleetDocument::where('fleet_id', $fleet->id)->where('document_id', $request->input('document_id'))->update($created_params);
            } else {
                $document_name = FleetNeededDocument::where('id',$request->input('document_id'))->first();
                $created_params['name'] = $document_name->name;

                FleetDocument::create($created_params);
            }

        }else{

            $created_params['document_status'] =DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL;

            $document_exists = auth()->user()->owner->ownerDocument()->where('document_id', $request->document_id)->exists();

            if ($document_exists) {
                $created_params['document_status'] =DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL;
            }
            $owner_id = auth()->user()->owner->id;

            $created_params['owner_id'] = $owner_id;

            if ($uploadedFile = $this->getValidatedUpload('document', $request)) {
                $created_params['image'] = $this->imageUploader->file($uploadedFile)
                    ->saveOwnerDocument($owner_id);
            }
            if($request->has('back_image'))
            {
                if ($uploadedFile = $this->getValidatedUpload('back_image', $request)) {
                    $created_params['back_image'] = $this->imageUploader->file($uploadedFile)
                        ->saveOwnerDocument($owner_id);
                }
            }
            // Check if document exists
            $owner_documents = OwnerDocument::where('owner_id', $owner_id)->where('document_id', $request->input('document_id'))->first();

            if ($owner_documents) {
                OwnerDocument::where('owner_id', $owner_id)->where('document_id', $request->input('document_id'))->update($created_params);
            } else {
                OwnerDocument::create($created_params);
            }


        }
        

    }
        
    if($enable_driver_auto_approval=="1"){

         if (auth()->user()->hasRole(Role::DRIVER)) 
            {
                $driver = auth()->user()->driver;

                // Retrieve all active needed documents for the driver
                $driverNeededDocsCount = DriverNeededDocument::where('active', true)->count();

                // Count the documents uploaded by the driver
                $driverUploadedDocsCount = $driver->driverDocument->count();

                // Check if both counts match
                if ($driverNeededDocsCount === $driverUploadedDocsCount)
                 {
                
                   $driver->update(['approve' => 1]);
                
                    // Update Firebase database
                    $this->database->getReference('drivers/driver_' . $driver->id)
                        ->update([
                            'approve' => 1,
                            'updated_at' => Database::SERVER_TIMESTAMP,
                        ]);
                
                    // Prepare notification title and body
                    // $title = custom_trans('driver_approved', [], $driver->user->lang);
                    // $body = custom_trans('driver_approved_body', [], $driver->user->lang);
                
                    // Dispatch push notification job
                    // dispatch(new SendPushNotification($driver->user, $title, $body));



                    
                        $notification = \DB::table('notification_channels')
                        ->where('topics', 'Driver Account Approval') // Match the correct topic
                        ->first();
                
                        // dd($notification);



                         // send push notification 
                         if ($notification && $notification->push_notification == 1) {
                            // Determine the user's language or default to 'en'
                           $userLang = $driver->user->lang ?? 'en';
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
                           dispatch(new SendPushNotification($driver->user, $title, $body));
                       }
               }
        }
    }
        
        return $this->respondSuccess();
    }

    /**
    * List All Uploaded Documents
    * @responseFile responses/driver/listAllUploadedDocuments.json
    */
    public function listUploadedDocuments()
    {
        $driver_documents = DriverDocument::where('driver_id', auth()->user()->driver->id)->get();

        $result = fractal($driver_documents, new DriverDocumentTransformer);

        return $this->respondSuccess($result);
    }
    /**
     * List All Bank information
     * 
     * @authenticated
     * 
     * @response
     * {
     *      "success": true,
     *      "message": "success",
     *      "data": [
     *          {
     *              "id": 1,
     *              "method_name": "upi",
     *              "active": 1,
     *              "fields": {
     *                  "data": [
     *                      {
     *                          "id": 1,
     *                          "method_id": 1,
     *                          "input_field_name": "upi_number",
     *                          "placeholder": "enter Upi Number",
     *                          "is_required": 0,
     *                          "input_field_type": "text"
     *                      }
     *                  ]
     *              },
     *              "driver_bank_info": {
     *                  "data": []
     *              }
     *          },
     *      ]
     * }
     *
     */
    public function listBankInfo()
    {
        $methods  = Method::where('active', true)->get();
        
      $result =  fractal($methods, new BankInfoTransformer);
      
      return $this->respondSuccess($result);
      
    }
    /**
     * 
     * Update Bank Info
     * @authorised
     * @bodyParam method_id integer required id of the Method field uploaded
     * 
     * @response 
     * {
     *      "success": true,
     *      "message": "Driver Bank information updated successfully.",
     *      "data": []
     * }
     * @response 
     * {
     *      "success": true,
     *      "message": "Owner Bank information updated successfully.",
     *      "data": []
     * }
     */
    public function updateBankinfoNew(Request $request)
    {

        if (auth()->user()->hasRole(Role::DRIVER)) 
        {
        // Validate Request id
            $request->validate([
            'method_id' => 'required|exists:methods,id',
            ]);


            $method_detail = Method::where('id',$request->method_id)->first();

            $driverId = auth()->user()->driver->id;

            $request_params = $request->all();

            foreach ($request_params as $key => $request_param) {
                    
                $field_data = $method_detail->fields()->where('input_field_name',$key)->first();

                if($field_data){

                    $driverBankInfo = DriverBankInfo::where('driver_id', $driverId)
                    ->where('method_id', $request->method_id)
                    ->where('field_id', $field_data->id)
                    ->first();

                if(!$driverBankInfo){

                    DriverBankInfo::create([
                        'driver_id' => $driverId,
                        'method_id' => $request->method_id,
                        'field_id' => $field_data->id,
                        'value' => $request->$key,
                    ]);


                }else{

                    $driverBankInfo->update([
                        'value' => $request->$key,
                    ]);
                }

                }

                    
            }
            return $this->respondSuccess(['message' => 'Driver Bank information updated successfully.']);          

        }else{
           
            $request->validate([
                'method_id' => 'required|exists:methods,id',
                ]);
    
    
                $method_detail = Method::where('id',$request->method_id)->first();
    
                $ownerId = auth()->user()->owner->id;
    
                $request_params = $request->all();
    
                foreach ($request_params as $key => $request_param) {
                        
                    $field_data = $method_detail->fields()->where('input_field_name',$key)->first();
    
                    if($field_data){
    
                        $ownerBankInfo = OwnerBankInfo::where('owner_id', $ownerId)
                        ->where('method_id', $request->method_id)
                        ->where('field_id', $field_data->id)
                        ->first();
    
                    if(!$ownerBankInfo){
    
                        OwnerBankInfo::create([
                            'owner_id' => $ownerId,
                            'method_id' => $request->method_id,
                            'field_id' => $field_data->id,
                            'value' => $request->$key,
                        ]);
    
    
                    }else{
    
                        $ownerBankInfo->update([
                            'value' => $request->$key,
                        ]);
                    }
    
                    }
    
                        
                }
    
    
            return $this->respondSuccess(['message' => 'Owner bank information updated successfully.']);
        }
            


    }

    public function diagnostics() {

        if(!access()->hasRole('driver')) {
            $this->throwAuthorizationException();
        }

        $driver = auth()->user()->driver;


        if($driver->approve) {

            if (!$driver->owner_id) {

                $neededDoc = DriverNeededDocument::active()->where(function($query) {
                    $query->where('account_type','individual')->orWhere('account_type','both');
                })->where('is_required', true)->count();

                $uploadedDoc = $driver->driverDocument()->where('document_status', 1)
                    ->whereHas('driverNeededDocuments',function($query) {
                        $query->where('active',true)->where(function($query) {
                            $query->where('account_type','individual')->orWhere('account_type','both');
                        })->where('is_required', true);
                    })->count();

            } else {

                $neededDoc = DriverNeededDocument::active()->where(function($query) {
                    $query->where('account_type','fleet_Driver')->orWhere('account_type','both');
                })->where('is_required', true)->count();

                $uploadedDoc = $driver->driverDocument()->where('document_status', 1)
                    ->whereHas('driverNeededDocuments',function($query) {
                        $query->where('active',true)->where(function($query) {
                            $query->where('account_type','fleet_Driver')->orWhere('account_type','both');
                        })->where('is_required', true);
                    })->count();
            }
            if( $neededDoc != $uploadedDoc){
                Driver::where('id', $driver->id)->update([
                    'approve'=> false,
                    'available'=>false
                ]);
                $this->toggle();
            }else{
                Driver::where('id', $driver->id)->update([
                    'approve'=> true,
                    'available'=>true
                ]);
            }

        }

        $driver = Driver::find($driver->id);

        if ($driver->approve) {


            $notification = NotificationChannel::where('topics', 'Driver Diagnostic') // Match the correct topic
            ->first();
            

        //    send push notification 
            if ($notification && $notification->push_notification == 1) {
                 // Determine the user's language or default to 'en'
                $userLang = $driver->user->lang ?? 'en';

                $translation = $notification->notificationChannelTranslationWords()
                    ->where(function($query) use ($userLang) {
                        $query->where('locale', $userLang)->orWhere('locale', 'en');
                    })
                    ->first();
                
                $title =  $translation->push_title ?? $notification->push_title;
                $body = strip_tags($translation->push_body ?? $notification->push_body);
                dispatch(new SendPushNotification($driver->user, $title, $body));
            }
            
            $this->database->getReference('drivers/driver_' . $driver->id)
            ->update(['approve' => true, 'is_active' => true, 'is_available'=>true, 'updated_at' => Database::SERVER_TIMESTAMP]);
        }else {
            

                
            $notification = NotificationChannel::where('topics', 'Driver Account Disapproval') // Match the correct topic
            ->first();
            
            // send push notification 
            if ($notification && $notification->push_notification == 1) {
                 // Determine the user's language or default to 'en'
                $userLang = $driver->user->lang ?? 'en';
                // dd($userLang);

                // Fetch the translation based on user language or fall back to 'en'
                $translation = $notification->notificationChannelTranslationWords()
                    ->where(function($query) use ($userLang) {
                        $query->where('locale', $userLang)->orWhere('locale', 'en');
                    })
                    ->first();

                $title =  $translation->push_title ?? $notification->push_title;
                $body = strip_tags($translation->push_body ?? $notification->push_body);

                dispatch(new SendPushNotification($driver->user, $title, $body));
            }

            // send email account disapproval
            dispatch(new SendAccountDisapprovedMailNotification($driver));
            // SendAccountDisapprovedMailNotification::dispatch($driver);

            $this->database->getReference('drivers/driver_' . $driver->id)
            ->update(['approve' => false, 'is_active' => true, 'is_available'=>false, 'updated_at' => Database::SERVER_TIMESTAMP]);
        }

        $response =  [
            'approve'=> $driver->approve,
            'available'=>$driver->available
        ];

        return $this->respondSuccess($response,'DiagnosticsComplete');
    }
    
}
