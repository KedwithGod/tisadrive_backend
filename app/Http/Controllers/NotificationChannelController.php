<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Admin\InvoiceConfiguration;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Services\ImageUploader\ImageUploader;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin\NotificationChannel;
use App\Models\User;
use App\Models\Admin\NotificationChannelTranslation;

class NotificationChannelController extends Controller
{

    protected $imageUploader;

    protected $notification;

 

    public function __construct(NotificationChannel $notification, ImageUploaderContract $imageUploader)
    {
        $this->notification = $notification;
        $this->imageUploader = $imageUploader;
    }
   
    public function index() {
        return Inertia::render('pages/notification_channel/index');
       
    } 
    public function list(Request $request)
    {
        // $query = NotificationChannel::with('notificationChannelTranslationWords')->paginate();
        $query = NotificationChannel::with('notificationChannelTranslationWords')->orderBy('created_at','DESC')->paginate();
        // dd( $query);

        return response()->json([
            'results' => $query->items(),
            'paginator' => $query,
        ]);
    }
    public function edit($id)
    {

        $notification = NotificationChannel::find($id);
        $languageFields = [];
        foreach ($notification->notificationChannelTranslationWords as $language) {
            $languageFields[$language->locale] = [
                'email_subject' => $language->email_subject,
                'mail_body' => $language->mail_body,
                'button_name' => $language->button_name,
                'footer_content' => $language->footer_content,
                'footer_copyrights' => $language->footer_copyrights,
                'notification_channel_id' => $notification->id,
            ];
        }
        $notification->languageFields = $languageFields ?? null;

        return Inertia::render(
            'pages/notification_channel/edit',
            ['notification' => $notification,'app_for'=>env('APP_FOR'),]
        );
    }
    public function editPushTemplate($id)
    {

        $notification = NotificationChannel::find($id);
        $languageFields = [];

        foreach ($notification->notificationChannelTranslationWords as $language) {
            $languageFields[$language->locale] = [
                'push_title' => $language->push_title,
                'push_body' => $language->push_body,
                'notification_channel_id' => $notification->id,
            ];
        }
        $notification->languageFields = $languageFields ?? null;

        return Inertia::render(
            'pages/notification_channel/push_template/edit',
            ['notification' => $notification,'app_for'=>env('APP_FOR'),]
        );
    }

   
    public function userInvoiceEdit($id)
    {

        $notification = NotificationChannel::find($id);
        $languageFields = [];

        foreach ($notification->notificationChannelTranslationWords as $language) {
            $languageFields[$language->locale] = [
                'email_subject' => $language->email_subject,
                'mail_body' => $language->mail_body,
                'button_name' => $language->button_name,
                'footer_content' => $language->footer_content,
                'footer_copyrights' => $language->footer_copyrights,
                'notification_channel_id' => $notification->id,
            ];
        }
        $notification->languageFields = $languageFields ?? null;

        return Inertia::render(
            'pages/notification_channel/user_invoice_edit',
            ['notification' => $notification,'app_for'=>env('APP_FOR'),]
        );
    }
    public function driverInvoiceEdit($id)
    {

        $notification = NotificationChannel::find($id);
        $languageFields = [];

        foreach ($notification->notificationChannelTranslationWords as $language) {
            $languageFields[$language->locale] = [
                'email_subject' => $language->email_subject,
                'mail_body' => $language->mail_body,
                'button_name' => $language->button_name,
                'footer_content' => $language->footer_content,
                'footer_copyrights' => $language->footer_copyrights,
                'notification_channel_id' => $notification->id,
            ];
        }
        $notification->languageFields = $languageFields ?? null;

        return Inertia::render(
            'pages/notification_channel/driver_invoice_edit',
            ['notification' => $notification,'app_for'=>env('APP_FOR'),]
        );
    }
    public function update(Request $request, NotificationChannel $notification,)
    {
        // dd($request);
        // dd($notification);
        //  Validate the incoming request
         $validated = $request->validate([
            'email_subject' => 'required',
            'logo_img' => 'required',
            'mail_body' => 'required',
            'button_name' => 'required',
            'button_url' => 'required',
            'show_button' =>'required',
            'show_img' => 'required',
            'show_fbicon' => 'required',
            'show_instaicon' => 'required',
            'show_twittericon' =>'required',
            'show_linkedinicon' => 'required',
            'banner_img' => 'required',
            'footer_content' => 'required',
            'footer_copyrights' => 'required',
            'footer_fblink' => 'required',
            'footer_instalink' => 'required',
            'footer_twitterlink' => 'required',
            'footer_linkedinlink' => 'required',
        ]);
        // dd($validated);

        $updated_params['email_subject'] = $validated['email_subject']['en'];
        $updated_params['mail_body'] = $validated['mail_body']['en'];
        $updated_params['button_name'] = $validated['button_name']['en'];
        $updated_params['footer_content'] = $validated['footer_content']['en'];
        $updated_params['footer_copyrights'] = $validated['footer_copyrights']['en'];
        
        $updated_params['button_url'] = $validated['button_url'];
        $updated_params['show_fbicon'] = $validated['show_fbicon'];
        $updated_params['show_button'] = $validated['show_button'];
        $updated_params['show_img'] = $validated['show_img'];
        $updated_params['show_instaicon'] = $validated['show_instaicon'];
        $updated_params['show_twittericon'] = $validated['show_twittericon'];
        $updated_params['show_linkedinicon'] = $validated['show_linkedinicon'];

        if ($uploadedFile = $request->file('banner_img')) {
            $updated_params['banner_img'] = $this->imageUploader->file($uploadedFile)
                ->saveEmailTemplateImage();
        }
        if ($uploadedFile = $request->file('logo_img')) {
            $updated_params['logo_img'] = $this->imageUploader->file($uploadedFile)
                ->saveEmailTemplateImage();
        }

        $updated_params['footer'] = [
            'footer_fblink' => $validated['footer_fblink'],
            'footer_instalink' => $validated['footer_instalink'],
            'footer_twitterlink' => $validated['footer_twitterlink'],
            'footer_linkedinlink' => $validated['footer_linkedinlink'],
        ];

        // $notification->where('id', $request->id)->update($updated_params);
        // $notification->notificationChannelTranslationWords()->delete();
        // dd($updated_params);


        $notification->update($updated_params);
        // dd($notification);
        $translationData = [];
        $translations_data = [];


      // Retrieve the existing translation dataset and decode it into an associative array
$existingTranslationsData = json_decode($notification->translation_dataset, true) ?? [];
// $notification->notificationChannelTranslationWords()->delete();

// Prepare translation data
foreach ($validated['email_subject'] as $locale => $title) {
    // Retrieve the existing record if it exists
    $existingRecord = $notification->notificationChannelTranslationWords()
        ->where('locale', $locale)
        ->where('notification_channel_id', $notification->id)
        ->first();

    // If the record exists, merge with existing data
    $data = [
        'locale' => $locale,
        'notification_channel_id' => $notification->id,
    ];

    if ($existingRecord) {
        $data = array_merge($existingRecord->toArray(), $data);
    }

    // Add only provided fields to the update data
    if (isset($validated['email_subject'][$locale])) {
        $data['email_subject'] = $validated['email_subject'][$locale];
    }
    if (isset($validated['mail_body'][$locale])) {
        $data['mail_body'] = $validated['mail_body'][$locale];
    }
    if (isset($validated['button_name'][$locale])) {
        $data['button_name'] = $validated['button_name'][$locale];
    }
    if (isset($validated['footer_content'][$locale])) {
        $data['footer_content'] = $validated['footer_content'][$locale];
    }
    if (isset($validated['footer_copyrights'][$locale])) {
        $data['footer_copyrights'] = $validated['footer_copyrights'][$locale];
    }

    // Ensure `push_title` is always set if required
    if (!isset($data['push_title'])) {
        $data['push_title'] = $existingRecord->push_title ?? ''; // Default empty string if missing
    }
    if (!isset($data['push_body'])) {
        $data['push_body'] = $existingRecord->push_body ?? ''; // Default empty string if missing
    }

    // Update or create record
    if ($existingRecord) {
        $existingRecord->update($data);
    } else {
        $notification->notificationChannelTranslationWords()->create($data);
    }

    // Update translation dataset
    // $currentTranslation = $existingTranslationsData[$locale] ?? [];
    $currentTranslation = isset($existingTranslationsData[$locale]) && is_array($existingTranslationsData[$locale]) 
           ? $existingTranslationsData[$locale] 
            : [];
    $existingTranslationsData[$locale] = array_merge($currentTranslation, $data);
}

// Save updated dataset
$notification->translation_dataset = json_encode($existingTranslationsData);
$notification->save();




        

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Mail Template updated successfully.',
            'notification' => $notification,
        ], 201);

    }


    public function updatePushTemplate(Request $request, NotificationChannel $notification)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'push_title' => 'required',
            'push_body' => 'required',
        ]);
    
        // Update the main notification channel fields
        $updated_params = [
            'push_title' => $validated['push_title']['en'],
            'push_body' => $validated['push_body']['en'],
        ];
        $notification->update($updated_params);

        // Retrieve the existing translation dataset and decode it into an associative array
        $existingTranslationsData = json_decode($notification->translation_dataset, true) ?? [];

        // Prepare translation data
        foreach ($validated['push_title'] as $locale => $title) {
            $push_body = $validated['push_body'][$locale] ?? '';
    
            // Update or create translations for each locale
            $notification->notificationChannelTranslationWords()->updateOrCreate(
                ['locale' => $locale, 'notification_channel_id' => $notification->id], // Match existing record
                ['push_title' => $title, 
                'push_body' => $push_body,
                ] // Update with new data
            );
            // $currentTranslation = $existingTranslationsData[$locale] ?? [];
            $currentTranslation = isset($existingTranslationsData[$locale]) && is_array($existingTranslationsData[$locale]) 
           ? $existingTranslationsData[$locale] 
            : [];
            $existingTranslationsData[$locale] = array_merge($currentTranslation, [
                'locale' => $locale,
                'push_title' => $title,
                'push_body' => $push_body ?? ($currentTranslation['push_body'] ?? ''),
            ]);
        }
        $notification->translation_dataset = json_encode($existingTranslationsData);
        $notification->save();
    
        // Return a response
        return response()->json([
            'successMessage' => 'Push Template updated successfully.',
            'notification' => $notification,
        ], 201);
    }
    

    public function updateStatus(Request $request)
{

    NotificationChannel::where('id', $request->id)
        ->update([
            'push_notification' => $request->push_notification,
            'mail' => $request->mail,
            'sms' => $request->sms,
        ]);

    return response()->json([
        'successMessage' => 'Status updated successfully',
    ]);
}



    
}
