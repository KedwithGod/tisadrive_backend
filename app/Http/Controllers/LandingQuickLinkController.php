<?php

namespace App\Http\Controllers;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Models\Admin\LandingQuickLink;
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


class LandingQuickLinkController extends BaseController
{
    protected $imageUploader;

    protected $landingQuick;

 

    public function __construct(LandingQuickLink $landingQuickLink, ImageUploaderContract $imageUploader)
    {
        $this->landingQuickLink = $landingQuickLink;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        return Inertia::render('pages/landingsite/quicklink/index');
    }


    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = LandingQuickLink::query();
// dd("ssss");
        $results = $queryFilter->builder($query)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {

        return Inertia::render('pages/landingsite/quicklink/create',['app_for'=>env('APP_FOR'),]);
    }
    public function store(Request $request)
    {
         // Validate the incoming request
         $request->validate([
            'privacy_title' => 'required' ,
            'privacy' => 'required' ,
            'terms_title' => 'required' ,
            'terms' => 'required' ,
            'compliance_title' => 'required' ,
            'compliance' => 'required' ,
            'dmv_title' => 'required' ,
            'dmv' => 'required' ,
            'locale' => 'required',
            'language' => 'required',
        ]);

        $created_params = $request->all();

        LandingQuickLink::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Landing QuickLinks Content created successfully.'
        ], 201);
    }

    public function edit($id)
    {

        $landingQuickLink = LandingQuickLink::find($id);
        return Inertia::render(
            'pages/landingsite/quicklink/create',
            ['landingQuickLink' => $landingQuickLink,'app_for'=>env('APP_FOR'),]
        );
    }
    public function update(Request $request, LandingQuickLink $landingQuickLink)
    {

         // Validate the incoming request
         $request->validate([
            'privacy_title' => 'required' ,
            'privacy' => 'required' ,
            'terms_title' => 'required' ,
            'terms' => 'required' ,
            'compliance_title' => 'required' ,
            'compliance' => 'required' ,
            'dmv_title' => 'required' ,
            'dmv' => 'required' ,
            'locale' => 'required',
            'language' => 'required',
        ]);

        $updated_params = $request->all();

        $landingQuickLink->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'QuickLink Content updated successfully.',
            'landingQuickLink' => $landingQuickLink,
        ], 201);

    }
    public function destroy(LandingQuickLink $landingQuickLink)
    {
        $landingQuickLink->delete();

        return response()->json([
            'successMessage' => 'Home deleted successfully',
        ]);
    }  

   

    public function privacypage(Request $request)
    {
        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingQuickLink = LandingQuickLink::where('locale', $selectedLocale)->orWhere('locale',$defaultLocale)->orWhere('locale','en')->first();
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

        return Inertia::render('pages/landing/privacy', [
            'landingQuickLink' => $landingQuickLink,
            'landingHeader' => $landingHeader,
            'locales' => $this->getLocales(),
        ]);
    }

    /**
     * Privacy Policy
     * @bodyParam locale string optional User Language
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrivacyContent(Request $request)
    {
        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        $content = LandingQuickLink::where('locale', $selectedLocale)->value('privacy') ??
                   LandingQuickLink::where('locale', 'en')->value('privacy');

        if (!$content) {
            return response()->json(['error' => 'Content not found'], 404); // Return 404 if no content found
        }

        return response()->json(['privacy' => $content]);
    }

    public function termspage(Request $request)
    {
        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingQuickLink = LandingQuickLink::where('locale', $selectedLocale)->first() ?? 
                            LandingQuickLink::where('locale', 'en')->first();
        $landingHeader = LandingHeader::where('locale', $selectedLocale)->first() ??
                         LandingHeader::where('locale', 'en')->first();

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

        return Inertia::render('pages/landing/terms', [
            'landingQuickLink' => $landingQuickLink,
            'landingHeader' => $landingHeader,
            'locales' => $this->getLocales(),
        ]);
    }

    /**
     * Terms and Conditions
     * @bodyParam locale string optional User Language
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTermsContent(Request $request)
    {
        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        $content = LandingQuickLink::where('locale', $selectedLocale)->value('terms') ?? 
                   LandingQuickLink::where('locale', 'en')->value('terms');

        if (!$content) {
            return response()->json(['error' => 'Content not found'], 404); // Return 404 if no content found
        }

        return response()->json(['terms' => $content]);
    }

    public function compliancepage(Request $request)
    {
        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingQuickLink = LandingQuickLink::where('locale', $selectedLocale)->first() ?? 
                            LandingQuickLink::where('locale', 'en')->first();
        $landingHeader = LandingHeader::where('locale', $selectedLocale)->first() ?? 
                         LandingHeader::where('locale', 'en')->first();

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

        return Inertia::render('pages/landing/compliance', [
            'landingQuickLink' => $landingQuickLink,
            'landingHeader' => $landingHeader,
            'locales' => $this->getLocales(),
        ]);
    }

    /**
     * Compliance
     * @bodyParam locale string optional User Language
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComplianceContent(Request $request)
    {
        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        $content = LandingQuickLink::where('locale', $selectedLocale)->value('compliance') ?? 
                   LandingQuickLink::where('locale', 'en')->value('compliance');

        if (!$content) {
            return response()->json(['error' => 'Content not found'], 404); // Return 404 if no content found
        }

        return response()->json(['compliance' => $content]);
    }

    public function dmvpage(Request $request)
    {
         // Fetch the default language code where default_status is true
         $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

         $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingQuickLink = LandingQuickLink::where('locale', $selectedLocale)->first() ?? 
                            LandingQuickLink::where('locale', 'en')->first();
        $landingHeader = LandingHeader::where('locale', $selectedLocale)->first() ?? 
                         LandingHeader::where('locale', 'en')->first();


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
    

        return Inertia::render('pages/landing/dmv', [
            'landingQuickLink' => $landingQuickLink,
            'landingHeader' => $landingHeader,
            'locales' => $this->getLocales(),
        ]);
    }

    /**
     * DMV
     * @bodyParam locale string optional User Language
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDmvContent(Request $request)
    {
         // Fetch the default language code where default_status is true
         $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' 

         $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale)); // default to 'en'
        $content = LandingQuickLink::where('locale', $selectedLocale)->value('dmv')  ??
                   LandingQuickLink::where('locale', 'en')->value('dmv');

        if (!$content) {
            return response()->json(['error' => 'Content not found'], 404); // Return 404 if no content found
        }

        return response()->json(['dmv' => $content]);
    }
    
    private function getLocales()
    {
        // return LandingHeader::pluck('locale', 'id');
        return LandingHeader::pluck('language', 'locale');
    }


}