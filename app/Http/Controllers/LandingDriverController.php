<?php

namespace App\Http\Controllers;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Models\Admin\LandingDriver;
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


class LandingDriverController extends BaseController
{
    protected $imageUploader;

    protected $landingDriver;

 

    public function __construct(LandingDriver $landingDriver, ImageUploaderContract $imageUploader)
    {
        $this->landingDriver = $landingDriver;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        return Inertia::render('pages/landingsite/driver/index');
    }


    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = LandingDriver::query();
// dd("ssss");
        $results = $queryFilter->builder($query)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {

        return Inertia::render('pages/landingsite/driver/create',['app_for'=>env('APP_FOR'),]);
    }
    public function store(Request $request)
    {
         // Validate the incoming request
         $request->validate([
            'hero_title'  => 'required',
            'driver_heading_1'  => 'required',
            'driver_para'  => 'required',
            'driver_img_1'  => 'required',
            'driver_title_1'  => 'required',
            'driver_para_1'  => 'required',
            'driver_img_2'  => 'required',
            'driver_title_2'  => 'required',
            'driver_para_2'  => 'required',
            'driver_img_3'  => 'required',
            'driver_title_3'  => 'required',
            'driver_para_3'  => 'required',
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
            'how_it_work_title_7'  => 'required',
            'how_it_work_para_7'  => 'required',
            'how_it_work_img_7'  => 'required',
            'req_heading'  => 'required',
            'req_title'  => 'required',
            'req_lists'  => 'required',
            'req_img'  => 'required',
            'vechile_req_title'  => 'required',
            'vechile_req_lists'  => 'required',
            'vechile_req_img'  => 'required',
            'doc_req_title'  => 'required',
            'doc_req_lists'  => 'required',
            'doc_req_img'  => 'required',
            'locale'  => 'required',
            'language'  => 'required'
        ]);

        $created_params = $request->all();

        if ($uploadedFile = $request->file('driver_img_1')) {
            $created_params['driver_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('driver_img_2')) {
            $created_params['driver_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('driver_img_3')) {
            $created_params['driver_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_1')) {
            $created_params['how_it_work_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_2')) {
            $created_params['how_it_work_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_3')) {
            $created_params['how_it_work_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_4')) {
            $created_params['how_it_work_img_4'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_5')) {
            $created_params['how_it_work_img_5'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_6')) {
            $created_params['how_it_work_img_6'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_7')) {
            $created_params['how_it_work_img_7'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('req_img')) {
            $created_params['req_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('vechile_req_img')) {
            $created_params['vechile_req_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('doc_req_img')) {
            $created_params['doc_req_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }

        // $landingHome->create($created_params);

        LandingDriver::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Landing Driver created successfully.'
        ], 201);
    }
    public function edit($id)
    {

        $landingDriver = LandingDriver::find($id);
        return Inertia::render(
            'pages/landingsite/driver/create',
            ['landingDriver' => $landingDriver,'app_for'=>env('APP_FOR'),]
        );
    }
    public function update(Request $request, LandingDriver $landingDriver)
    {

         // Validate the incoming request
         $request->validate([
            'hero_title'  => 'required',
            'driver_heading_1'  => 'required',
            'driver_para'  => 'required',
            'driver_img_1'  => 'required',
            'driver_title_1'  => 'required',
            'driver_para_1'  => 'required',
            'driver_img_2'  => 'required',
            'driver_title_2'  => 'required',
            'driver_para_2'  => 'required',
            'driver_img_3'  => 'required',
            'driver_title_3'  => 'required',
            'driver_para_3'  => 'required',
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
            'how_it_work_title_7'  => 'required',
            'how_it_work_para_7'  => 'required',
            'how_it_work_img_7'  => 'required',
            'req_heading'  => 'required',
            'req_title'  => 'required',
            'req_lists'  => 'required',
            'req_img'  => 'required',
            'vechile_req_title'  => 'required',
            'vechile_req_lists'  => 'required',
            'vechile_req_img'  => 'required',
            'doc_req_title'  => 'required',
            'doc_req_lists'  => 'required',
            'doc_req_img'  => 'required',
            'locale'  => 'required',
            'language'  => 'required',
        ]);

        $updated_params = $request->all();

        if ($uploadedFile = $request->file('driver_img_1')) {
            $updated_params['driver_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('driver_img_2')) {
            $updated_params['driver_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('driver_img_3')) {
            $updated_params['driver_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_1')) {
            $updated_params['how_it_work_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_2')) {
            $updated_params['how_it_work_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_3')) {
            $updated_params['how_it_work_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_4')) {
            $updated_params['how_it_work_img_4'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_5')) {
            $updated_params['how_it_work_img_5'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_6')) {
            $updated_params['how_it_work_img_6'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('how_it_work_img_7')) {
            $updated_params['how_it_work_img_7'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('req_img')) {
            $updated_params['req_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('vechile_req_img')) {
            $updated_params['vechile_req_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }
        if ($uploadedFile = $request->file('doc_req_img')) {
            $updated_params['doc_req_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingDriverImage();
        }



        $landingDriver->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Driver updated successfully.',
            'landingDriver' => $landingDriver,
        ], 201);

    }
    public function destroy(LandingDriver $landingDriver)
    {
        $landingDriver->delete();

        return response()->json([
            'successMessage' => 'Driver deleted successfully',
        ]);
    }  

   

    public function driverpage(Request $request)
    {

        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingDriver = LandingDriver::where('locale', $selectedLocale)->orWhere('locale',$defaultLocale)->orWhere('locale','en')->first();
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
         $imageColumns = ['driver_img_1', 'driver_img_2', 'driver_img_3', 'how_it_work_img_1', 'how_it_work_img_2', 'how_it_work_img_3', 'how_it_work_img_4', 'how_it_work_img_5', 'how_it_work_img_6', 'how_it_work_img_7',
        'req_img', 'vechile_req_img', 'doc_req_img'];

         //     // Construct URLs for each image column
                 foreach ($imageColumns as $column) {
                     if ($landingDriver && $landingDriver->$column) {
                         $landingDriver->{$column . '_url'} = asset('storage/uploads/website/images/' . $landingDriver->$column);
                     } else {
                         $landingDriver->{$column . '_url'} = null;
                     }
                 }

        return Inertia::render('pages/landing/driver', [
            'landingDriver' => $landingDriver,
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