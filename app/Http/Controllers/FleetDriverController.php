<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Country;
use App\Models\Admin\Owner;
use Illuminate\Http\Request;
use App\Models\Admin\Driver;
use App\Models\Admin\VehicleType;
use App\Models\Admin\DriverDocument;
use App\Models\Admin\ServiceLocation;
use Kreait\Firebase\Contract\Database;
use App\Base\Filters\Admin\OwnerFilter;
use App\Base\Filters\Admin\DriverFilter;
use App\Transformers\CountryTransformer;
use App\Models\Admin\DriverNeededDocument;
use App\Models\Admin\FleetNeededDocument;
use App\Http\Controllers\Web\BaseController;
use App\Base\Constants\Masters\DriverDocumentStatus;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use Carbon\Carbon;
use App\Jobs\Notifications\SendPushNotification;
use App\Base\Constants\Masters\PushEnums;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Method;
use App\Models\Request\Request as RequestModel;
use App\Models\Request\RequestBill;
use Illuminate\Support\Facades\Mail;
use App\Mail\DriverApprovedMail;
use App\Mail\DriverDisapproveMail;
use App\Jobs\Mails\SendAccountApprovedMailNotification;
use App\Jobs\Mails\SendAccountDisapprovedMailNotification;

class FleetDriverController extends BaseController
{
    protected $imageUploader;
    protected $user;
    protected $database;
    protected $driver;


    public function __construct(ImageUploaderContract $imageUploader, User $user, Driver $driver,Database $database)
    {
        $this->imageUploader = $imageUploader;
        $this->database = $database;
        $this->user = $user;
        $this->driver = $driver;
    }
    public function index() 
    {
        $location = ServiceLocation::active()->get();
        $types = VehicleType::active()->get();
        $owners = Owner::where('approve',true)->where('service_location_id',$location[0]->id)->limit(10)->get();
      
        return Inertia::render('pages/fleet_drivers/approved_drivers/index',[
            'serviceLocations' => $location,
            'types' => $types
        ]);
    }
    public function pendingIndex() {
        $location = ServiceLocation::active()->get();
        $types = VehicleType::active()->get();
        return Inertia::render('pages/fleet_drivers/pending_drivers/index',[
            'serviceLocations' => $location,
            'types' => $types
        ]);
    }

    public function listDrivers(QueryFilterContract $queryFilter)
    {
        $query = Driver::whereNotNull('owner_id')->orderBy('created_at','DESC');

        if(auth()->user()->hasRole('owner'))
        {
            $owner = auth()->user()->owner;
          
            $query = Driver::where('owner_id', $owner->id)->orderBy('created_at','DESC');

        }
        
        $results =  $queryFilter->builder($query)->customFilter(new DriverFilter())->paginate();

        return response()->json([
            'results' => $results,
        ]);
    }
    public function listOwnersByLocation(Request $request)
    {
        if($request->search){
            $query = Owner::where('approve',true)->where('name','LIKE','%'.$request->search.'%');
        }else{
            $query = Owner::where('approve',true)->where('service_location_id',$request->service_location_id);
        }
        $results = $query->get();
        return response()->json(['results' => $results]);
    }

    public function listOwners(Request $request)
    {
        $results = Owner::where('approve', true)
                        ->where('service_location_id', $request->service_location_id)
                        ->get();
    
        if ($results->isEmpty()) {
            return response()->json(['results' => [], 'message' => 'No owners found'], 404);
        }
    
        return response()->json(['results' => $results]);
    }
    

    public function create(Owner $owner)
    {
        $country = Country::active()->get();
        $location = ServiceLocation::active()->get();

        $countries = fractal($country, new CountryTransformer);
        $result = json_decode($countries->toJson(),true);
        return Inertia::render('pages/fleet_drivers/approved_drivers/create',[
            'owner'=>$owner,
            'countries'=>$result['data'],
            'serviceLocation'=>$location]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $created_params = $request->validate([
            'name' => 'required',
            'service_location_id' => 'required',
            'owner_id' => 'required',
            'country' => 'required',
            'mobile' => 'required',
            'email' => 'required',
            'gender' => 'required',
        ]);
        $user_created_params = $request->only([
            'name','country', 'mobile', 'email', 'gender',
        ]);
        $user_created_params['password'] = bcrypt($request->input('password'));
        $user_created_params['refferal_code'] = str_random(6);

        // if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
        //     $profile_picture = $this->imageUploader->file($uploadedFile)->saveProfilePicture();
        // }

        if ($uploadedFile = $request->file('profile_picture')) {
            $user_created_params['profile_picture']  = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }

        $validate_exists_email = $this->checkEmailExists($request->email)->getData()->exists;
        $validate_exists_mobile = $this->checkMobileExists($request->mobile)->getData()->exists;

        if ($validate_exists_email) {
            return response()->json([
                'email'=>'Provided email has already been taken',
            ], 422);
        }
        if ($validate_exists_mobile) {
            return response()->json([
                'email'=>'Provided mobile has already been taken',
            ], 422);
        }
        
        $user = User::create($user_created_params);
        $created_params['user_id'] = $user->id;
        $driver = Driver::create($created_params);
        $driver->driverWallet()->create(['amount_added'=>0]);
        $user->attachRole('driver');
    
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver created successfully.',
        ], 201);
    }

    public function edit(Driver $driver) {
        $query = Country::active()->get();

        $location = ServiceLocation::active()->get();
// dd($driver);

        $countries = fractal($query, new CountryTransformer);
        $result = json_decode($countries->toJson(),true);

        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();

        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;

        return Inertia::render('pages/fleet_drivers/approved_drivers/create',[
            'owner'=>$driver->owner,
            'driver'=>$driver,
            'countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,
            'default_flag'=>$default_flag,
            'default_country_id'=>$default_country_id,
            'serviceLocation'=>$location
        ]);
    }
    public function update(Driver $driver,Request $request) {
        // Validate the incoming request
        $updated_params = $request->validate([
            'name' => 'required',
            'service_location_id' => 'required',
            'owner_id' => 'required',
            'country' => 'required',
            'mobile' => 'required',
            'email' => 'required',
            'gender' => 'required',
        ]);
        $user_updated_params = $request->only([
            'name','country', 'mobile', 'email', 'gender',
        ]);
        if($request->input('password')){
            $user_updated_params['password'] = bcrypt($request->input('password'));
        }

        // if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
        //     $profile_picture = $this->imageUploader->file($uploadedFile)->saveProfilePicture();
        // }

        if ($uploadedFile = $request->file('profile_picture')) {
            $user_updated_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }
        $validate_exists_email = $this->checkEmailExists($request->email, $driver->id)->getData()->exists;
        $validate_exists_mobile = $this->checkMobileExists($request->mobile, $driver->id)->getData()->exists;

        if ($validate_exists_email) {
            return response()->json([
                'email'=>'Provided email has already been taken',
            ], 422);
        }
        if ($validate_exists_mobile) {
            return response()->json([
                'email'=>'Provided mobile has already been taken',
            ], 422);
        }

        $driver->user->update($user_updated_params);
        $updated_params['user_id'] =$driver->user->id;
        $driver->update($updated_params);
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver Updated successfully.',
        ], 201);
    }

    public function checkMobileExists($mobile, $driverId = null)
    {
        $query = Driver::where('mobile', $mobile);
        if ($driverId !== null) {
            $query->where('id', '!=', $driverId);
        }
        $driverExists = $query->exists();
        return response()->json(['exists' => $driverExists]);
    }

    public function checkEmailExists($email, $driverId = null)
    {
        $query = Driver::where('email', $email);
        if ($driverId !== null) {
            $query->where('id', '!=', $driverId);
        }
        $driverExists = $query->exists();
        return response()->json(['exists' => $driverExists]);
    }

    public function editPassword(Driver $driver)
    {
        $query = Country::active()->get();

        $location = ServiceLocation::active()->get();
// dd($driver);

        $countries = fractal($query, new CountryTransformer);
        $result = json_decode($countries->toJson(),true);

        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();

        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;

        return Inertia::render('pages/fleet_drivers/approved_drivers/edit',[
            'owner'=>$driver->owner,
            'driver'=>$driver,
            'countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,
            'default_flag'=>$default_flag,
            'default_country_id'=>$default_country_id,
            'serviceLocation'=>$location
        ]);
    }

    public function updatePasswords(Driver $driver,Request $request)
    {
        // Validate the password and confirmation
        $updated_params = $request->validate([
            'password' => 'required|min:8',  // Confirmed is for password_confirmation
            'confirm_password' => 'required|same:password',
        ]);
        if($request->input('password')){
            $user_updated_params['password'] = bcrypt($request->input('password'));
        }

        $driver->user->update($user_updated_params);
        $updated_params['user_id'] =$driver->user->id;
        $driver->update($updated_params);
        return response()->json([
            'successMessage' => 'Password updated successfully.',
        ], 201);
    }

    public function delete(Driver $driver)
    {
        // dd($driver);
        $driver->delete();
        $driver->user->delete();
        $this->database->getReference('drivers/driver_' . $driver->id)
                ->remove();     

        return response()->json([
            'successMessage' => 'FleetDriver deleted successfully',
        ]);
    } 
    public function editDocument(Driver $driver,DriverNeededDocument $document)
    {
        $neededDocument = DriverNeededDocument::where('account_type','fleet_driver')->orWhere('account_type', 'both')->whereActive(true)->get();
        $documents = $driver->listDriversDocument;
        return Inertia::render('pages/fleet_drivers/approved_drivers/edit_document',['neededDocuments'=>$neededDocument,'documents'=>$documents,'driver'=>$driver]);
    }

    public function UploadDocument(Driver $driver,Request $request) {
        $neededDocuments = DriverNeededDocument::where('account_type','fleet_driver')->orWhere('account_type', 'both')->whereActive(true)->get();
        foreach ($neededDocuments as $key => $docs) {
            $name = 'iconFile_'.($key + 1);
            $document_params = [
                'document_id' =>$docs->id,
                'driver_id' =>$driver->id,
            ];
            $driver_documents = DriverDocument::where('driver_id', $driver->id)->where('document_id', $docs->id)->first();
            if ($uploadedFile = $this->getValidatedUpload($name, $request)) {
                $document_params['image'] = $this->imageUploader->file($uploadedFile)->saveDriverDocument($driver->id);
            }
            if (isset($request->identify_number[$key])) {
                $document_params['identify_number'] = $request->identify_number[$key];
            }
            if (isset($request->expiry_date[$key])) {
                $document_params['expiry_date'] = $request->expiry_date[$key];
            }
            if ($driver_documents) {
                $document_params['document_status'] =DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL;
                DriverDocument::where('driver_id', $driver->id)->where('document_id', $docs->id)->update($document_params);
            } else {
                $document_params['document_status'] =DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL;
                DriverDocument::create($document_params);
            }
        }

        return response()->json([
            'successMessage' => 'Driver Document Uploaded Successfully',
        ],201);
    }

    public function UpdateDriverDeclineReason(Request $request)
    {    
        $driver = Driver::whereId($request->id)->first();
        $driver->update([
            'reason' => $request->reason,
            'approve' => 0
        ]);

        $driver->driverDocument()->update([
            'document_status' => 5
        ]);

        $this->database->getReference('drivers/driver_' . $driver->id)
                ->update(['approve' => 0, 'updated_at' => Database::SERVER_TIMESTAMP]);

        return response()->json([
            'successMessage' => 'Reason declined.',
            'driver' => $driver,
        ], 201);
    }



    public function approveDocument(DriverDocument $document) {
        // dd($document);
        if($document->document_status == 1){
            $document->update(['document_status'=>DriverDocumentStatus::UPLOADED_AND_DECLINED]);
        }else{
            $document->update(['document_status'=>DriverDocumentStatus::UPLOADED_AND_APPROVED]);
        }
        return response()->json([
            'successMessage' => 'Document Approved Successfully',
            'document' => $document,
        ],201);
    }

    public function approve(Driver $driver,Database $database,Request $request) {


        // dd("dfkf");

        if(isset($request->status)){
            $driver->update(['approve'=>false]);
            $message = 'Driver Disapproved Successfully';
            return response()->json([
                'successMessage' => $message,
                'driver' => $driver,
            ],201);
        }
        $neededDocuments = FleetNeededDocument::active()->get();
        $needed_count = 0;
        $uploaded_count = 0;
        foreach($neededDocuments as $key=> $document){
            $doc = $driver->driverDocument()->where('document_id',$document->id)->first();
            $needed_count++;
            if($doc && $doc->document_status == DriverDocumentStatus::UPLOADED_AND_APPROVED){
                $uploaded_count++;
            }
        }
        $status = $uploaded_count == $needed_count;
        if($status){
            $driver->update(['approve'=>true]);
            $message = 'Fleet Approved Successfully';
        }else{
            $driver->update(['approve'=>false]);
            $message = 'Fleet Disapproved Successfully';
        }
        $this->database->getReference('drivers/driver_'.$driver->id)->update(['approve'=>(int)$status,'updated_at'=> Database::SERVER_TIMESTAMP]);
        

        return response()->json([
            'successMessage' => $message,
        ],201);
    }

    public function pendingDriverIndex() {
        return Inertia::render('pages/fleet_drivers/pending_drivers/index');
  
    }

    public function approvedDriverViewDocument(Driver $driver)
    {
        // Fetch uploaded documents
        $driverDocuments = $driver->driverDocument ?: collect(); // Default to empty collection if null
        $driverDocuments = $driverDocuments->keyBy('document_id'); // Key by document_id for easy lookup
    
        // Fetch required documents
        $driverNeededDocuments = DriverNeededDocument::where(function ($query) {
            $query->where('account_type', 'fleet_driver')
                  ->orWhere('account_type', 'both');
        })->where('active', true)
          ->get();
    
        // Merge data
        $documents = $driverNeededDocuments->map(function ($doc) use ($driverDocuments) {
            $uploadedDoc = $driverDocuments->get($doc->id);
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'doc_type' => $doc->doc_type,
                'has_identify_number' => $doc->has_identify_number,
                'has_expiry_date' => $doc->has_expiry_date,
                'active' => $doc->active,
                'identify_number_locale_key' => $doc->identify_number_locale_key,
                'account_type' => $doc->account_type,
                'uploaded' => $uploadedDoc ? true : false,
                'expiry_date' => $uploadedDoc->expiry_date ?? null,
                'identify_number' => $uploadedDoc->identify_number ?? null,
                'document_status' => $uploadedDoc->document_status ?? null,
                'comment' => $uploadedDoc->comment ?? null,
                'image' => $uploadedDoc->image ?? null,
                'back_image' => $uploadedDoc->back_image ?? null,
                'document_name_front' => $doc->document_name_front, // Include front name
                'document_name_back' => $doc->document_name_back, // Include back name


            ];
        });

        // dd($documents);
    
        return Inertia::render('pages/fleet_drivers/approved_drivers/document', [
            'documents' => $documents,
            'driverId' => $driver->id,
        ]);
    }
    

    public function driverDocumentList(Driver $driver,QueryFilterContract $queryFilter) {
        
        // Fetch uploaded documents
        $driverDocuments = $driver->driverDocument ?: collect(); // Default to empty collection if null
        $driverDocuments = $driverDocuments->keyBy('document_id'); // Key by document_id for easy lookup
    
        // Fetch required documents
        $driverNeededDocuments = DriverNeededDocument::where(function ($query) {
            $query->where('account_type', 'fleet_driver')
                  ->orWhere('account_type', 'both');
        })->where('active', true)
          ->get();

        $documents = $driverNeededDocuments->map(function ($doc) use ($driverDocuments) {
            $uploadedDoc = $driverDocuments->get($doc->id);
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'doc_type' => $doc->doc_type,
                'has_identify_number' => $doc->has_identify_number,
                'has_expiry_date' => $doc->has_expiry_date,
                'active' => $doc->active,
                'identify_number_locale_key' => $doc->identify_number_locale_key,
                'account_type' => $doc->account_type,
                'uploaded' => $uploadedDoc ? true : false,
                'expiry_date' => $uploadedDoc->expiry_date ?? null,
                'identify_number' => $uploadedDoc->identify_number ?? null,
                'document_status' => $uploadedDoc->document_status ?? null,
                'comment' => $uploadedDoc->comment ?? null,
                'image' => $uploadedDoc->image ?? null,
                'back_image' => $uploadedDoc->back_image ?? null,
                'document_name_front' => $doc->document_name_front, // Include front name
                'document_name_back' => $doc->document_name_back, // Include back name
            ];
        });
        
        return response()->json([
            'results' => $documents,
        ]);
    }

    public function documentUpload(DriverNeededDocument $document, Driver $driverId)
    {
        $uploaded = $driverId->driverDocument()->where('document_id', $document->id)->first();

// dd($uploaded);
    return Inertia::render('pages/fleet_drivers/approved_drivers/document_upload',['driverId'=>$driverId,
    'uploaded'=>$uploaded, 'document'=>$document,]);

    }
    public function documentUploadStore(Request $request, DriverNeededDocument $document, Driver $driverId,)
    {

        // dd($request->all());
        $created_params = $request->only(['identify_number']);

        $created_params['driver_id'] = $driverId->id;
        $created_params['document_id'] = $document->id;

        $created_params['expiry_date'] = null;


        if($request->expiry_date!=null)
        {
            $expiry_date = Carbon::parse($request->expiry_date)->toDateTimeString();

            $created_params['expiry_date'] = $expiry_date;
        }

        if ($uploadedFile = $request->file('image')) {
            $created_params['image'] = $this->imageUploader->file($uploadedFile)
                ->saveDriverDocument($driverId->id);
        }

        if ($uploadedFile = $request->file('back_image')) {
            $created_params['back_image'] = $this->imageUploader->file($uploadedFile)
                ->saveDriverDocument($driverId->id);
        }
        // dd($created_params);

        // Check if document exists
        $driver_documents = DriverDocument::where('driver_id', $driverId->id)->where('document_id', $document->id)->first();

        if ($driver_documents) {
            $created_params['document_status'] = DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL;
            DriverDocument::where('driver_id', $driverId->id)->where('document_id', $document->id)->update($created_params);
        } else {
            $created_params['document_status'] = DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL;
            DriverDocument::create($created_params);
        }



        if ($uploadedFile = $this->getValidatedUpload('iconFile', $request)) {
            $created_params['iconFile'] = $this->imageUploader->file($uploadedFile)
                ->saveVehicleTypeImage();
        }

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver Document uploaded successfully.',
                'driverId'=>$driverId,
                'document'=>$document
                ], 201);

    }
    
    public function approveDocumentStatus($driver)
    {
        $neededDoc = DriverNeededDocument::active()->where(function($query) {
            $query->where('account_type','fleet_driver')->orWhere('account_type','both');
        })->count();
        $driver = Driver::with('driverDocument')->find($driver);
        $uploadedDoc = $driver->driverDocument()
            ->whereHas('driverNeededDocuments', function ($query) {
                $query->where('active', true)
                    ->where(function ($query) {
                        $query->where('account_type', 'fleet_driver')
                            ->orWhere('account_type', 'both');
                    });
            })
            ->whereIn('document_status', [1])
            ->count();

            if($neededDoc != $uploadedDoc){
                return response()->json([
                    'status' => 'failure',
                    'message' => 'Driver document Disapproved.',
                    'data' =>'uploaddocument'
                ]);            
            }       

        
          
            $driver->update([
                'approve'=>1,
                'reason' =>Null
            ]);

            $this->database->getReference('drivers/driver_' . $driver->id)
            ->update(['approve' => 1, 'updated_at' => Database::SERVER_TIMESTAMP]);
    
            // $title = custom_trans('driver_approved', [], $driver->user->lang);
            // $body = custom_trans('driver_approved_body', [], $driver->user->lang);
        


            $notification = \DB::table('notification_channels')
            ->where('topics', 'Driver Account Approval') // Match the correct topic
            ->first();

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

            //   send email account approved
            SendAccountApprovedMailNotification::dispatch($driver);

            return response()->json([
                'status' => 'success',
                'message' => 'Driver document Approved.',
            ]);
            
    

    }
    
    public function approveDriverDocument($documentId, $driverId, $status)
    {
        // dd($driverId);
        $driverDoc = DriverDocument::where('driver_id', $driverId)->where('document_id', $documentId)->first();
    
        if (!$driverDoc) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Document not found for the given driver.'
            ], 404); // Return a 404 status code for better semantics
        }
    
        $driverDoc->update(['document_status' => $status]);
    
        $driver = Driver::find($driverId);
        $documentStatuses = $driver->driverDocument->pluck('document_status');
        $neededDoc = DriverNeededDocument::active()->where(function($query) {
            $query->where('account_type','fleet_driver')->orWhere('account_type','both');
        })->where('is_required', true)->count();
        $uploadedDoc = $driver->driverDocument()->whereHas('driverNeededDocuments',function($query) {
            $query->where('active',true)->where(function($query) {
                $query->where('account_type','fleet_driver')->orWhere('account_type','both');
            })->where('is_required', true);
        })->count();

        if( $neededDoc != $uploadedDoc){
            return redirect('approved-drivers/document/'.$driver->id);
        }

        if($status==1)
        {
            // Get all document IDs that match the condition
            $docIds = $driver->driverDocument()
            ->whereHas('driverNeededDocuments', function ($query) {
                $query->where('active', true)->where(function($query) {
                    $query->where('account_type','fleet_driver')->orWhere('account_type','both');
                })
                    ->where('is_required', true);
                
            })
            ->pluck("id");

            // Get statuses of the driver's documents
            $documentStatus = $driver->driverDocument->whereIn('id', $docIds)->pluck('document_status');
       
            $allDocumentsApproved = $documentStatus->every(function ($value) {
                return $value == 1;
            });
            // dd($allDocumentsApproved);
            if ($allDocumentsApproved)
            {
                $driver->update(['approve'=>1,'reason' => null]);
    
                $this->database->getReference('drivers/driver_' . $driver->id)
                ->update(['approve' => 1, 'updated_at' => Database::SERVER_TIMESTAMP]);
                SendAccountApprovedMailNotification::dispatch($driver);


                return response()->json([
                    'status' => 'success',
                    'message' => 'Driver document approved successfully.',
                    'allDocumentsApproved'=>$allDocumentsApproved,
                ]);
        
           }
    
            // $title = custom_trans('driver_approved', [], $driver->user->lang);
            // $body = custom_trans('driver_approved_body', [], $driver->user->lang);
        
            // dispatch(new SendPushNotification($driver->user, $title, $body));

             
            $notification = \DB::table('notification_channels')
            ->where('topics', 'Driver Account Approval') // Match the correct topic
            ->first();    
            
            //   send push notification 
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

                 //   send email account approved
            // SendAccountApprovedMailNotification::dispatch($driver);
            return response()->json([
                'status' => 'success',
                'message' => 'Driver document approved successfully.',
                'allDocumentsApproved'=>$allDocumentsApproved,
            ]);
        }else{

            $allDocumentsDisapproved = $documentStatuses->contains(5);
    
            if ($allDocumentsDisapproved){
            $driver->update(['approve'=>0]);
    
            $this->database->getReference('drivers/driver_' . $driver->id)
            ->update(['approve' => 0, 'updated_at' => Database::SERVER_TIMESTAMP]);
    

            // $title = custom_trans('driver_declined_title', [], $driver->user->lang);
            // $body = custom_trans('driver_declined_body', [], $driver->user->lang);
        
            // dispatch(new SendPushNotification($driver->user, $title, $body));  
            $notification = \DB::table('notification_channels')
            ->where('topics', 'Driver Account Disapproval') // Match the correct topic
            ->first();    
        
            //   send push notification 
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

                // send mail account disapprove
                SendAccountDisapprovedMailNotification::dispatch($driver);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Driver document Disapproved.',
                    'allDocumentsDisapproved'=>$allDocumentsDisapproved
                ]);
            }
        }


    
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Driver document approved successfully.'
        // ]);
    }
    public function updateAndApprove(Driver $driverId)
    {
        $documentStatuses = $driverId->driverDocument->pluck('document_status');

         // Handle the case where no document statuses exist
         if ($documentStatuses->isEmpty()) {           
            return response()->json(['message' => 'No documents found. Update not performed.']);
        }
       
        $allDocumentsApproved = $documentStatuses->every(function ($value) {
            return $value == 1;
        });
        // dd($allDocumentsApproved);
        if ($allDocumentsApproved)
        {
            $driverId->update(['approve'=>1]);

            return response()->json([
                'successMessage' => 'Driver  Approved successfully',
            ]);

        }else{
            // dd("Else ");

            return response()->json([
                'failureMessage' => 'Please Upload All Documents',
            ]);

        }
// dd($driverId);

    }

    public function destroyDriverDocument(DriverNeededDocument $driverNeededDocument)
    {
        
        $driverNeededDocument->delete();

        return response()->json([
            'successMessage' => 'Driver Document deleted successfully',
        ]);
    }

    public function viewProfile(Driver $driver) 
    {
        // dd($driver);
        $disable_options = false;

        if(auth()->user()->hasRole('owner'))
        {
            $disable_options = true;
        }


        $firebaseSettings = [
            'firebase_api_key' => get_firebase_settings('firebase_api_key'),
            'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
            'firebase_database_url' => get_firebase_settings('firebase_database_url'),
            'firebase_project_id' => get_firebase_settings('firebase_project_id'),
            'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
            'firebase_app_id' => get_firebase_settings('firebase_app_id'),
        ];

        $currency = $driver->user->countryDetail()->pluck('currency_symbol');

        $driver_date = $driver->getConvertedCreatedAtAttribute();

        $driver_wallet = $driver->driverWallet;

        $completed_ride_count = $driver->requestDetail->where('is_completed', 1)->count();

        $canceled_ride_count = $driver->requestDetail->where('is_cancelled', 1)->count();


        $accepted_count = $driver->requestDetail()->count();
        $rejected_count = $driver->rejectedRequestDetail()->count();

        $total_rides = $accepted_count + $rejected_count;

        $acceptance_rate = 0;
        $cancellation_rate = 0;
        if($total_rides > 0) {
            $acceptance_rate = round(($accepted_count / $total_rides * 100),2);
            $cancellation_rate = round(($rejected_count / $total_rides * 100),2);
        }
// totaltrips
        // Fetch the data

        $today = Carbon::today()->toDateString();

        $trip_data = RequestModel::companyKey()
            ->where('driver_id', $driver->id)
            ->selectRaw('
                IFNULL(SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END), 0) AS completed,
                IFNULL(SUM(CASE WHEN is_cancelled = 1 THEN 1 ELSE 0 END), 0) AS cancelled,
                IFNULL(SUM(CASE WHEN is_completed = 1 AND DATE(created_at) = ? THEN 1 ELSE 0 END), 0) AS completed_today,
                IFNULL(SUM(CASE WHEN is_cancelled = 1 AND DATE(created_at) = ? THEN 1 ELSE 0 END), 0) AS cancelled_today
            ', [$today, $today])
            ->first();
// dd($trip_data);

        //query params

        $cardEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=0 AND DATE(requests.created_at) = '$today', request_bills.total_amount, 0)), 0)";
        $cashEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=1 AND DATE(requests.created_at) = '$today', request_bills.total_amount, 0)), 0)";
        $walletEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=2 AND DATE(requests.created_at) = '$today', request_bills.total_amount, 0)), 0)";
        $adminCommissionQuery = "IFNULL(SUM(request_bills.admin_commision_with_tax), 0)";
        $driverCommissionQuery = "IFNULL(SUM(request_bills.driver_commision), 0)";
        
        $totalEarningsQuery = "$cardEarningsQuery + $cashEarningsQuery + $walletEarningsQuery";
        
        // Today's Earnings
        $todayCardEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=0 AND DATE(requests.created_at) = '$today', request_bills.total_amount, 0)), 0)";
        $todayCashEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=1 AND DATE(requests.created_at) = '$today', request_bills.total_amount, 0)), 0)";
        $todayWalletEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=2 AND DATE(requests.created_at) = '$today', request_bills.total_amount, 0)), 0)";
        $todayTotalEarningsQuery = "$todayCardEarningsQuery + $todayCashEarningsQuery + $todayWalletEarningsQuery";
        
        // Overall Earnings
        $overallCardEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=0, request_bills.total_amount, 0)), 0)";
        $overallCashEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=1, request_bills.total_amount, 0)), 0)";
        $overallWalletEarningsQuery = "IFNULL(SUM(IF(requests.payment_opt=2, request_bills.total_amount, 0)), 0)";
        $overallTotalEarningsQuery = "$overallCardEarningsQuery + $overallCashEarningsQuery + $overallWalletEarningsQuery";
        
        $earnings_data = RequestModel::leftJoin('request_bills', 'requests.id', '=', 'request_bills.request_id')
            ->selectRaw("
                {$todayCardEarningsQuery} AS today_card,
                {$todayCashEarningsQuery} AS today_cash,
                {$todayWalletEarningsQuery} AS today_wallet,
                {$todayTotalEarningsQuery} AS today_total,
                {$overallCardEarningsQuery} AS overall_card,
                {$overallCashEarningsQuery} AS overall_cash,
                {$overallWalletEarningsQuery} AS overall_wallet,
                {$overallTotalEarningsQuery} AS overall_total,
                {$adminCommissionQuery} as admin_commission,
                {$driverCommissionQuery} as driver_commission
            ")
            ->companyKey()
            ->where('requests.is_completed', true)
            ->where('driver_id', $driver->id)
            ->first();

//  dd($earnings_data);           

                            $startDate = Carbon::now()->startOfYear(); // Start of the current year (January 1st)
                            $endDate = Carbon::now();
                            $earningsChartData=[];
                            $months = [];
                       
                            while ($startDate->lte($endDate))
                            {
                                $from = Carbon::parse($startDate)->startOfMonth();
                                $to = Carbon::parse($startDate)->endOfMonth();
                                $shortName = $startDate->shortEnglishMonth;
                                $monthName = $startDate->monthName;
                            
                                // Collect cancel data directly into arrays
                                $months[] = $shortName;
                             
                                $earningsChartData['earnings']['months'][] = $monthName;
                                $earningsChartData['earnings']['values'][] = RequestBill::whereHas('requestDetail', function ($query) use ($from,$to,$driver) {
                                                    $query->where('driver_id', $driver->id)->whereBetween('trip_start_time', [$from,$to])->whereIsCompleted(true);
                                                })->sum('total_amount');

                                // Collect data directly into arrays
                                $tripsChartData['months'][] = $shortName;
                                $tripsChartData['completed'][] = RequestModel::whereBetween('trip_start_time', [$from, $to])
                                    ->whereIsCompleted(true)->where('driver_id', $driver->id)
                                    ->count();
                                $tripsChartData['cancelled'][] = RequestModel::whereBetween('trip_start_time', [$from, $to])
                                    ->where('driver_id', $driver->id)
                                    ->whereIsCancelled(true)
                                    ->count();

                
                                $startDate->addMonth();
                            }

        if(get_map_settings('map_type') == "open_street_map"){
            return Inertia::render('pages/fleet_drivers/approved_drivers/open-view_profile',[
                'driver'=>$driver,
                'driver_date'=>$driver_date, 
                'currency'=>$currency,
                'app_for'=>env("APP_FOR"),
                'completed_ride_count'=>$completed_ride_count,
                'canceled_ride_count'=>$canceled_ride_count,
                'default_lat'=>get_settings('default_latitude'),
                'default_lng'=>get_settings('default_longitude'),
                'tripsChartData' => $tripsChartData,
                'acceptance_rate'=>$acceptance_rate,
                'cancellation_rate'=>$cancellation_rate,
                'trip_data'=>$trip_data,
                'earnings_data'=>$earnings_data,
                'disable_options'=>$disable_options,
                'earningsChartData' => [
                    'months' => $months,
                    'values' => $earningsChartData['earnings']['values'],
                ],
                'firebaseSettings'=>$firebaseSettings,
            ]);

        }

        $map_key = get_map_settings('google_map_key');
        // dd($tripsChartData);

        return Inertia::render('pages/fleet_drivers/approved_drivers/view_profile',
            [
                'driver'=>$driver,
                'driver_date'=>$driver_date, 
                'map_key'=>$map_key, 
                'currency'=>$currency,
                'app_for'=>env("APP_FOR"),
                'completed_ride_count'=>$completed_ride_count,
                'canceled_ride_count'=>$canceled_ride_count,
                'tripsChartData' => $tripsChartData,
                'default_lat'=>get_settings('default_latitude'),
                'default_lng'=>get_settings('default_longitude'),
                'trip_data'=>$trip_data,
                'acceptance_rate'=>$acceptance_rate,
                'cancellation_rate'=>$cancellation_rate,
                'earnings_data'=>$earnings_data,
                'disable_options'=>$disable_options,
                'earningsChartData' => [
                    'months' => $months,
                    'values' => $earningsChartData['earnings']['values'],
                ],
                'firebaseSettings'=>$firebaseSettings,
            ]);
    }

}
