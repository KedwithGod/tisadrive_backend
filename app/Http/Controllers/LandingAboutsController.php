<?php

namespace App\Http\Controllers;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Models\Admin\LandingAbouts;
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


class LandingAboutsController extends BaseController
{
    protected $imageUploader;

    protected $landingAbouts;

 

    public function __construct(LandingAbouts $landingAbouts, ImageUploaderContract $imageUploader)
    {
        $this->landingAbouts = $landingAbouts;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        return Inertia::render('pages/landingsite/aboutus/index');
    }


    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = LandingAbouts::query();
// dd("ssss");
        $results = $queryFilter->builder($query)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {

        return Inertia::render('pages/landingsite/aboutus/create',['app_for'=>env('APP_FOR'),]);
    }
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'hero_title' => 'required',
            'about_heading' => 'required',
            'about_title' => 'required',
            'about_para' => 'required',
            'about_lists' => 'required',
            'about_img' => 'required', 
            'ceo_name' => 'required',
            'ceo_title' => 'required',            
            'ceo_para' => 'required',
            'ceo_img' => 'required', 
            'signature' => 'required', 
            'vision_mision_heading' => 'required',
            'vision_title' => 'required',
            'vision_para' => 'required',
            'mission_title' => 'required',
            'mission_para' => 'required',
            'team_title' => 'required',
            'team_para' => 'required',
            'team_members' => 'required|array',
            'team_members.*.team_member_name' => 'required',
            'team_members.*.team_member_posision' => 'required',
            'team_members.*.team_member_image' => 'required',
            'testimonial_heading'=>'required',
            'testimonial_content' => 'required',
            'testimonial_content.*.testimonial_para' => 'required',
            'testimonial_content.*.testimonial_title_1' => 'required',
            'testimonial_content.*.testimonial_title_2' => 'required',
            'locale' => 'required',
            'language' => 'required',
        ]);

        $created_params = $request->all();   

    
        // Handle single image uploads
        if ($uploadedFile = $request->file('about_img')) {
            $created_params['about_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingAboutusImage();
        }
        if ($uploadedFile = $request->file('ceo_img')) {
            $created_params['ceo_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingAboutusImage();
        }
        if ($uploadedFile = $request->file('signature')) {
            $created_params['signature'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingAboutusImage();
        }
    
        // Handle multiple team member images and details
        $teamMembers = [];
        if ($request->has('team_members')) {
            foreach ($request->team_members as $index => $member) {
                $teamMemberData = [
                    'team_member_name' => $member['team_member_name'],
                    'team_member_posision' => $member['team_member_posision'],
                    'team_member_image' =>  null, // Initialize for the image
                ];
    
                // Handle the image upload for each team member
                if ($uploadedFile = $request->file("team_members.{$index}.team_member_image")) {
                    $teamMemberData['team_member_image'] = $this->imageUploader->file($uploadedFile)
                        ->saveLandingAboutusImage();
                }
    
                // Add the member's data to the team members array
                $teamMembers[] = $teamMemberData;
            }
        }
    
        $testimonialContent = [];
        if ($request->has('testimonial_content')) {
            foreach ($request->testimonial_content as $index => $member) {
                $testimonialContentData = [
                    'testimonial_para' => $member['testimonial_para'],
                    'testimonial_title_1' => $member['testimonial_title_1'],
                    'testimonial_title_2' =>  $member['testimonial_title_2'],
                ];
    
                // Add the member's data to the team members array
                $testimonialContent[] = $testimonialContentData;
            }
        }
    
        // Add the team_members array to the created_params
        $created_params['testimonial_content'] = json_encode($testimonialContent);
        $created_params['team_members'] = json_encode($teamMembers);
    //    dd($teamMembers); 
    
        // Store the data in the database
        LandingAbouts::create($created_params);
    
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Landing Aboutus created successfully.'
        ], 201);
    }
    

    public function edit($id)
    {
        $landingAbouts = LandingAbouts::find($id);
        // dd($landingAbouts);
        return Inertia::render(
            'pages/landingsite/aboutus/create',
            ['landingAbouts' => $landingAbouts,'app_for'=>env('APP_FOR'),]
        );
    }
    public function update(Request $request, LandingAbouts $landingAbouts)
    {
         // Validate the incoming request
         $request->validate([
            'hero_title' => 'required',
            'about_heading' => 'required',
            'about_title' => 'required',
            'about_para' => 'required',
            'about_lists' => 'required',
            'about_img' => 'required', 
            'ceo_name' => 'required',
            'ceo_title' => 'required',            
            'ceo_para' => 'required',
            'ceo_img' => 'required', 
            'signature' => 'required', 
            'vision_mision_heading' => 'required',
            'vision_title' => 'required',
            'vision_para' => 'required',
            'mission_title' => 'required',
            'mission_para' => 'required',
            'team_title' => 'required',
            'team_para' => 'required',
            'team_members' => 'required|array',
            'team_members.*.team_member_name' => 'required',
            'team_members.*.team_member_posision' => 'required',
            'team_members.*.team_member_image' => 'required',
            'testimonial_heading'=>'required',
            'testimonial_content' => 'required',
            'testimonial_content.*.testimonial_para' => 'required',
            'testimonial_content.*.testimonial_title_1' => 'required',
            'testimonial_content.*.testimonial_title_2' => 'required',
            'locale' => 'required',
            'language' => 'required',
        ]);

        $updated_params = $request->all();

        if ($uploadedFile = $request->file('about_img')) {
            $updated_params['about_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingAboutusImage();
        }
        if ($uploadedFile = $request->file('ceo_img')) {
            $updated_params['ceo_img'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingAboutusImage();
        }
        if ($uploadedFile = $request->file('signature')) {
            $updated_params['signature'] = $this->imageUploader->file($uploadedFile)
                ->saveLandingAboutusImage();
        }

        $teamMembers = [];
        if ($request->has('team_members')) {
            foreach ($request->team_members as $index => $member) {
                $teamMemberData = [
                    'team_member_name' => $member['team_member_name'],
                    'team_member_posision' => $member['team_member_posision'],
                    'team_member_image' =>  $member['team_member_image'],
                ];
    
                // Handle the image upload for each team member
                if ($uploadedFile = $request->file("team_members.{$index}.team_member_image")) {
                    $teamMemberData['team_member_image'] = $this->imageUploader->file($uploadedFile)
                        ->saveLandingAboutusImage();
                }
    
                // Add the member's data to the team members array
                $teamMembers[] = $teamMemberData;
            }
        }

        $testimonialContent = [];
        if ($request->has('testimonial_content')) {
            foreach ($request->testimonial_content as $index => $member) {
                $testimonialContentData = [
                    'testimonial_para' => $member['testimonial_para'],
                    'testimonial_title_1' => $member['testimonial_title_1'],
                    'testimonial_title_2' =>  $member['testimonial_title_2'],
                ];
    
                // Add the member's data to the team members array
                $testimonialContent[] = $testimonialContentData;
            }
        }
    
        // Add the team_members array to the created_params
        $updated_params['testimonial_content'] = json_encode($testimonialContent);
        $updated_params['team_members'] = json_encode($teamMembers);

        $landingAbouts->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Aboutus updated successfully.',
            'landingAbouts' => $landingAbouts,
        ], 201);

    }
    public function destroy(LandingAbouts $landingAbouts)
    {
        $landingAbouts->delete();

        return response()->json([
            'successMessage' => 'Aboutus deleted successfully',
        ]);
    }  

   

    public function aboutuspage(Request $request)
    {

        // Fetch the default language code where default_status is true
        $defaultLocale = Languages::where('default_status', true)->value('code') ?? 'en'; // Fallback to 'en' if not found
        // Use the default locale if none is selected
        $selectedLocale = $request->input('locale', session('selectedLocale', $defaultLocale));
        session(['selectedLocale' => $selectedLocale]); // Store in session
        // $selectedLocale = $request->input('locale', session('selectedLocale', 'en')); // default to 'en'
        session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingAbouts = LandingAbouts::where('locale', $selectedLocale)->orWhere('locale',$defaultLocale)->orWhere('locale','en')->first();
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
         $imageColumns = [ 'about_img', 'ceo_img','signature','team_member_image'];

         //     // Construct URLs for each image column
                 foreach ($imageColumns as $column) {
                     if ($landingAbouts && $landingAbouts->$column) {
                         $landingAbouts->{$column . '_url'} = asset('storage/uploads/website/images/' . $landingAbouts->$column);
                     } else {
                         $landingAbouts->{$column . '_url'} = null;
                     }
                 }

        return Inertia::render('pages/landing/aboutus', [
            'landingAbouts' => $landingAbouts,
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
