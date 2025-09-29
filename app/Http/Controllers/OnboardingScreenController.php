<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\Onboarding;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Base\Services\ImageUploader\ImageUploader;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Models\Admin\OnboardingTranslation;

class OnboardingScreenController extends Controller
{

    protected $imageUploader;

    public function __construct(ImageUploaderContract $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }
    public function index() {
        return Inertia::render('pages/onboarding_screen/index',['app_for'=>env('APP_FOR'),]);
    }

    public function list(QueryFilterContract $queryFilter ,Request $request)
    {
        // $query = Onboarding::orderBy('sn_o')->paginate();
        $query = Onboarding::with('onboardingTranslationWords')->orderBy('sn_o','ASC')->paginate();

        // dd($query);
        // $results = $queryFilter->builder($query)->paginate();
        // dd($results);

        return response()->json([
            'results' => $query->items(),
            'paginator' => $query,
        ]);
    }
    public function edit($id)
    {

        $onboarding = Onboarding::find($id);

        $languageFields = [];

        foreach ($onboarding->onboardingTranslationWords as $language) {
            $languageFields[$language->locale] = [
                'title' => $language->title,
                'description' => $language->description,
                'onboarding_screen_id' => $onboarding->id,
            ];
        }

        $onboarding->languageFields = $languageFields ?? null;
        // dd( $onboarding);
        return Inertia::render(
            'pages/onboarding_screen/editPage',
            ['onboarding' => $onboarding,'app_for'=>env('APP_FOR'),]
        );
    }

    public function update(Onboarding $onboarding, Request $request) {
         // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'onboarding_image' => 'required',
        ]);
        
        $updated_params['title'] = $validated['title']['en'];
        $updated_params['description'] = $validated['description']['en'];

        // $updated_params = $request->only(['id','title','description','onboarding_image']);

        $updated_params['active'] = true;

        if ($uploadedFile = $request->file('onboarding_image')) {
            $updated_params['onboarding_image'] = $this->imageUploader->file($uploadedFile)
                ->OnboardingImage();
        }
        // dd($updated_params);
        $onboarding->update($request->all());
        // $onboarding->where('id', $request->id)->update($updated_params);

         
         $onboarding->onboardingTranslationWords()->delete();
         // dd($updated_params);
//  dd($onboarding);
 
         $onboarding->update($updated_params);
         $translationData = [];
         $translations_data = [];
         foreach ($validated['title'] as $code => $language) {
             $translationData[] = [
                 'title' => $language,
                 'description' => $validated['description'][$code] ?? '',
                 'locale' => $code,
                 'onboarding_screen_id' => $onboarding->id,
             ];
             $translations_data[$code] = new \stdClass();
             $translations_data[$code]->locale = $code;
             $translations_data[$code]->title = $language; // Use the string value directly
             $translations_data[$code]->description = $validated['description'][$code]; // Fetch corresponding value
         }
         $onboarding->onboardingTranslationWords()->insert($translationData);
         $onboarding->translation_dataset = json_encode($translations_data);
         $onboarding->save();
       

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Onboarding created  updated successfully.',
            'onboarding' => $onboarding,
        ], 201);
    }

    public function updateStatus(Request $request)
    {
        // dd($request->all());
        Onboarding::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Onboarding status updated successfully',
        ]);
    }
}
