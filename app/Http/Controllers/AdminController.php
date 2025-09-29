<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\ServiceLocation;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Admin\AdminDetail;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Filters\Admin\AdminFilter;
use Illuminate\Support\Facades\Storage;
use App\Models\Support\SupportTicketCategory;

class AdminController extends BaseController
{
    protected $imageUploader;
    protected $user;

    public function __construct(ImageUploaderContract $imageUploader, User $user)
    {
        $this->imageUploader = $imageUploader;
        $this->user = $user;
    }
    public function index() 
    {
        return Inertia::render('pages/admins/index',['app_for'=>env('APP_FOR')]);
    }
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        // $query = AdminDetail::query();
        $query = AdminDetail::where('created_by', auth()->user()->id);

        $results = $queryFilter->builder($query)->customFilter(new AdminFilter)->paginate();        // dd($results);

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create() 
    {
            $serviceLocations = ServiceLocation::active()->get();
            $countries = Country::active()->get();
            // $category = SupportTicketCategory::get();

            $rolesNotTodisplay = ['owner','user', 'driver'];

            // $roles = Role::whereNotIn('slug', $rolesNotTodisplay)->get();

             // Fetch roles created by the authenticated user
            $roles = Role::whereNotIn('slug', $rolesNotTodisplay)
            ->where('created_by', auth()->user()->id) // Filter by created_by
            ->get();

            // dd($roles);


            return Inertia::render('pages/admins/create',[ 'serviceLocations'=> $serviceLocations,
            'countries'=> $countries,'roles'=> $roles,'app_for'=>env('APP_FOR'),
            // 'category'=> $category,
        ]);
    }
    public function store(Request $request)
    {

        // dd($request);


        $created_params =   $request->validate([
            // 'name' => 'required',
            'country'=>'required',
            'mobile' => 'required',
            'email' => 'required',
            'role' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            // 'category_type' => 'required',
        ]);

        // $created_params['category_type'] = is_array($created_params['category_type']) 
        // ? implode(',', $created_params['category_type']) 
        // : $created_params['category_type'];
            
        $created_params['first_name'] = $request->input('name');

        $created_params['created_by'] = auth()->user()->id;

        $created_params['active'] = true;

        if ($request->input('service_location_id')) {
            $created_params['service_location_id'] = $request->service_location_id;
            $timezone = ServiceLocation::where('id', $request->input('service_location_id'))->pluck('timezone')->first();
        } else {
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }

        $user_params = ['name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'mobile'=>$request->input('mobile'),
            'city'=>$request->input('city'),
            'timezone'=>$timezone,
            'country'=>$request->input('country'),
            'password' => bcrypt($request->input('password'))
        ];
        $user_params['password'] = bcrypt($request->input('password'));

        $user = $this->user->create($user_params);
      
        if ($uploadedFile = $request->file('profile_picture')) {
            $user['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
            $user->save();
        }
        $user->attachRole($request->role);

        $user->admin()->create($created_params);


        // dd($created_params);
    
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver created successfully.',
        ], 201);
    }

    public function edit(AdminDetail $adminDetail)
    {
        // dd($adminDetail->user->roles[0]);

        $role = $adminDetail->user->roles[0];

        $serviceLocations = ServiceLocation::active()->get();
        $countries = Country::active()->get();
        // $category = SupportTicketCategory::get();

        $rolesNotTodisplay = ['owner','user', 'driver'];

        $roles = Role::whereNotIn('slug', $rolesNotTodisplay)->get();
        
        return Inertia::render('pages/admins/create',[ 'serviceLocations'=> $serviceLocations,
        'countries'=> $countries,'roles'=> $roles,'admin'=> $adminDetail,'role'=> $role,
        'app_for'=>env('APP_FOR'),
        // 'category'=> $category,
        ]);
    }

    public function update(Request $request, AdminDetail $adminDetail)
    {       

        $updatedParams =  $request->validate([
            'country'=>'required',
            'mobile' => 'required',
            'email' => 'required',
            'role' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            // 'category_type' => 'required',
        ]);

        // $updatedParams['category_type'] = is_array($updatedParams['category_type']) 
        // ? implode(',', $updatedParams['category_type']) 
        // : $updatedParams['category_type'];
            

        $updatedParams['first_name'] = $request->input('name');

        $updatedParams['pincode'] = $request->pincode;
       
        if ($request->input('service_location_id')) {
            $updatedParams['service_location_id'] = $request->service_location_id;
            $timezone = ServiceLocation::where('id', $request->input('service_location_id'))->pluck('timezone')->first();
        } else {
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }
        
        $user_params = ['name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'mobile'=>$request->input('mobile'),
            'city'=>$request->input('city'),
            'timezone'=>$timezone,
            'country'=>$request->input('country'),            
        ];


        if ($uploadedFile = $request->file('profile_picture')) {
            $user_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }


        $adminDetail->user->update($user_params);

        $adminDetail->user->roles()->detach();

        $adminDetail->user->attachRole($request->role);

        $adminDetail->update($updatedParams);
        // dd($adminDetail);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Admin created successfully.',
        ], 201);
    }


    public function destroy(AdminDetail $adminDetail)
    {
        $adminDetail->user->delete();

        $adminDetail->delete();

        return response()->json([
            'successMessage' => 'Admin deleted successfully',
        ]);
    }  
    public function updateStatus(Request $request)
    {
        $status = $request->status; // Toggle the status
    
        $admin = AdminDetail::where('id', $request->id)->first();

        // dd($request->status);

        
        $admin->user->update(['active' => $status]);

        $admin->update(['active' => $status]);

    
        return response()->json([
            'successMessage' => 'Admin Status updated successfully',
        ]);
    }

    public function editPassword(AdminDetail $adminDetail)
    {

    $role = $adminDetail->user->roles[0];

    $serviceLocations = ServiceLocation::active()->get();
    $countries = Country::active()->get();

    $rolesNotTodisplay = ['owner','user', 'driver'];

    $roles = Role::whereNotIn('slug', $rolesNotTodisplay)->get();
        
        return Inertia::render('pages/admins/edit',[ 'serviceLocations'=> $serviceLocations,
        'countries'=> $countries,'roles'=> $roles,'admin'=> $adminDetail,'role'=> $role,
        'app_for'=>env('APP_FOR')
        ]);
    }

    public function updatePasswords(Request $request, AdminDetail $adminDetail)
    {
        // Validate the password and confirmation
        $updatedParams = $request->validate([
            'password' => 'required|min:8',  // Confirmed is for password_confirmation
            'confirm_password' => 'required|same:password',
        ]);
        
        $user_params = [
            'password'=>$request->input('password'),
            'confirm_password'=>$request->input('confirm_password'),         
        ];

        if($request->input('password')){
            $validated['password'] = bcrypt($request->input('password'));
        }
        if($request->input('password')){
            $user_params['password'] = $validated['password'];
        }

        $adminDetail->user->update($user_params);

        $adminDetail->update($updatedParams);
        // dd($adminDetail);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Admin created successfully.',
        ], 201);
    }
}
