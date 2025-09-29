<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Models\User;
use App\Models\Admin\Fleet;
use App\Models\Admin\Driver;
use App\Models\Admin\Owner;
use App\Models\Admin\VehicleType;
use App\Models\Admin\FleetNeededDocument;
use App\Models\Admin\FleetDocument;
use App\Base\Filters\Admin\FleetFilter;
use App\Base\Constants\Masters\DriverDocumentStatus;
use App\Base\Constants\Auth\Role;
use App\Models\Admin\ServiceLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\BaseController;
use Kreait\Firebase\Contract\Database;
use DB;
use Log;
use Carbon\Carbon;
use App\Jobs\Notifications\SendPushNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\FleetDriverApprovedMail;
use App\Mail\FleetDriverDisapproveMail;

class ManageFleetController extends BaseController
{
    protected $imageUploader;
    protected $fleet;
    protected $database;

    /**
     * DriverDocumentController constructor.
     *
     * @param ImageUploaderContract $imageUploader
     */
    public function __construct(ImageUploaderContract $imageUploader, fleet $fleet)
    {
        $this->imageUploader = $imageUploader;
        $this->fleet = $fleet;
    }
    public function index() {
        $location = ServiceLocation::active()->get();

        $owners = Owner::where('approve', true)->get();

        if (auth()->user()->hasRole('owner')) {
            // Retrieve the specific owner associated with the authenticated user
            $owners = auth()->user()->owner()->get();

            $location = auth()->user()->owner->area()->get();
        }
        // dd($location);

        return Inertia::render('pages/manage_fleet/index',[
            'serviceLocations' => $location,
            'owners' => $owners,
        ]);
    }

    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = Fleet::query()->orderBy('created_at','DESC');

        if(auth()->user()->hasRole('owner'))
        {
          
            $query = Fleet::where('owner_id', auth()->user()->id);

        }
// dd($owner);

        $results = $queryFilter->builder($query)->customFilter(new FleetFilter)->paginate();
// dd($results);
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {
        $owners = Owner::where('approve', true)->get();
    
        if (auth()->user()->hasRole('owner')) {
            // Retrieve the specific owner associated with the authenticated user
            $owners = auth()->user()->owner()->get();
        }
    
        $types = VehicleType::active()->get();
        return Inertia::render('pages/manage_fleet/create', [
            'owners' => $owners,
            'types' => $types
        ]);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'vehicle_type' => 'required',
            'custom_make' => 'required',
            'custom_model' => 'required',
            'car_color' => 'required',
            'license_number' => 'required',
        ]);
        $owner = Owner::where('user_id', $request->owner_id)->first();
       
        $validated['owner_id']=$owner->user_id;

        $validated['fleet_id']='';
        $validated['permission_number']='';


        $results = Fleet::create($validated);

        // dd($results);

        return response()->json([
            'results' => 'Fleet Created Successfully',
        ],201);
    }
    public function edit(Fleet $fleet) {
        $types = VehicleType::active()->get();
        $owners = Owner::where('approve', true)->get();
        $owner = User::find($fleet->owner_id); // Fetch the owner from the users table
    
        return Inertia::render('pages/manage_fleet/create', [
            'owners' => $owners,
            'types' => $types,
            'fleet' => $fleet,
            'selectedOwner' => $owner // Pass the owner to the front end
        ]);
    }
    
    public function update(Fleet $fleet, Request $request) {
        $validated = $request->validate([
            'owner_id' => 'required',
            'vehicle_type' => 'required',
            'custom_make' => 'required',
            'custom_model' => 'required',
            'car_color' => 'required',
            'license_number' => 'required',
        ]);
        $fleet->update($validated);
        return response()->json([
            'successMessage' => 'Fleet Updated Successfully',
        ],201);
    }
    public function delete(Fleet $fleet) {
        $fleet->delete();
        return response()->json([
            'successMessage' => 'Fleet Deleted Successfully',
            'serviceLocations' =>ServiceLocation::active()->get(),
        ],201);
    }

    // Fleet  Needed Document Management ---------------------------------------------------------------------------
    public function fleetNeededDocumentIndex()
    {
        return Inertia::render('pages/fleet_needed_documents/index');
    }

    public function fleetNeededDocumentList(QueryFilterContract $queryFilter,) {
        $query = FleetNeededDocument::query();
        $results = $queryFilter->builder($query)->paginate();
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function fleetNeededDocumentCreate()
    {
        return Inertia::render('pages/fleet_needed_documents/create');
    }

    public function fleetNeededDocumentStore(Request $request) 
    {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required',
            'has_identify_number' => 'required',
            'has_expiry_date' => 'required',
        ]);

        $validated['active'] = true;
        $validated['image_type'] = $request->image_type;
        $validated['document_name_front'] = $request->document_name_front;
        $validated['document_name_back'] = $request->document_name_back;

        $validated['is_editable'] = $request->is_editable;
        $validated['is_required'] = $request->is_required;


        if($request->has_identify_number){
            $validated['identify_number_locale_key'] = $request->identify_number_locale_key;
        }
        $document = FleetNeededDocument::create($validated);
        return response()->json([
            'successMessage' => 'Document created successfully.',
            'result' => $document,
        ],201);

    }
    public function fleetNeededDocumentEdit(FleetNeededDocument $document,Request $request) 
    {
        // dd($document);
        return Inertia::render('pages/fleet_needed_documents/create',['document'=>$document]);
    }
    public function fleetNeededDocumentUpdate(FleetNeededDocument $document,Request $request) {
        // dd($document);
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        $validated = $request->validate([
            'name' => 'required',
            'has_identify_number' => 'required',
            'has_expiry_date' => 'required',
        ]);
        $validated['image_type'] = $request->image_type;
        $validated['document_name_front'] = $request->document_name_front;
        $validated['document_name_back'] = $request->document_name_back;

        $validated['is_editable'] = $request->is_editable;
        $validated['is_required'] = $request->is_required;



        if($request->has_identify_number){
            $validated['identify_number_locale_key'] = $request->identify_number_locale_key;
        }
        $document->update($validated);
        // dd($validated);

        return response()->json([
            'successMessage' => 'Document Updated successfully.',
            'result' => $document,
        ],201);
    }
    public function fleetNeededDocumentToggle(Request $request) 
    {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        FleetNeededDocument::where('id',$request->id)->update(['active'=>$request->status]);
        return response()->json([
            'successMessage' => 'Document Status updated successfully.',
        ],201);
    }
    public function fleetNeededDocumentDelete(FleetNeededDocument $document) {
        // dd($document);
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        $document->delete();
        return response()->json([
            'successMessage' => 'Document Deleted successfully.',
        ],201);
    }


    //  Fleet Documents
    public function document(Fleet $fleet) 
    {

        // Fetch uploaded documents
        $fleetDocuments = $fleet->fleetDocument ?: collect(); // Default to empty collection if null
        $fleetDocuments = $fleetDocuments->keyBy('document_id'); // Key by document_id for easy lookup
    
        // Fetch required documents
        $fleetNeededDocuments = FleetNeededDocument::where('active', true)->get();
    
        // Merge data
        $documents = $fleetNeededDocuments->map(function ($doc) use ($fleetDocuments) {
            $uploadedDoc = $fleetDocuments->get($doc->id);
            
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'doc_type' => $doc->doc_type,
                'has_identify_number' => $doc->has_identify_number,
                'has_expiry_date' => $doc->has_expiry_date,
                'active' => $doc->active,
                'identify_number_locale_key' => $doc->identify_number_locale_key,
                'uploaded' => $uploadedDoc ? true : false,
                'expiry_date' => $uploadedDoc->expiry_date ?? null,
                'identify_number' => $uploadedDoc->identify_number ?? null,
                'document_status' => $uploadedDoc->document_status ?? null,
                'comment' => $uploadedDoc->comment ?? null,
                'image' => $uploadedDoc->image ?? null,
                'back_image' => $uploadedDoc->back_image ?? null,
            ];
        });

        // dd($documents);
    
        return Inertia::render('pages/manage_fleet/document', [
            'documents' => $documents,
            'fleetId' => $fleet->id,
        ]);

    }
    public function documentUpload(FleetNeededDocument $document, Fleet $fleetId)
    {
        $uploaded = $fleetId->fleetDocument()->where('document_id', $document->id)->first();

// dd($uploaded);
    return Inertia::render('pages/manage_fleet/document_upload',['fleetId'=>$fleetId,
    'uploaded'=>$uploaded, 'document'=>$document,]);

    }
    public function documentUploadStore(Request $request, FleetNeededDocument $document, Fleet $fleetId,)
    {

        // dd($request->all());
        $created_params = $request->only(['identify_number']);

        $created_params['fleet_id'] = $fleetId->id;
        $created_params['document_id'] = $document->id;

        $created_params['name'] = $document->name;


        $created_params['expiry_date'] = null;


        if($request->expiry_date!=null)
        {
            $expiry_date = Carbon::parse($request->expiry_date)->toDateTimeString();

            $created_params['expiry_date'] = $expiry_date;
        }



        if($request->hasFile('image'))
        {
            if ($uploadedFile = $this->getValidatedUpload('image', $request)) {
                $created_params['image'] = $this->imageUploader->file($uploadedFile)
                    ->saveFleetDocument($fleetId->id);
            }
        }
        if($request->hasFile('back_image'))
        {
            if ($uploadedFile = $this->getValidatedUpload('back_image', $request)) {
                $created_params['back_image'] = $this->imageUploader->file($uploadedFile)
                    ->saveFleetDocument($fleetId->id);
            }
        }

       // Check if document exists
        $fleet_documents = FleetDocument::where('fleet_id', $fleetId->id)->where('document_id', $document->id)->first();

        $fleetId->update(['status'=>3]);

        if ($fleet_documents) {
            $created_params['document_status'] = DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL;
            FleetDocument::where('fleet_id', $fleetId->id)->where('document_id', $document->id)->update($created_params);
        } else {
            $created_params['document_status'] = DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL;
            FleetDocument::create($created_params);
        }


        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Fleet Document uploaded successfully.',
                'fleetId'=>$fleetId,
                'document'=>$document
                ], 201);

    }


    public function approveDocumentStatus($fleet)
    {
        $neededDoc = FleetNeededDocument::active()->count();
        $fleets = Fleet::with('fleetDocument')->find($fleet);
        $uploadedDoc = $fleets->fleetDocument()
            ->whereIn('document_status', [1])
            ->count();

            if($neededDoc != $uploadedDoc){
                return response()->json([
                    'status' => 'failure',
                    'message' => 'Driver document Disapproved.',
                    'data' =>'uploaddocument'
                ]);            
            } 
            
            $fleets->update([
                'approve'=>1,
                'reason' =>Null
            ]);
    
            // $title = custom_trans('driver_approved', [], $driver->user->lang);
            // $body = custom_trans('driver_approved_body', [], $driver->user->lang);
        


            $notification = \DB::table('notification_channels')
            ->where('topics', 'Fleet Approved') // Match the correct topic
            ->first();

            // send push notification 
            if ($notification && $notification->push_notification == 1) {
                // Determine the user's language or default to 'en'
                $userLang = $fleet->ownerDetail->lang ?? 'en';
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
                dispatch(new SendPushNotification($fleets->ownerDetail, $title, $body));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Driver document Approved.',
            ]);
            
    

    }
    public function approvFleetDocument($documentId,$fleetId,$status)
    {
        // dd($status);
        // $fleetDoc = fleetDocument::where('fleet_id', $fleetId)->where('document_id', $documentId)->first();
        // $fleetDoc->update(['document_status' => $status]);

        // return 'success';


        $fleet = Fleet::find($fleetId);

        $fleetDoc = FleetDocument::where('fleet_id', $fleetId)->where('document_id', $documentId)->first();

        if (!$fleetDoc) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Document not found for the given Fleet.'
            ], 404); // Return a 404 status code for better semantics
        }

        $fleetDoc->update(['document_status' => $status]);


        $documentStatuses = $fleet->fleetDocument->pluck('document_status');
        $neededDoc = FleetNeededDocument::active()->count();
        $uploadedDoc = count($fleet->fleetDocument);

        if( $neededDoc != $uploadedDoc){
            return redirect('manage-fleet/document/'.$fleet->id);
        }
        if($status==1)
        {
            $is_required = FleetNeededDocument::active()->where('is_required', true)->pluck("id");
            $documentStatus = FleetDocument::whereIn('document_id',$is_required)->pluck('document_status');
       
            $allDocumentsApproved = $documentStatus->every(function ($value) {
                return $value == 1;
            });
            if ($allDocumentsApproved)
            {
                $fleet->update(['approve'=>1]);
    
                // $title = custom_trans('fleet_approved_title', [], $fleet->ownerDetail->lang);
                // $body = custom_trans('fleet_approved_body', [], $fleet->ownerDetail->lang);
            
                // dispatch(new SendPushNotification($fleet->ownerDetail, $title, $body));  
                
                $notification = \DB::table('notification_channels')
                ->where('topics', 'Fleet Approved') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $fleet->ownerDetail->lang ?? 'en';
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
                    dispatch(new SendPushNotification($fleet->ownerDetail, $title, $body));
                }

                // return redirect()->route('managefleets.index');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Fleet document approved successfully.',
                    'allDocumentsApproved'=>$allDocumentsApproved,
                ]);
            }
    
        }else{

            $allDocumentsDisapproved = $documentStatuses->contains(5);
    
            if ($allDocumentsDisapproved){
                $fleet->update(['approve'=>0]);
        

                // $title = custom_trans('fleet_declined_title', [], $fleet->ownerDetail->lang);
                // $body = custom_trans('fleet_declined_body', [], $fleet->ownerDetail->lang);
            
                // dispatch(new SendPushNotification($fleet->ownerDetail, $title, $body));

                 $notification = \DB::table('notification_channels')
                ->where('topics', 'Fleet Decline') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $fleet->ownerDetail->lang ?? 'en';
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
                    dispatch(new SendPushNotification($fleet->ownerDetail, $title, $body));
                }
                // return redirect()->route('managefleets.index');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Fleet document Disapproved.',
                    'allDocumentsDisapproved'=>$allDocumentsDisapproved
                ]);
            }
        }


    
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Owner document approved successfully.'
        // ]);


    }
    public function approve(Fleet $fleet,Database $database,Request $request) {


        // dd("dfkf");
        $fleet->update(['approve'=>false]);

        return response()->json([
            'successMessage' => 'Fleet disapproved successfully',
            'results' => $fleet,
        ],201);
    }
    public function updateAndApprove(Fleet $fleetId)
    {
        // dd($fleetId);

        $documentStatuses = $fleetId->fleetDocument->pluck('document_status');

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
            $fleetId->update(['approve'=>1,'status'=>1]);

            return response()->json([
                'successMessage' => 'Owner  Approved successfully',
            ]);

        }else{
            // dd("Else ");

            $fleetId->update(['approve'=>0,'status'=>5]);


            return response()->json([
                'failureMessage' => 'Please Upload All Documents',
            ]);

        }
// dd($fleetId);
    }
    public function fleetPaymentHistory() 
    {
        return Inertia::render('pages/manage_fleets/fleet_payment_history');
    }

    public function listFleetDrivers(Fleet $fleet)
    {
        $owner = $fleet->ownerDetail->owner;
        // dd($owner);

        $drivers = Driver::where('owner_id', $owner->id)->get();
        // dd($drivers);
        return response()->json([
            'successMessage' => 'Fleet Drivers Listed Successfully',
            'drivers' => $drivers,
        ],201);
    }
    public function assignDriver(Request $request,Fleet $fleet, Driver $driver)
    {
        // dd($driver);

        if($fleet->driver_id===$driver->id){
            return response()->json([
                'successMessage' => 'Driver Assigned Successfully',
            ],201);
        }

        if ($driver->fleet_id != null) {
            if ($driver->fleet_id != null) {
                $driver->update([
                    'fleet_id'=>null,
                    'vehicle_type'=>null,
                    'car_make' => null,
                    'car_model' => null,
                    'car_color' => null,
                    'custom_make'=>null,
                    'custom_model'=>null,
                ]);
            }
        }
        // dd($fleet->driverDetail);

        if($fleet->driverDetail){
            $last_driver = $fleet->driverDetail;

            // $title = custom_trans('fleet_removed_from_your_account_title',[],$last_driver->user->lang);
            // $body = custom_trans('fleet_removed_from_your_account_body',[],$last_driver->user->lang);

            $this->database->getReference('drivers/'.'driver_'.$last_driver->id)->update(['fleet_changed'=>1,'updated_at'=> Database::SERVER_TIMESTAMP]);

            $notifiable_driver = $last_driver->user;
            // dispatch(new SendPushNotification($notifiable_driver,$title,$body));

             $notification = \DB::table('notification_channels')
                ->where('topics', 'Fleet Account Removed') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $last_driver->user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($notifiable_driver, $title, $body));
                }

            $last_driver->update([
                'fleet_id'=>null,
                'vehicle_type'=>null,
                'car_make' => null,
                'car_model' => null,
                'car_color' => null,
                'custom_make'=>null,
                'custom_model'=>null,
            ]);
        }

        $fleet->fresh();

        if($driver->fleetDetail){

            $driver->fleetDetail()->update(['driver_id'=>null]);

        }

        $driver->fresh();

        $fleet->update(['driver_id'=>$driver->id]);

        $driver->update([
            'fleet_id'=> $fleet->id,
            'car_color' => $fleet->car_color,
            'custom_make'=>$fleet->custom_make,
            'custom_model'=>$fleet->custom_model,
            'vehicle_type' => $fleet->vehicle_type,
        ]);
        $notifiable_driver = $driver->user;
        // $title = custom_trans('new_fleet_assigned_title',[],$notifiable_driver->lang);
        // $body = custom_trans('new_fleet_assigned_body',[],$notifiable_driver->lang);

        // dispatch(new SendPushNotification($notifiable_driver,$title,$body));

        $notification = \DB::table('notification_channels')
                ->where('topics', 'New Fleet Assigned') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $notifiable_driver->lang ?? 'en';
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
                    dispatch(new SendPushNotification($notifiable_driver, $title, $body));
                }

        $this->database->getReference('drivers/driver_'.$driver->id)->update(['fleet_changed'=>1,'updated_at'=> Database::SERVER_TIMESTAMP]);

        return response()->json([
            'successMessage' => 'Driver Assigned Successfully',
        ],201);
    }
}
