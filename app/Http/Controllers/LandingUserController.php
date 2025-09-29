<?php

namespace App\Http\Controllers;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Models\Admin\LandingUser;
use App\Models\Admin\LandingHeader;
use Illuminate\Support\Facades\Validator;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Base\Services\ImageUploader\ImageUploader;
use Illuminate\Support\Str;
use App\Models\Admin\Setting;
use App\Models\Languages;


class LandingUserController extends BaseController
{
    protected $imageUploader;

    protected $landingUser;

 

    public function __construct(LandingUser $landingUser, ImageUploaderContract $imageUploader)
    {
        $this->landingUser = $landingUser;
        $this->imageUploader = $imageUploader;
    }
    public function index()
    {
        return Inertia::render('pages/landingsite/user/index');
    }


    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = LandingUser::query();
// dd("ssss");
        $results = $queryFilter->builder($query)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {

        return Inertia::render('pages/landingsite/user/create',['app_for'=>env('APP_FOR'),]);
    }
    public function store(Request $request)
    {
         // Validate the incoming request
         $request->validate([
            'hero_title'  => 'required',
            'user_heading_1'  => 'required',
            'user_para'  => 'required',
            'user_img_1'  => 'required',
            'user_title_1'  => 'required',
            'user_para_1'  => 'required',
            'user_img_2'  => 'required',
            'user_title_2'  => 'required',
            'user_para_2'  => 'required',
            'user_img_3'  => 'required',
            'user_title_3'  => 'required',
            'user_para_3'  => 'required',
            'how_it_work_heading'  => 'required',
            'how_it_work_title_1'  => 'required',
            'how_it_work_para_1'  => 'required',
            'how_it_work_img_1'  => 'required',
            'how_it_work_title_2'  => 'required',
            'how_it_work_para_2' => 'required', 
            'how_it_work_img_2'  => 'required',
            'how_it_work_title_3'  => 'required',
            'how_it_work_para_3' => 'required',
            'how_it_work_img_3' => 'required',
            'how_it_work_title_4'  => 'required',
            'how_it_work_para_4'  => 'required',
            'how_it_work_img_4'  => 'required',
            'how_it_work_title_5'  => 'required',
            'how_it_work_para_5'  => 'required',
            'how_it_work_img_5'  => 'required',
            'how_it_work_title_6'  => 'required',
            'how_it_work_para_6'  => 'required',
            'how_it_work_img_6'  => 'required',
            'locale'  => 'required',
            'language'  => 'required'
        ]);

        $created_params = $request->all();

        if ($uploadedFile = $request->file('user_img_1')) {
            $created_params['user_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('user_img_2')) {
            $created_params['user_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('user_img_3')) {
            $created_params['user_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_1')) {
            $created_params['how_it_work_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_2')) {
            $created_params['how_it_work_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_3')) {
            $created_params['how_it_work_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_4')) {
            $created_params['how_it_work_img_4'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_5')) {
            $created_params['how_it_work_img_5'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_6')) {
            $created_params['how_it_work_img_6'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }

        // $landingHome->create($created_params);

        LandingUser::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Landing User created successfully.'
        ], 201);
    }
    public function edit($id)
    {

        $landingUser = LandingUser::find($id);
        return Inertia::render(
            'pages/landingsite/user/create',
            ['landingUser' => $landingUser,'app_for'=>env('APP_FOR'),]
        );
    }
    public function update(Request $request, LandingUser $landingUser)
    {

         // Validate the incoming request
         $request->validate([
            'hero_title'  => 'required',
            'user_heading_1'  => 'required',
            'user_para'  => 'required',
            'user_img_1'  => 'required',
            'user_title_1'  => 'required',
            'user_para_1'  => 'required',
            'user_img_2'  => 'required',
            'user_title_2'  => 'required',
            'user_para_2'  => 'required',
            'user_img_3'  => 'required',
            'user_title_3'  => 'required',
            'user_para_3'  => 'required',
            'how_it_work_heading'  => 'required',
            'how_it_work_title_1'  => 'required',
            'how_it_work_para_1'  => 'required',
            'how_it_work_img_1'  => 'required',
            'how_it_work_title_2'  => 'required',
            'how_it_work_para_2' => 'required', 
            'how_it_work_img_2'  => 'required',
            'how_it_work_title_3'  => 'required',
            'how_it_work_para_3' => 'required',
            'how_it_work_img_3' => 'required',
            'how_it_work_title_4'  => 'required',
            'how_it_work_para_4'  => 'required',
            'how_it_work_img_4'  => 'required',
            'how_it_work_title_5'  => 'required',
            'how_it_work_para_5'  => 'required',
            'how_it_work_img_5'  => 'required',
            'how_it_work_title_6'  => 'required',
            'how_it_work_para_6'  => 'required',
            'how_it_work_img_6'  => 'required',
            'locale'  => 'required',
            'language'  => 'required'
        ]);

        $updated_params = $request->all();

        if ($uploadedFile = $request->file('user_img_1')) {
            $updated_params['user_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('user_img_2')) {
            $updated_params['user_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('user_img_3')) {
            $updated_params['user_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_1')) {
            $updated_params['how_it_work_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_2')) {
            $updated_params['how_it_work_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_3')) {
            $updated_params['how_it_work_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_4')) {
            $updated_params['how_it_work_img_4'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_5')) {
            $updated_params['how_it_work_img_5'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_6')) {
            $updated_params['how_it_work_img_6'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingUserImage();
        }



        $landingUser->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'User updated successfully.',
            'landingUser' => $landingUser,
        ], 201);

    }
    public function destroy(LandingUser $landingUser)
    {
        $landingUser->delete();

        return response()->json([
            'successMessage' => 'User deleted successfully',
        ]);
    }  
   

    public function userpage(Request $request)
    {
        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingUser = LandingUser::where('locale', $selectedLocale)->orWhere('locale',$defaultLocale)->orWhere('locale','en')->first();
        $landingHeader = LandingHeader::where('locale', $selectedLocale)->orWhere('locale',$defaultLocale)->orWhere('locale','en')->first();

           // Check the customization settings toggle status
           $enableLandingSite = Setting::where('category', 'customization_settings')
           ->where('name', 'enable_landing_site')
           ->value('value');

       // if ($enableLandingSite == 0) {
       //     return redirect()->route('login.admin'); // Replace with the actual route name for redirection
       // }
       if ($enableLandingSite == 0) {
           // Fetch the dynamic redirect value from the settings table
           $adminRedirect = Setting::where('category', 'general')
               ->where('name', 'admin_login')
               ->value('value');

           // Pass the dynamic value to the route
           return redirect()->route('login.{redirect}', ['redirect' => $adminRedirect]);
       }

        // Define the image columns you have
        $imageColumns = ['user_img_1', 'user_img_2', 'user_img_3', 'how_it_work_img_1', 'how_it_work_img_2', 'how_it_work_img_3', 'how_it_work_img_4', 'how_it_work_img_5', 'how_it_work_img_6'];

         // // Construct URLs for each image column
                 foreach ($imageColumns as $column) {
                     if ($landingUser && $landingUser->$column) {
                         $landingUser->{$column . '_url'} = asset('storage/uploads/website/images/' . $landingUser->$column);
                     } else {
                         $landingUser->{$column . '_url'} = null;
                     }
                 }

        return Inertia::render('pages/landing/user', [
            'landingUser' => $landingUser,
            'landingHeader' => $landingHeader,
            'locales' => $this->getLocales(),
        ]);
    }
    
    private function getLocales()
    {
        // return LandingHeader::pluck('locale', 'id');
        return LandingHeader::pluck('language', 'locale');
    }


}