<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use Illuminate\Http\Request;
use App\Models\Admin\Setting;
use Illuminate\Support\Facades\File;
use App\Models\ThirdPartySetting;
use Kreait\Firebase\Contract\Database;

class FirebaseController  extends Controller
{
    protected $imageUploader;

    /**
     * FirebaseController constructor.
     *
     * @param 
     */
    public function __construct(ImageUploaderContract $imageUploader,Database $database)
    {
        $this->imageUploader = $imageUploader;

    }

    public function index() 
    {
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray(); // firebase data
        
        $existingJsonFile = file_exists(public_path('push-configurations/firebase.json')) ? 'firebase.json' : null;
         // Check if the `firebase.json` file exists
            $jsonFilePath = public_path('push-configurations/firebase.json');

            $firebase_json_validation = false;

            if (file_exists($jsonFilePath)) {
                // Read and decode the JSON file
                $jsonContent = json_decode(file_get_contents($jsonFilePath), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Specify the key you want to validate (e.g., 'project_id')
                    $keyToValidate = 'project_id';

                    if (isset($settings["firebase_project_id"]) && isset($jsonContent[$keyToValidate])) {
                        // Compare the database value with the JSON value
                        if ($settings["firebase_project_id"] === $jsonContent[$keyToValidate]) {
                            $firebase_json_validation = true;
                        } else {
                            $firebase_json_validation = false;
                        }
                    } else {
                        $firebase_json_validation = false;                    
                    }
                } else {
                    $firebase_json_validation = false;                
                }
            } else {
                $firebase_json_validation = false;
            }
// dd($firebase_json_validation);
        return Inertia::render('pages/firebase/index', [
            'app_for'=>env('APP_FOR'),
            'settings' => $settings,
            'existingJsonFile' => $existingJsonFile,
            'firebase_json_validation' => $firebase_json_validation
        ]);
    }
 
    public function get() 
    {
            $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();

            // dd($settings);
            return response()->json(['response' => $settings]);

    }
    
    public function update(Request $request) 
    {
        // Delete existing Firebase settings
        ThirdPartySetting::where('module', 'firebase')->delete();
    
        // Retrieve only the specified settings from the request
        $settings = $request->only([
            'firebase_database_url',
            'firebase_api_key',
            'firebase_auth_domain',
            'firebase_project_id',
            'firebase_storage_bucket',
            'firebase_messaging_sender_id',
            'firebase_app_id',
        ]);
    
        // Store the settings in the database
        foreach ($settings as $key => $setting) {
            ThirdPartySetting::create([
                'name' => $key,
                'value' => $setting,
                'module' => 'firebase'
            ]);
        }
    
// Handle the JSON file upload
if ($request->hasFile('firebase_json')) {
    $uploadedFile = $request->file('firebase_json');

    // Define the file name and destination path
    $fileName = 'firebase.json';
    $destinationPath = public_path('push-configurations');

    // Check if the directory exists, create it if not
    if (!File::isDirectory($destinationPath)) {
        File::makeDirectory($destinationPath, 0755, true, true);
    }

    // Delete the old file if it exists
    $oldFilePath = $destinationPath . '/' . $fileName;
    if (File::exists($oldFilePath)) {
        File::delete($oldFilePath);
    }

    // Move the uploaded file to the destination path
    $uploadedFile->move($destinationPath, $fileName);
}

    
        // Return a success response
        return response()->json(['message' => 'Firebase details updated successfully'], 201);
    }
    
    
    public function mapSettings()
    {
        $settings = ThirdPartySetting::where('module', 'map')->pluck('value', 'name')->toArray(); // firebase data

        return Inertia::render('pages/firebase/mapIndex', [
            'settings' => $settings,
        ]);
    }
    public function mapSettingsupdate(Request $request) 
    {
        // dd($request->all());
        // ThirdPartySetting::where('module', 'map')->delete(); // corrected delete command


        $settings = $request->only(['google_map_key','enable_vase_map']);
        
        foreach ($settings as $key => $setting) 
        {
            // dd($setting);

            ThirdPartySetting::where('name', $key)->where('module', 'map')->update(['value' => $setting]);
        }
  
    
        return response()->json(['message' => 'Map  Destails updated successfully'], 201);
    }
}
