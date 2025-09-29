<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Master\Email;
use App\Base\Services\ImageUploader\ImageUploader;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
    protected $imageUploader;

    protected $emails;

 

    public function __construct(Email $emails, ImageUploaderContract $imageUploader)
    {
        $this->emails = $emails;
        $this->imageUploader = $imageUploader;
    }
    public function index() {
        return Inertia::render('pages/email_template/index');
    }

    public function list()
    {
        // $query = MailTemplate::query();
        $query = Email::query()->paginate();

        // $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

        return response()->json([
            'results' => $query->items(),
            'paginator' => $query,
        ]);
    }

    public function create() {

        return Inertia::render('pages/email_template/create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email_subject' => 'required',
            'logo_img' => 'required',
            'mail_body' => 'required',
            'button_name' => 'required',
            'button_url' => 'required',
            'banner_img' => 'required', 
            'footer' => 'required',
            'footer.*.footer_content' => 'required',
            'footer.*.footer_copyrights' => 'required',
            'footer.*.footer_fblink' => 'required',
            'footer.*.footer_instalink' => 'required',
            'footer.*.footer_twitterlink' => 'required',
            'footer.*.footer_linkedinlink' => 'required',
        ]);

        $created_params = $request->all();   
        dd($created_params);

    
        // Handle single image uploads
        if ($uploadedFile = $request->file('banner_img')) {
            $created_params['banner_img'] = $this->imageUploader->file($uploadedFile)
                ->saveEmailTemplateImage();
        }
        if ($uploadedFile = $request->file('logo_img')) {
            $created_params['logo_img'] = $this->imageUploader->file($uploadedFile)
                ->saveEmailTemplateImage();
        }
    
        $footer = [];
        if ($request->has('footer')) {
            foreach ($request->footer as $index => $member) {
                $footerContentData = [
                    'footer_content' => $member['footer_content'],
                    'footer_copyrights' => $member['footer_copyrights'],
                    'footer_fblink' =>  $member['footer_fblink'],
                    'footer_instalink' => $member['footer_instalink'],
                    'footer_twitterlink' => $member['footer_twitterlink'],
                    'footer_linkedinlink' =>  $member['footer_linkedinlink'],
                ];
    
                // Add the member's data to the team members array
                $footer[] = $footerContentData;
            }
        }
    
        // Add the team_members array to the created_params
        $created_params['footer'] = json_encode($footer);
        $created_params['active'] = true;
    //    dd($teamMembers); 
    
        // Store the data in the database
        Email::create($created_params);
    
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Mail Template created successfully.'
        ], 201);
    }
    public function edit($id)
    {
        $emails = Email::find($id);
        return Inertia::render(
            'pages/email_template/create',
            ['emails' => $emails]
        );
    }

    public function update(Request $request, Email $emails)
    {
         // Validate the incoming request
         $request->validate([
            'email_subject' => 'required',
            'logo_img' => 'required',
            'mail_body' => 'required',
            'button_name' => 'required',
            'button_url' => 'required',
            'banner_img' => 'required', 
            'footer' => 'required',
            'footer.*.footer_content' => 'required',
            'footer.*.footer_copyrights' => 'required',
            'footer.*.footer_fblink' => 'required',
            'footer.*.footer_instalink' => 'required',
            'footer.*.footer_twitterlink' => 'required',
            'footer.*.footer_linkedinlink' => 'required',
        ]);

        $updated_params = $request->all();

        if ($uploadedFile = $request->file('banner_img')) {
            $updated_params['banner_img'] = $this->imageUploader->file($uploadedFile)
                ->saveEmailTemplateImage();
        }
        if ($uploadedFile = $request->file('logo_img')) {
            $updated_params['logo_img'] = $this->imageUploader->file($uploadedFile)
                ->saveEmailTemplateImage();
        }
    
        $footer = [];
        if ($request->has('footer')) {
            foreach ($request->footer as $index => $member) {
                $footerContentData = [
                    'footer_content' => $member['footer_content'],
                    'footer_copyrights' => $member['footer_copyrights'],
                    'footer_fblink' =>  $member['footer_fblink'],
                    'footer_instalink' => $member['footer_instalink'],
                    'footer_twitterlink' => $member['footer_twitterlink'],
                    'footer_linkedinlink' =>  $member['footer_linkedinlink'],
                ];
    
                // Add the member's data to the team members array
                $footer[] = $footerContentData;
            }
        }
        // Add the team_members array to the created_params
        $updated_params['footer'] = json_encode($footerContentData);
        $updated_params['active'] = true;

        $emails->update($updated_params);         

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Mail Template updated successfully.',
            'emails' => $emails,
        ], 201);

    }
    public function destroy(Email $emails)
    {
        $emails->delete();

        return response()->json([
            'successMessage' => 'Mail Template deleted successfully',
        ]);
    }  

}
