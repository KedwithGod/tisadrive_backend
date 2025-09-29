<?php

namespace App\Http\Controllers;
use App\Models\Admin\VehicleType;
use Inertia\Inertia;
use App\Models\Admin\ServiceLocation;
use Illuminate\Http\Request;
use App\Models\Admin\DriverNeededDocument;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use App\Models\Admin\Driver;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Admin\Setting;
use App\Models\User;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\DriverFilter;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin\DriverLevelUp;
use App\Models\Admin\DriverDocument;
use App\Models\Payment\DriverWalletHistory;
use App\Base\Constants\Masters\DriverDocumentStatus;
use Carbon\Carbon;
use App\Models\Request\Request as RequestModel;
use App\Base\Filters\Admin\RequestFilter;
use App\Base\Filters\Admin\UserFilter;
use App\Models\Request\RequestBill;
use App\Transformers\User\UserTransformer;
use App\Models\Payment\WalletWithdrawalRequest;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Payment\WalletWithdrawalRequestsTransformer;
use App\Jobs\Notifications\SendPushNotification;
use App\Base\Constants\Masters\PushEnums;
use Kreait\Firebase\Contract\Database;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Method;
use Illuminate\Support\Facades\Mail;
use App\Mail\DriverApprovedMail;
use App\Mail\DriverDisapproveMail;
use App\Mail\DriverWithdrawalApproveMail;
use App\Mail\DriverWithdrawalDisapproveMail;
use App\Mail\DriverWalletAddAmountMail;
use App\Transformers\Payment\DriverWalletHistoryTransformer;
use App\Jobs\Mails\SendAccountApprovedMailNotification;
use App\Jobs\Mails\SendAccountDisapprovedMailNotification;
use App\Jobs\Mails\SendDriverWalletAmountMailNotification;
use App\Jobs\Mails\SendDriverWithdrawalAcceptMailNotification;
use App\Jobs\Mails\SendDriverWithdrawalDeclineMailNotification;
use App\Models\Admin\ZoneType;

class DriverManagementController extends BaseController
{

    protected $imageUploader;
  
    protected $driver;

    protected $user;

    protected $database;


    public function __construct(ImageUploaderContract $imageUploader, Driver $driver, User $user,Database $database)
    {
        $this->imageUploader = $imageUploader;
        $this->driver = $driver;
        $this->database = $database;

    }

    public function approvedDriverIndex() 
    {
        $types = VehicleType::active()->get();
        $serviceLocations = ServiceLocation::active()->get();
        return Inertia::render('pages/approved_drivers/index',['types' => $types,'app_for'=>env("APP_FOR"),'serviceLocations'=>$serviceLocations]);
    }

    public function list(QueryFilterContract $queryFilter)
    {
        
        $columns = \Schema::getColumnListing('drivers'); // Get all columns
        $filteredColumns = array_diff($columns, ['route_coordinates']); // Exclude route_coordinates

        $query = Driver::whereNull('owner_id')
        ->select($filteredColumns)
        ->orderBy('created_at', 'DESC');

        // $query = Driver::whereNull('owner_id')->orderBy('created_at','DESC');

        $results =  $queryFilter->builder($query)->customFilter(new DriverFilter())->paginate();

        $drivers = $results->items();

        // Manually attach referred_by_name
        foreach ($drivers as $driver) {
            $drivers = $driver->user;
            if ($drivers->referred_by) {
                $refUser = User::find($drivers->referred_by);
                $drivers->referred_by_name = $refUser ? $refUser->name : null;
            } else {
                $drivers->referred_by_name = null;
            }
        }

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }    
    public function create() 
    {
        $serviceLocations = ServiceLocation::active()->get();
        $vehicleTypes = VehicleType::where('active', true)->get();  
        $driver = null;
        
        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();

        $selected_vehicle_type[] =null;
        // dd($default_country->flag);

        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;

        return Inertia::render('pages/approved_drivers/create', [
            'serviceLocations'=> $serviceLocations,
            'vehicleTypes'=> $vehicleTypes,
            'driver'=> $driver,
            'selected_vehicle_type'=>$selected_vehicle_type,
            'countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,
            'default_flag'=>$default_flag,
            'default_country_id'=>$default_country_id,
        ]);
    }
    public function store(Request $request)
    {
        // Validate the incoming request
        $created_params = $request->validate([
            'name' => 'required',
            'service_location_id' => 'required',
            'vehicle_type' => 'required',
            'car_color' => 'required',
            'car_number' => 'required',
            'transport_type' => 'required',
            'country' => 'required',
            'mobile'=>'required|mobile_number|min:8',
            'email' => 'required',
            'gender' => 'required',
            // 'profile_picture' => 'required'
        ]);
    
        $created_params['custom_make'] = $request->vehicle_make;
        $created_params['custom_model'] = $request->vehicle_model;  


        // Hash the password
        $created_params['password'] = bcrypt($request->input('password'));
    
        // Set other parameters
        $created_params['active'] = true;
      
        // Handle the profile picture upload
        // if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
        //     $profile_picture = $this->imageUploader->file($uploadedFile)->saveProfilePicture();
        // }
        if ($uploadedFile = $request->file('profile_picture')) {
            $profile_picture = $this->imageUploader->file($uploadedFile)
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
       
            // Create the User
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'mobile_confirmed' => true,
                'password' => bcrypt($request->input('password')),
                'refferal_code' => str_random(6),
                'country' => $request->input('country'),
                'profile_picture' => $profile_picture,

            ]);
            

        // Set the user_id in created_params
        $created_params['user_id'] = $user->id;

        // Create a new Driver (don't manually set the 'id' field)
        $driver = Driver::create($created_params);

        $driver->driverWallet()->create(['amount_added'=>0]);
        $driver->user->rewardPoint()->create(['amount'=>0]);


       // Create drivers vehicle data 
       if($request->has('vehicle_type'))
       { 
               $driver->driverVehicleTypeDetail()->create(['vehicle_type'=>$request->vehicle_type]); 
       }   
        // Assign the role
        $user->attachRole('driver');
    
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver created successfully.',
            'driver' => $driver,
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

    public function edit($id)
    {
        $serviceLocations = ServiceLocation::active()->get();
        $vehicleTypes = VehicleType::where('active', true)->get();  
        $driver = Driver::find($id);
        $selected_vehicle_type = $driver->driverVehicleTypeDetail()->pluck('vehicle_type');


        $user = $driver->user;


        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = $driver->user->countryDetail()->first();

        // dd($driver->user->profile_picture);

        $default_dial_code = $driver->user->countryDetail->dial_code;
        $default_country_id = $driver->user->countryDetail->id;
        $default_flag = $driver->user->countryDetail->flag;

// dd($driver->getProfilePictureAttribute());
        return Inertia::render(
            'pages/approved_drivers/create', ['countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,
            'driver'=>$driver,
            'serviceLocations'=> $serviceLocations,
            'vehicleTypes'=> $vehicleTypes,
            'default_flag'=>$default_flag,
            'user'=>$user,
            'app_for'=>env("APP_FOR"),
            'selected_vehicle_type'=>$selected_vehicle_type,
            'default_country_id'=>$default_country_id]
        );
    }



    public function update(Request $request, Driver $driver) 
    {
        // Validate the incoming request
        $updated_params = $request->validate([
            'name' => 'required',
            'service_location_id' => 'required',
            'vehicle_type' => 'required',
            'car_color' => 'required',
            'car_number' => 'required',
            'transport_type' => 'required',
            'country' => 'required',
            'mobile'=>'required|mobile_number|min:8',
            'email' => 'required',
            'gender' => 'required',
        ]);
        
        $updated_params['custom_make'] = $request->vehicle_make;
        $updated_params['custom_model'] = $request->vehicle_model;
    
        $user_params = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'mobile_confirmed' => true,
            'country' => $request->input('country'),
        ];
    
        if ($request->filled('password')) {
            $user_params['password'] = bcrypt($request->input('password'));
        }
    
        if ($uploadedFile = $request->file('profile_picture')) {
            $user_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
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
    
        // Update user data
        $driver->user->update($user_params);
    
        // Update driver data
        $driver->update($updated_params);
    
        // Update driver's vehicle type detail
        if ($request->has('vehicle_type')) { 
            $driver->driverVehicleTypeDetail()->update(['vehicle_type' => $request->vehicle_type]); 
        }
    
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver updated successfully.',
            'driver' => $driver,
        ], 201);
    }

    public function editPassword($id)
    {
        // You don't need to fetch other user information here, just the password fields
        $driver = Driver::find($id);
        $user = $driver->user;
        // Return the edit password page
        return Inertia::render(
            'pages/approved_drivers/edit', ['user' => $user, 'driver'=>$driver,]
        );
    }

    public function updatePasswords(Request $request, Driver $driver)
    {
        // Validate the password and confirmation
        $updated_params = $request->validate([
            'password' => 'required|min:8',  // Confirmed is for password_confirmation
            'confirm_password' => 'required|same:password',
        ]);
        $user_params = [
            'password' => $request->input('password'),
            'confirm_password' => $request->input('confirm_password'),
        ];

        if ($request->filled('password')) {
            $user_params['password'] = bcrypt($request->input('password'));
        }

        $driver->user->update($user_params);

        // Update the password
        $driver->update([
            'password' => bcrypt($updated_params['password']),
        ]);
        return response()->json([
            'successMessage' => 'Password updated successfully.',
            'driver' => $driver,
        ], 201);
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

                $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Account Disapproval') // Match the correct topic
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

                // send email account disapproval
                SendAccountDisapprovedMailNotification::dispatch($driver);

        return response()->json([
            'successMessage' => 'Reason declined.',
            'driver' => $driver,
        ], 201);
    }



    public function disapprove(Driver $driver)
    {
        $driver->update(['approve'=>false]);

        return response()->json([
            'successMessage' => 'Driver disapproved successfully',
            'results' => $driver,
        ],201);
    }   
    public function destroy(Driver $driver)
    {
        // dd($driver);
        $driver->delete();
        $driver->user->delete();
        $this->database->getReference('drivers/driver_' . $driver->id)
                ->remove();

        return response()->json([
            'successMessage' => 'Driver deleted successfully',
        ]);
    }   
    public function approvedDriverViewDocument(Driver $driver)
    {
        // Fetch uploaded documents
        $driverDocuments = $driver->driverDocument ?: collect(); // Default to empty collection if null
        $driverDocuments = $driverDocuments->keyBy('document_id'); // Key by document_id for easy lookup
    
        // Fetch required documents
        $driverNeededDocuments = DriverNeededDocument::where(function ($query) {
            $query->where('account_type', 'individual')
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
    
        return Inertia::render('pages/approved_drivers/document', [
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
            $query->where('account_type', 'individual')
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

        return Inertia::render('pages/approved_drivers/document_upload',['driverId'=>$driverId,
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
        // if($request->hasFile('backImageFile'))
        // {
            if ($uploadedFile = $request->file('back_image')) {
                $created_params['back_image'] = $this->imageUploader->file($uploadedFile)
                    ->saveDriverDocument($driverId->id);
            }
        // }
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
            $query->where('account_type','individual')->orWhere('account_type','both');
        })->count();
        $driver = Driver::with('driverDocument')->find($driver);
        $uploadedDoc = $driver->driverDocument()
            ->whereHas('driverNeededDocuments', function ($query) {
                $query->where('active', true)
                    ->where(function ($query) {
                        $query->where('account_type', 'individual')
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
            $query->where('account_type','individual')->orWhere('account_type','both');          
        })->where('is_required', true)->count();
        $uploadedDoc = $driver->driverDocument()->whereHas('driverNeededDocuments',function($query) {
            $query->where('active',true)->where(function($query) {
                $query->where('account_type','individual')->orWhere('account_type','both');
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
                    $query->where('account_type','individual')->orWhere('account_type','both');
                })->where('is_required', true);
            })->pluck("id");

            // Get statuses of the driver's documents
            $documentStatus = $driver->driverDocument->whereIn('id', $docIds)->pluck('document_status');

            $allDocumentsApproved = $documentStatus->every(function ($value) {
                return $value == 1;
            });
            if ($allDocumentsApproved)
            {
                $driver->update([
                    'approve'=>1,
                    'reason' => null
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
    
                // dispatch(new SendPushNotification($driver->user, $title, $body));
                // return redirect()->route('approveddriver.Index');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Driver document approved successfully.',
                    'allDocumentsApproved'=>$allDocumentsApproved,
                ]);
           }
    
        }else{

            $allDocumentsDisapproved = $documentStatuses->contains(5);
    
            if ($allDocumentsDisapproved)
            {
                $driver->update(['approve'=>0]);
    
                $this->database->getReference('drivers/driver_' . $driver->id)
                ->update(['approve' => 0, 'updated_at' => Database::SERVER_TIMESTAMP]);
        
                // $title = custom_trans('driver_declined_title', [], $driver->user->lang);
                // $body = custom_trans('driver_declined_body', [], $driver->user->lang);

                
                $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Account Disapproval') // Match the correct topic
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

                // send email account disapproval
                SendAccountDisapprovedMailNotification::dispatch($driver);
            
                // dispatch(new SendPushNotification($driver->user, $title, $body));  
                // return redirect()->route('approveddriver.Index');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Driver document Disapproved.',
                    'allDocumentsDisapproved'=>$allDocumentsDisapproved
                ]);
           }
        }


    
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Driver document approved successfully.',
        // ]);
    }
    

    public function viewProfile(Driver $driver) 
    {
        // dd($driver);



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
            return Inertia::render('pages/approved_drivers/open-view_profile',[
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
                'earningsChartData' => [
                    'months' => $months,
                    'values' => $earningsChartData['earnings']['values'],
                ],
                'driver_wallet'=>$driver_wallet,
                'firebaseSettings'=>$firebaseSettings,
            ]);

        }

        $map_key = get_map_settings('google_map_key');
        // dd($tripsChartData);

        return Inertia::render('pages/approved_drivers/view_profile',
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
                'earnings_data'=>$earnings_data,
                'acceptance_rate'=>$acceptance_rate,
                'cancellation_rate'=>$cancellation_rate,
                'earningsChartData' => [
                    'months' => $months,
                    'values' => $earningsChartData['earnings']['values'],
                ],
                'driver_wallet'=>$driver_wallet,
                'firebaseSettings'=>$firebaseSettings,
            ]);
    }


    public function pendingDriverIndex() {
        $types = VehicleType::active()->get();
        $serviceLocations = ServiceLocation::active()->get();
        return Inertia::render('pages/pending_drivers/index',['types'=>$types,'app_for'=>env("APP_FOR"),'serviceLocations'=>$serviceLocations]);
    }

    public function pendingDriverViewDocument()
    {
        return Inertia::render('pages/pending_drivers/document',);
    }


    public function driverRatingIndex() 
    {
        return Inertia::render('pages/drivers_rating/index',['app_for'=>env("APP_FOR"),]);
    }

    public function driverRatingList(QueryFilterContract $queryFilter)
    {
        $query = Driver::query();

        $results =  $queryFilter->builder($query)->customFilter(new DriverFilter())->paginate();
        $items = fractal($results->items(), new DriverProfileTransformer)->toArray();
        $results->setCollection(collect($items['data']));

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);      
    }

    public function viewDriverRating(Driver $driver) 
    {

        return Inertia::render('pages/drivers_rating/view', 
            ['driver'=>$driver,'app_for'=>env("APP_FOR"),]);
    }

    public function driverRatinghistory(Driver $driver, QueryFilterContract $queryFilter)
    {
        // Assuming RequestModel is the correct model for driver ratings
        $query = RequestModel::where('driver_id', $driver->id)->where('user_rated',true)->orderBy('created_at','DESC');
        // Apply filters and paginate
        $results = $queryFilter->builder($query)->customFilter(new RequestFilter())->paginate(); // Adjust page size if needed
    
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    
 //delted request drivers   
    public function deleteRequestDriversIndex() {
        return Inertia::render('pages/delete_request_drivers/index');
    }
    public function deleteRequestList(QueryFilterContract $queryFilter)
    {
        $query = User::whereNotNull('is_deleted_at')->belongsToRole('driver');

        $results =  $queryFilter->builder($query)->customFilter(new UserFilter())->paginate();

        $items = fractal($results->items(), new UserTransformer)->toArray();
        $results->setCollection(collect($items['data']));
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);      
    }
    public function deleteRequestDriversViewDocument() {
        return Inertia::render('pages/delete_request_drivers/document');
    }

    public function deleteRequestDriversuploadDocument() {
        return Inertia::render('pages/approved_drivers/document_upload');
    }
    public function destroyDriver(Driver $driver)
    {
        // dd($driver);
        $driver->delete();
        $driver->user->delete();
        $this->database->getReference('drivers/driver_' . $driver->id)
                ->remove();

        return response()->json([
            'successMessage' => 'Driver deleted successfully',
        ]);
    } 
//Driver Needed Documents
    public function driverNeededDocumentIndex() 
    {
        return Inertia::render('pages/driver_needed_documents/index');
    }
    public function driverNeededDocumentList()
    {
        $results = DriverNeededDocument::paginate();
        // dd($results);

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function driverNeededDocumentCreate()
    {

        return Inertia::render('pages/driver_needed_documents/create',);
    }
    public function driverNeededDocumentStore(Request $request)
    {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        // dd($request->all());
        $created_params = $request->only(['name', 'has_expiry_date', 'document_name_front','document_name_back','has_identify_number', 'identify_number_local_key', 'account_type']);
    
        // Convert the 'has_identify_number' and 'has_expiry_date' to boolean
        $created_params['has_identify_number'] = $request->has_identify_number == "1";
        $created_params['has_expiry_date'] = $request->has_expiry_date == "1";

        $created_params['image_type'] = $request->image_type;

        $created_params['is_editable'] = $request->is_editable;
        $created_params['is_required'] = $request->is_required;


    
        // Set identify_number_local_key based on has_identify_number
        if ($created_params['has_identify_number']) {
            $created_params['identify_number_locale_key'] = $request->identify_number_locale_key;
        } else {
            $created_params['identify_number_locale_key'] = null;
        }
    
        // Set the active status
        $created_params['active'] = true;
    
        // Create the new record
        $driver_needed_doc = DriverNeededDocument::create($created_params);
    
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver needed document created successfully.',
            'driver_needed_doc' => $driver_needed_doc,
        ], 201);
    }
    public function driverNeededDocumentEdit(DriverNeededDocument $driverNeededDocument)
    {
        return Inertia::render(
            'pages/driver_needed_documents/create',
            ['driverNeededDocument' => $driverNeededDocument,]
        );
    }
    public function driverNeededDocumentUpdate(Request $request, DriverNeededDocument $driverNeededDocument)
    {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        // dd($driverNeededDocument);
        $updated_params = $request->only(['name', 'has_expiry_date', 'has_identify_number', 'identify_number_local_key', 'account_type','document_name_front','document_name_back']);
       
        if ($updated_params['has_identify_number']) 
        {
            $updated_params['identify_number_locale_key'] = $request->identify_number_locale_key;
        }else{
            $updated_params['identify_number_locale_key'] = null;
        }

        $updated_params['image_type'] = $request->image_type;

        $updated_params['is_editable'] = $request->is_editable;
        $updated_params['is_required'] = $request->is_required;


        // dd($updated_params);

        $driverNeededDocument->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver needed document Updated successfully.',
            'driver_needed_doc' => $driverNeededDocument,
        ], 201);
    }
// updateDocumentStatus
    public function updateDocumentStatus(Request $request)
    {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        // dd($request->all());
        DriverNeededDocument::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'DriverNeededDocument Document Status updated successfully',
        ]);


    }

    public function updateAndApprove(Driver $driverId)
    {
        $documentStatuses = $driverId->driverDocument->pluck('document_status');

         // Handle the case where no document statuses exist
        if ($documentStatuses->isEmpty()) {           
            return response()->json(['failureMessage' => 'No documents found. Update not performed.']);
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
        
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'Driver Document deleted successfully',
            ],403);
        }
        $driverNeededDocument->delete();

        return response()->json([
            'successMessage' => 'Driver Document deleted successfully',
        ]);
    }

//Driver Needed Docs End
//withdrwal request List
    public function WithdrawalRequestDriversIndex()
    {
        return Inertia::render('pages/withdrawal_request_drivers/index',['app_for'=>env("APP_FOR"),]);
    }

    public function WithdrawalRequestDriversViewDetails(Driver $driver)
    {
        $walletBalance = $driver->driverWallet ? $driver->driverWallet->amount_balance : 0;
    
        $bankDetails = [
            'account_holder_name' => $driver->name,
        ];
    
        $methods = Method::with('fields')->get(); // Fetch all methods with their fields
        $bankInfos = $driver->bankInfoDetail;
    
        $formattedBankInfos = $methods->map(function ($method) use ($bankInfos) {
            $fields = $method->fields->map(function ($field) use ($bankInfos) {
                $info = $bankInfos->firstWhere('field_id', $field->id);
    
                return [
                    'field_name' => $field->input_field_name,
                    'value' => $info->value ?? null,
                ];
            });
    
            return [
                'method_name' => $method->method_name,
                'fields' => $fields,
            ];
        });

        // dd($formattedBankInfos);
    
        return Inertia::render('pages/withdrawal_request_drivers/view_in_detail', [
            'app_for' => env("APP_FOR"),
            'walletBalance' => $walletBalance,
            'bankDetails' => $bankDetails,
            'driver_id' => $driver->id,
            'formattedBankInfos' => $formattedBankInfos,
        ]);
    }
    
    public function WithdrawalRequestDriversList(QueryFilterContract $queryFilter)
    {


        $query = WalletWithdrawalRequest::whereHas('driverDetail.user',function($query){
            $query->companyKey();
            })->orderBy('created_at','desc')->with('driverDetail');

        $results =  $queryFilter->builder($query)->customFilter(new DriverFilter())->paginate();
        $items = fractal($results->items(), new WalletWithdrawalRequestsTransformer)->toArray();
        $results->setCollection(collect($items['data']));

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);  
    }   
    //WithdrawalRequestAmount 
    public function WithdrawalRequestAmount(QueryFilterContract $queryFilter, Driver $driver_id)
    {
        // Debugging driver_id for confirmation
        //dd($driver_id);
    
        $query = WalletWithdrawalRequest::whereHas('driverDetail.user', function($query) {
            $query->companyKey();
        })
        ->where('driver_id', $driver_id->id) // Filter by driver_id
        ->orderBy('created_at', 'desc')
        ->with('driverDetail');
    
        $results = $queryFilter->builder($query)->customFilter(new DriverFilter())->paginate();
        $items = fractal($results->items(), new WalletWithdrawalRequestsTransformer)->toArray();
        $results->setCollection(collect($items['data']));
    
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:wallet_withdrawal_requests,id',
            'status' => 'required|in:approved,declined',
        ]);
    
        $wallet_withdrawal_request = WalletWithdrawalRequest::findOrFail($request->id);
    
        if ($request->status === 'approved') {
            // Handle approval logic
            $driver_wallet = DriverWallet::firstOrCreate(['user_id' => $wallet_withdrawal_request->driver_id]);
            $driver_wallet->amount_spent += $wallet_withdrawal_request->requested_amount;
            $driver_wallet->amount_balance -= $wallet_withdrawal_request->requested_amount;
            $driver_wallet->save();

             // Generate transaction_id
                 $transaction_id = str_random(6); 
    
            $wallet_withdrawal_request->driverDetail->driverWalletHistory()->create([
                'amount' => $wallet_withdrawal_request->requested_amount,
                'transaction_id' => $transaction_id,
                'remarks' => WalletRemarks::WITHDRAWN_FROM_WALLET,
                'is_credit' => false,
            ]);
    
            $wallet_withdrawal_request->status = 1; // Approved

            $user = $driver_wallet->driver->user;
            // $title = custom_trans('payment_credited',[],$user->lang);
            // $body = custom_trans('payment_credited_body',[],$user->lang);
            // $push_data = ['notification_enum'=>"payment_credited"];

            $currency = $user->countryDetail()->pluck('currency_symbol')->first();


            $notification = \DB::table('notification_channels')
            ->where('topics', 'Driver Withdrawal Request Approval') // Match the correct topic
            ->first();

            //   send push notification 
                if ($notification && $notification->push_notification == 1) {

                     // Determine the user's language or default to 'en'
                    $userLang = $user->lang ?? 'en';
    
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
                    $push_data = ['notification_enum'=>"payment_credited"];
                    dispatch(new SendPushNotification($user, $title, $body,$push_data));
                }

                //   send email account approved
                SendDriverWithdrawalAcceptMailNotification::dispatch($user, $transaction_id, $currency, $wallet_withdrawal_request, $driver_wallet);

          
            // dispatch(new SendPushNotification($user, $title, $body,$push_data));

        } elseif ($request->status === 'declined') {
            $wallet_withdrawal_request->status = 2; // Declined

            $driver_wallet = DriverWallet::firstOrCreate(['user_id' => $wallet_withdrawal_request->driver_id]);


            $user = $driver_wallet->driver->user;
            // $title = custom_trans('payment_declained',[],$user->lang);
            // $body = custom_trans('payment_declained_body',[],$user->lang);
            // $push_data = ['notification_enum'=>"payment_declained"];

            $notification = \DB::table('notification_channels')
            ->where('topics', 'Driver Withdrawal Request Decline') // Match the correct topic
            ->first();
            //   send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $user->lang ?? 'en';
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
                        $push_data = ['notification_enum'=>"payment_declained"];
                    dispatch(new SendPushNotification($user, $title, $body,$push_data));
                }
                
                // send the mail withdrawal decline
                SendDriverWithdrawalDeclineMailNotification::dispatch($user);
            // dispatch(new SendPushNotification($user, $title, $body,$push_data));

        }
    
        $wallet_withdrawal_request->payment_status = $request->status;
        $wallet_withdrawal_request->save();
    
        return response()->json([
            'successMessage' => 'Driver payment status updated successfully.',
        ]);
    }
    
    

    public function negativeBalanceDriversIndex() 
    {
        return Inertia::render('pages/negative_balance_drivers/index',['app_for'=>env("APP_FOR"),]);
    }

    public function negativeBalanceDriversList(QueryFilterContract $queryFilter)
    {

        $threshould_value = get_settings('driver_wallet_minimum_amount_to_get_an_order') ?? -300;
        // dd($threshould_value);

        $query = Driver::orderBy('created_at', 'desc')->where('owner_id', null)->whereHas('driverWallet',function($query)use($threshould_value){
            $query->where('amount_balance','<=',$threshould_value);
        });

        $results =  $queryFilter->builder($query)->customFilter(new DriverFilter())->paginate();
        $items = fractal($results->items(), new DriverProfileTransformer)->toArray();
        $results->setCollection(collect($items['data']));

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);  
    }

    public function negativeBalanceDriverPaymentHistory(Driver $driver)
    {
        $currency = $driver->user->countryDetail()->pluck('currency_symbol');

        $driver_date = $driver->getConvertedCreatedAtAttribute();

        $driver_wallet = $driver->driverWallet;


        return Inertia::render('pages/negative_balance_drivers/view_profile',
         ['driver'=>$driver,
        'driver_date'=>$driver_date, 
        'currency'=>$currency,
        'app_for'=>env("APP_FOR"),
        'driver_wallet'=>$driver_wallet,
        ]);   


    }

    // walletHistoryList
    public function walletHistoryList(Driver $driver)
    {

        // dd($driver);
        $limit = request('limit', 15);
        $results = $driver->driverWalletHistory()->orderBy('created_at', 'desc')->paginate($limit);        
        $items = fractal($results, new DriverWalletHistoryTransformer)->toArray();

        return response()->json([
            'results' => $items['data'],
            'paginator' => $results,
        ]);
    }

    public function walletAddAmount(Request $request, Driver $driver)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'operation' => 'required|in:add,subtract'
        ]);

        $driver_wallet = $driver->driverWallet;
      
        if (!$driver_wallet) {
            // Create a new wallet for the driver
            $driver_wallet = $driver->driverWallet()->create([
                // Add the necessary fields and their default values
                'amount_added' => 0, 
                'amount_balance' => 0, 
                'amount_spent' => 0, 
            ]);
        }

        $amount = $request->input('amount');
        $operation = $request->input('operation');
        $transaction_id = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        if ($operation === 'subtract' && $driver_wallet->amount_balance < $amount) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }


        if ($operation === 'add') {
            $driver_wallet->amount_added += $amount;
            $driver_wallet->amount_balance += $amount;
            $is_credit = true;
            $remarks = WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET_FROM_ADMIN;
        } else {
            $driver_wallet->amount_balance -= $amount;
            $driver_wallet->amount_spent += $amount;
            $is_credit = false;
            $remarks = WalletRemarks::WITHDRAWN_FROM_WALLET;
        }

        $driver_wallet->save();

        DriverWalletHistory::create([
            'user_id' => $driver->id,
            'amount' => $amount,
            'transaction_id' => $transaction_id,
            'remarks' => $remarks,
            'is_credit' => $is_credit,
        ]);

        $currency = $driver->user->countryDetail()->pluck('currency_symbol')->first();

        // send mail wallet amount added
        SendDriverWalletAmountMailNotification::dispatch($driver, $transaction_id, $currency, $amount, $driver_wallet);
        return response()->json(['message' => 'Amount adjusted successfully', 'transaction_id' => $transaction_id], 200);
    }
    public function requestList(Driver $driver)
    {
        // dd($user);
        $limit = request('limit', 15);
        $requests = $driver->requestDetail()->paginate($limit);

        return response()->json([
            'requests' => $requests->items(),
            'paginator' => $requests,
        ]);
    }


    // public function driverLevelUpIndex()
    public function driverLevelUpIndex(ZoneType $zoneType)
    {
        $show_driver_level_feature = get_settings('show_driver_level_feature') == 1;
        $reward_point_value = get_settings('reward_point_value');
        $minimun_reward_point = get_settings('minimun_reward_point');
        $currency_symbol = get_settings('currency_symbol');
        
        return Inertia::render('pages/driver_levelup/index',[
            'show_driver_level_feature'=>$show_driver_level_feature,
            'reward_point_value'=>$reward_point_value,
            'minimun_reward_point'=>$minimun_reward_point,
            'app_for'=>env("APP_FOR"),
            'currency_symbol'=>$currency_symbol,
            'zone_type' => $zoneType
        ]);
    }
    public function driverLevelList(QueryFilterContract $queryFilter)
    // public function driverLevelList(ZoneType $zoneType,QueryFilterContract $queryFilter)
    {
        $query = DriverLevelUp::where('zone_type_id',request()->zone_type_id)->orderBy('level','ASC');

        // $query = $zoneType->levelDetail()->orderBy('level','ASC');
        $results =  $queryFilter->builder($query)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function settingsUpdate(Request $request)
    {
        $settings = Setting::where('category', 'general')->where('name', $request->id)->first();

        if($settings){
            $settings->update(['value'=>$request->status]);
        }else{
            Setting::create(['category'=>'general','name'=>$request->id,'value'=>$request->status]);

        }
       
        return response()->json([
            'successMessage' => 'status updated successfully',
        ]);
    }
    public function driverLevelStore(Request $request)
    {
        $params = $request->validate([
            'name' => 'required',
            'level' => 'required',
            'reward' => 'required',
            'is_min_ride_amount_complete' => 'required',
            'is_min_ride_complete' => 'required',
            'reward_type' => 'required',
            'min_ride_amount' => 'required_if:is_min_ride_amount_complete,true',
            'amount_points' => 'required_if:is_min_ride_amount_complete,true',
            'min_ride_count' => 'required_if:is_min_ride_complete,true',
            'ride_points' => 'required_if:is_min_ride_complete,true',
            'image' => 'required',
            'zone_type_id' => 'required',
        ]);

        if ($uploadedFile = $request->file('image')) {
            $params['image'] = $this->imageUploader->file($uploadedFile)
                ->saveDriverLevelImage();
        }

        $level = DriverLevelUp::create($params);

        return response()->json([
            'successMessage' => 'Driver Level created successfully.',
            'level' => $level,
        ], 201);
    }
    // public function driverLevelUpCreate()
    // {
    //     $level = DriverLevelUp::orderBy('created_at','DESC')->count();
    //     return Inertia::render('pages/driver_levelup/create',['lastLevel'=> $level]);
    // }
    public function driverLevelUpCreate(ZoneType $zoneType)
{
    $level = DriverLevelUp::where('zone_type_id', $zoneType->id)
                          ->orderBy('created_at', 'DESC')
                          ->count();

    return Inertia::render('pages/driver_levelup/create', [
        'lastLevel' => $level,
        'zone_type' => $zoneType
    ]);
}
   
    public function driverLevelEdit(DriverLevelUp $level)
    {
        $zone_type = $level->zoneTypeDetail;
        return Inertia::render('pages/driver_levelup/create',['level'=> $level,
    'zone_type'=> $zone_type]);
    }
    public function driverLevelUpdate(DriverLevelUp $level,Request $request)
    {
        $params = $request->validate([
            'name' => 'required',
            'level' => 'required',
            'reward' => 'required',
            'reward_type' => 'required',
            'is_min_ride_amount_complete' => 'required',
            'is_min_ride_complete' => 'required',
            'min_ride_amount' => 'required_if:is_min_ride_amount_complete,true',
            'amount_points' => 'required_if:is_min_ride_amount_complete,true',
            'min_ride_count' => 'required_if:is_min_ride_complete,true',
            'zone_type_id' => 'required',
            'ride_points' => 'required_if:is_min_ride_complete,true',
        ]);
        
        if ($uploadedFile = $request->file('image')) {
            $params['image'] = $this->imageUploader->file($uploadedFile)
                ->saveDriverLevelImage();
        }

        $level->update($params);

        return response()->json([
            'successMessage' => 'Driver Level updated successfully.',
            'level' => $level,
        ], 201);
    }

    public function driverLevelDelete(DriverLevelUp $level)
    {
        $level->delete();

        return response()->json([
            'successMessage' => 'Driver Level Deleted successfully.',
        ], 201);
    }
    public function restoreUser($id)
    {
        $user = User::withTrashed()->find($id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $user->update(['is_deleted_at' => null,'active'=>1]);
        $user->driver->update(['is_deleted_at' => null,'active'=>0]);
    
        return response()->json(['message' => 'User restored successfully']);
    }
}
