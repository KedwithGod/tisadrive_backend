<?php

namespace App\Http\Controllers;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Models\Admin\LandingHome;
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


class LandingHomeController extends BaseController
{
    protected $imageUploader;

    protected $landingHome;

 

    public function __construct(LandingHome $landingHome, ImageUploaderContract $imageUploader)
    {
        $this->landingHome = $landingHome;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        return Inertia::render('pages/landingsite/home/index');
    }


    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = LandingHome::query();
// dd("ssss");
        $results = $queryFilter->builder($query)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {

        return Inertia::render('pages/landingsite/home/create',['app_for'=>env('APP_FOR')]);
    }
    public function store(Request $request)
    {
         // Validate the incoming request
         $request->validate([
            'hero_title' => 'required',
            'hero_user_link_android' => 'required',
            'hero_user_link_apple' => 'required',
            'hero_driver_link_android' => 'required',
            'hero_driver_link_apple' => 'required',
            'feature_heading' => 'required',
            'feature_para' => 'required',
            'feature_sub_heading_1' => 'required',
            'feature_sub_para_1' => 'required',
            'feature_sub_heading_2' => 'required',
            'feature_sub_para_2' => 'required',
            'feature_sub_heading_3' => 'required',
            'feature_sub_para_3' => 'required',
            'feature_sub_heading_4' => 'required',
            'feature_sub_para_4' => 'required',
            'service_heading_1' => 'required',
            'service_heading_2' => 'required',
            'service_para' => 'required',
            'services' => 'required' ,
            'service_img' => 'required',
            'about_title_1' => 'required',
            'about_title_2' => 'required',
            'about_img' => 'required' ,
            'about_para' => 'required',
            'about_lists' => 'required' ,
            'box_img_1' => 'required',
            'box_para_1' => 'required' ,
            'box_img_2' => 'required',
            'box_para_2' => 'required',
            'box_img_3' => 'required' ,
            'box_para_3' => 'required' ,
            'drive_heading' => 'required' ,
            'drive_title_1' => 'required' ,
            'drive_para_1' => 'required' ,
            'drive_title_2' => 'required' ,
            'drive_para_2' => 'required' ,
            'drive_title_3' => 'required',
            'drive_para_3' => 'required' ,
            'service_area_img' => 'required' ,
            'service_area_title' => 'required' ,
            'service_area_para' => 'required' ,
            'locale' => 'required',
            'language' => 'required',
        ]);

        $created_params = $request->all();

        if ($uploadedFile = $request->file('service_img')) {
            $created_params['service_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('about_img')) {
            $created_params['about_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('box_img_1')) {
            $created_params['box_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('box_img_2')) {
            $created_params['box_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('box_img_3')) {
            $created_params['box_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('service_area_img')) {
            $created_params['service_area_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }

        // $landingHome->create($created_params);

        LandingHome::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Landin Home created successfully.'
        ], 201);
    }
    public function edit($id)
    {

        $landingHome = LandingHome::find($id);
        return Inertia::render(
            'pages/landingsite/home/create',
            ['landingHome' => $landingHome,'app_for'=>env('APP_FOR'),]
        );
    }
    public function update(Request $request, LandingHome $landingHome)
    {

         // Validate the incoming request
         $request->validate([
            'hero_title' => 'required',
            'hero_user_link_android' => 'required',
            'hero_user_link_apple' => 'required',
            'hero_driver_link_android' => 'required',
            'hero_driver_link_apple' => 'required',
            'feature_heading' => 'required',
            'feature_para' => 'required',
            'feature_sub_heading_1' => 'required',
            'feature_sub_para_1' => 'required',
            'feature_sub_heading_2' => 'required',
            'feature_sub_para_2' => 'required',
            'feature_sub_heading_3' => 'required',
            'feature_sub_para_3' => 'required',
            'feature_sub_heading_4' => 'required',
            'feature_sub_para_4' => 'required',
            'service_heading_1' => 'required',
            'service_heading_2' => 'required',
            'service_para' => 'required',
            'services' => 'required' ,
            'service_img' => 'required',
            'about_title_1' => 'required',
            'about_title_2' => 'required',
            'about_img' => 'required' ,
            'about_para' => 'required',
            'about_lists' => 'required' ,
            'box_img_1' => 'required',
            'box_para_1' => 'required' ,
            'box_img_2' => 'required',
            'box_para_2' => 'required',
            'box_img_3' => 'required' ,
            'box_para_3' => 'required' ,
            'drive_heading' => 'required' ,
            'drive_title_1' => 'required' ,
            'drive_para_1' => 'required' ,
            'drive_title_2' => 'required' ,
            'drive_para_2' => 'required' ,
            'drive_title_3' => 'required',
            'drive_para_3' => 'required' ,
            'service_area_img' => 'required' ,
            'service_area_title' => 'required' ,
            'service_area_para' => 'required' ,
            'locale' => 'required',
            'language' => 'required',
        ]);

        $updated_params = $request->all();

        if ($uploadedFile = $request->file('service_img')) {
            $updated_params['service_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('about_img')) {
            $updated_params['about_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('box_img_1')) {
            $updated_params['box_img_1'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('box_img_2')) {
            $updated_params['box_img_2'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('box_img_3')) {
            $updated_params['box_img_3'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }
        if ($uploadedFile = $request->file('service_area_img')) {
            $updated_params['service_area_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingHomeImage();
        }


        $landingHome->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Home updated successfully.',
            'landingHome' => $landingHome,
        ], 201);

    }
    public function destroy(LandingHome $landingHome)
    {
        $landingHome->delete();

        return response()->json([
            'successMessage' => 'Home deleted successfully',
        ]);
    }  


    // public function homepage( Request $request)
    // {
    //     // Get the locale from the request or default to 'en'
    //     $locale = $request->input('locale', 'en');

    //     // Fetch the data based on the locale
    //     $landingHome = LandingHome::where('locale', $locale)->first();

    //      // Define the image columns you have
    //     $imageColumns = ['service_img', 'about_img', 'box_img_1', 'box_img_2', 'box_img_3', 'service_area_img'];

    //     // Construct URLs for each image column
    //         foreach ($imageColumns as $column) {
    //             if ($landingHome && $landingHome->$column) {
    //                 $landingHome->{$column . '_url'} = asset('storage/uploads/website/images/' . $landingHome->$column);
    //             } else {
    //                 $landingHome->{$column . '_url'} = null;
    //             }
    //         }

    //     // Fetch all unique locales
    //     $locales = LandingHome::select('locale')->distinct()->get();

    //     return Inertia::render('pages/landing/index', [
    //         'landingHome' => $landingHome,
    //         'locales' => $locales,
    //     ]);
    // }
    // {
    //     // Get the locale from the request, default to 'en'
    //     $locale = $request->input('locale', 'en');

    //     // Fetch the data based on the locale
    //     $landingHome = LandingHome::where('locale', $locale)->first();

    //     return Inertia::render('pages/landing/index', [
    //         'landingHome' => $landingHome,
    //     ]);

    // }
    public function homepage(Request $request)
    {

        
        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' if not found
        // Use the default locale if none is selected
        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale));
        session(['selectedLocale' => $selectedLocale]); // Store in session

        // $selectedLocale = $request->input('locale', session('selectedLocale', 'es')); // default to 'en'
        // session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingHome = LandingHome::where('locale', $selectedLocale)->orWhere('locale',$defaultLocale)->orWhere('locale','en')->first();
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
        $imageColumns = ['service_img', 'about_img', 'box_img_1', 'box_img_2', 'box_img_3', 'service_area_img'];

    //     // Construct URLs for each image column
            foreach ($imageColumns as $column) {
                if ($landingHome && $landingHome->$column) {
                    $landingHome->{$column . '_url'} = asset('storage/uploads/website/images/' . $landingHome->$column);
                } else {
                    $landingHome->{$column . '_url'} = null;
                }
            }
        
        return Inertia::render('pages/landing/index', [
            'landingHome' => $landingHome,
            'landingHeader' => $landingHeader,
            'locales' => $this->getLocales(),
            'defaultLocale' => $defaultLocale, // Send default locale to Vue
            'selectedLocale' => $selectedLocale,
        ]);
    }
    
    private function getLocales()
    {
        // return LandingHeader::pluck('locale', 'id');
        return LandingHeader::pluck('language', 'locale');
    }

}