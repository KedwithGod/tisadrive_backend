<?php

namespace App\Http\Controllers\Web\Admin;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Languages;
use Illuminate\Support\Facades\File;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Models\Admin\ServiceLocation;
use Google\Cloud\Translate\V2\TranslateClient;  
use App\Models\Admin\AdminNotification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class LanguagesController extends Controller
{
    public function index()
    {
        return Inertia::render('pages/languages/index',['app_for'=>env('APP_FOR')]);
    }


    public function CurrenetLanguagelist()
    {
        // $path = public_path() . "/lang/lang.json"; 

        // $language = json_decode(file_get_contents($path), true); 

        $language_list = Languages::where('active',true)->get();
        
        return response()->json(['success'=>true,'message'=>'success','data'=>$language_list]);


    }

    public function serviceLocationlist()
    {
  
        $service_location = ServiceLocation::where('active',true)->get();
        
        return response()->json(['success'=>true,'message'=>'success','data'=>$service_location]);

    }

    public function adminNotification()
    {
  
        $adminNotication = AdminNotification::where('is_read', false)->get();
        
        return response()->json(['success'=>true,'message'=>'success','data'=>$adminNotication]);

    }
    // readNotification
    public function readNotification(Request $request)
    {
        // dd($request->has('id'));

        if($request->has('id'))
        {
            $adminNotication = AdminNotification::where('id', $request->id)->update(['is_read'=> true]);

        }else{

            $adminNotication = AdminNotification::where('is_read', 0)->get();

            foreach($adminNotication as $notification)
            {
                $notification->update(['is_read'=>true]);
            }

        }

        
        return response()->json(['success'=>true,'message'=>'success']);

    }        
    public function list(QueryFilterContract $queryFilter)
    {
        
        $query = Languages::query();

        $results = $queryFilter->builder($query)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);

       
    }


    public function create()
    {

        if(env('APP_FOR') == 'demo') {
            return redirect('/languages');
        }
        return Inertia::render('pages/languages/create');
    }


    public function store(Request $request)
    {
        $explode_data = explode(',', $request->code);

        $code = $explode_data[0];
        $name = $explode_data[1];

            $explode_data      = explode(',', $request->language);
            $folderPath        = public_path("/lang/en");
            $destinationFolder = public_path("/lang/".$code); 

            if (!is_dir($destinationFolder)) { 
                File::makeDirectory($destinationFolder, 0777, true); 
           
                if (is_dir($folderPath)) {
                    $files = File::files($folderPath);
                    foreach ($files as $file) {
                        $filename    = pathinfo($file, PATHINFO_FILENAME);
                        $extension   = pathinfo($file, PATHINFO_EXTENSION);
                        $newFilename = $filename . '.' . $extension; 
                        File::copy($file, $destinationFolder . '/' . $newFilename);
                        
                    }
                    
                $Languages = Languages::create([
                'name'=>$name,
                'code'=>$code,
                'direction'=>$request->direction]);
                    
            return response()->json([
            'successMessage' => 'Language created successfully.',
        ], 201);
                  
                } else {
                    return response()->json([
                        'alertMessage' => 'Translation not found.',
                    ], 400);
                }

            }



       return $this->respondSuccess();
        
    }  
    public function delete($id)
    {
        // Find the language by ID
        $language = Languages::find($id);

        if ($language) {
            // Get the language code
            $code = $language->code;

            // Define the folder path
            $destinationFolder = public_path("/lang/" . $code);

            // Check if the folder exists
            if (is_dir($destinationFolder)) {
                // Delete the folder and its contents
                File::deleteDirectory($destinationFolder);
            }

            // Delete the language from the database
            $language->delete();

            return response()->json([
                'successMessage' => 'Language deleted successfully',
            ], 201);
        }

        return response()->json([
            'alertMessage' => 'Language not found.',
        ], 404);
    }

    public function status(Request $request, Languages $language)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $request->validate([ 'status_type' => 'required|string' ]);
        if ($request->status_type == "status") {
            $updated_status = $language->active ? 0 : 1;
            $language->active = $updated_status;

        } 

        $language->save();
        return response()->json(['status' => 'success', 'check_status' => $updated_status  ]);
    }


    // Browse
    public function browse(Request $request, $id)
    {
        $language = Languages::find($id); // Adjust as needed to fetch the language data
        $name = $language->name;
        $code = $language->code;

        return Inertia::render('pages/languages/browse',['language_id'=>$id,'language_name'=>$name,'language_code'=>$code,'app_for'=>env('APP_FOR'),]);

    }


    // Load Translation
    public function loadTranslation($id){
        $group = request()->group;   
        
        $code = Languages::where('id',$id)->pluck('code')->first();

        $default_lang_path = public_path('lang/en/'.$group.'.json');
        $translated_lang_path = public_path('lang/'.$code.'/'.$group.'.json');

        $default_lang_data = $this->getTranslationContentFromFilePath($default_lang_path);
        

    $translated_data = $this->getTranslationContentFromFilePath($translated_lang_path);


  $data = [];
  $i = 0;
foreach ($default_lang_data as $key => $current_value) {

  $translated_value = isset($translated_data[$key]) ? $translated_data[$key] : $current_value;

  $data[$i]['key_value'] = $key;
  $data[$i]['current_value'] = $current_value;
  $data[$i]['translated_value'] = $translated_value;
  $i++;

}


  return $this->respondSuccess($data);
        
    }

    public function downloadTranslation($id)
    {
        $group = request()->group;
        $code = Languages::where('id', $id)->pluck('code')->first();
    
        $translated_lang_path = public_path('lang/' . $code . '/' . $group . '.json');
    
        if (!file_exists($translated_lang_path)) {
            abort(404, 'File not found');
        }
    
        $translated_data = json_decode(file_get_contents($translated_lang_path), true);
    
        // Re-encode the data with JSON_UNESCAPED_UNICODE to preserve the Unicode characters
        $encodedData = json_encode($translated_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
        // Create a response for file download
        return response($encodedData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $group . '_' . $code . '.json"',
        ]);
    }
    



    public function getTranslationContentFromFilePath($default_lang_path)
    {

        // Check if the file exists and is readable
    if (file_exists($default_lang_path) && is_readable($default_lang_path)) {

    // Use file_get_contents() or fopen() to read the file content
    $default_lang_content = file_get_contents($default_lang_path); // Option 1: Simpler approach
  
    // Decode the JSON data
    $default_lang = json_decode($default_lang_content, true); // Use true for associative array
    
    // Check if decoding was successful and the 'pages_names' object exists
    if (is_array($default_lang)) {
        // Access and print the content of the 'pages_names' object
      return $default_lang_data = $default_lang;

     
    } else {

        return $this->respondInternalError();
        // TODO return exception 
    }
  } else {

    return $this->respondInternalError();
    // TODO return exception
    // echo "Error: File not found or not readable.\n";
  }

    }

    /**
     * Update Translate
     * 
     * 
     * */
    public function updateTranslate($id, Request $request){

        $code = Languages::where('id',$id)->pluck('code')->first();
        
        $keyword = $request->current_value;

        $group = $request->group;

        $key_value = $request->key_value;

        $translated_lang_path = public_path('lang/'.$code.'/'.$group.'.json');

        $translated_lang_data = json_decode(File::get($translated_lang_path), true);

        $translated_lang_data[$key_value] = $keyword;

        $encodedData = json_encode($translated_lang_data, JSON_PRETTY_PRINT);

        File::put($translated_lang_path, $encodedData); 

        return response()->json(['success'=>true,'message'=>'success','data'=>$keyword]);



    }


    /**
     * Auto Translate 
     * 
     * */
    public function autoTranslate($id, Request $request){


        $code = Languages::where('id',$id)->pluck('code')->first();

        $keyword = $request->current_value;

        $group = $request->group;

        $key_value = $request->key_value;

        $translated_lang_path = public_path('lang/'.$code.'/'.$group.'.json');

        $translated_lang_data = json_decode(File::get($translated_lang_path), true);

        $result = $this->googleTranslate($keyword,$code);

        $translated_lang_data[$key_value] = $result;

        $encodedData = json_encode($translated_lang_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

        File::put($translated_lang_path, $encodedData); 

        return response()->json(['success'=>true,'message'=>'success','data'=>$result]);

    }

   /**
    * Auto Translate All keywords
    * 
    * */ 
   public function autoTranslateAll($id, Request $request)
   {

        $code = Languages::where('id',$id)->pluck('code')->first();

        $group = $request->group;

        $default_lang_path = public_path('lang/en/'.$group.'.json');
       
        $default_lang_data = json_decode(File::get($default_lang_path), true);

        $target_lang_path = public_path('lang/'.$code.'/'.$group.'.json');

        $translated_lang_data = json_decode(File::get($target_lang_path), true);


        foreach ($default_lang_data as $key => $translation) {

            $keyword = $default_lang_data[$key];

            $result = $this->googleTranslate($keyword,$code);

            $translated_lang_data[$key] = $result;


        }

        $encodedData = json_encode($translated_lang_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        // dd($encodedData);
        

        File::put($target_lang_path, $encodedData); 



        return $this->loadTranslation($id);

   }

   /**
    * Google Translate
    * 
    * */
   public function googleTranslate($keyword,$target_lang){


    $apiKey = get_map_settings('google_map_key');
        $projectId='safargo-c6a84';

        $translate = new TranslateClient([
            'key' => $apiKey,
            'projectId' => $projectId,
         ]);

        $result = $translate->translate($keyword, [
                'target' => $target_lang,
            ]); 


        return $result['text'];

   }

   function updateAppLocale(Languages $lang)
   {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        Languages::where('default_status',1)->update(['default_status'=>0]);

        Languages::where('id',$lang->id)->update(['default_status'=>1]);

        $locale = Languages::where('default_status',1)->first()->code;


        return response()->json([
            'successMessage' => 'Language Updated successfully.',
        ], 201);
   }
}

