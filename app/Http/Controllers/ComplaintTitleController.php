<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\ComplaintTitle;
use Illuminate\Http\Request;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\ComplaintTitleFilter;

class ComplaintTitleController extends Controller
{
    public function index() {
        return Inertia::render('pages/complaint_title/index');
    }

    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = ComplaintTitle::with('complaintTitleTranslationWords')->orderBy('created_at','DESC');
        // dd("ssss");
        $results = $queryFilter->builder($query)->customFilter(new ComplaintTitleFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create() {
        return Inertia::render('pages/complaint_title/create',['serviceLocation' => null]);
    }

    public function store(Request $request)
    {
         // Validate the incoming request
        $validated = $request->validate([
            'languageFields' => 'required|array',
            'complaint_type' => 'required',
            'user_type' => 'required',
        ]);

        $created_params['complaint_type'] = $validated['complaint_type'];
        $created_params['title'] = $validated['languageFields']['en'];
        $created_params['user_type'] = $validated['user_type'];

        // Create a new Title
        $complaintTitle = ComplaintTitle::create($created_params);

        foreach ($validated['languageFields'] as $code => $language) {
            $translationData[] = ['name' => $language, 'locale' => $code, 'complaint_title_id' => $complaintTitle->id];
            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->name = $language;
        }
        $complaintTitle->complaintTitleTranslationWords()->insert($translationData);
        $complaintTitle->translation_dataset = json_encode($translations_data);
        $complaintTitle->save();
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Complaint Title created successfully.',
            'complaintTitle' => $complaintTitle,
        ], 201);
    }
    public function edit($id)
    {

        $complaintTitle = ComplaintTitle::find($id);
        foreach ($complaintTitle->complaintTitleTranslationWords as $language) {
            $languageFields[$language->locale]  = $language->name;
        }
        $complaintTitle->languageFields = $languageFields ?? null;
        return Inertia::render(
            'pages/complaint_title/create',
            ['complaintTitle' => $complaintTitle,]
        );
    }
    public function update(Request $request, ComplaintTitle $complaintTitle)
    {
         // Validate the incoming request         // Validate the incoming request
        $validated = $request->validate([
            'languageFields' => 'required|array',
            'complaint_type' => 'required',
            'user_type' => 'required',
        ]);

        $updated_params['complaint_type'] = $validated['complaint_type'];
        $updated_params['title'] = $validated['languageFields']['en'];
        $updated_params['user_type'] = $validated['user_type'];



        $complaintTitle->complaintTitleTranslationWords()->delete();

        $complaintTitle->update($updated_params);
        foreach ($validated['languageFields'] as $code => $language) {
            $translationData[] = ['name' => $language, 'locale' => $code, 'complaint_title_id' => $complaintTitle->id];
            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->name = $language;
        }
        $complaintTitle->complaintTitleTranslationWords()->insert($translationData);
        $complaintTitle->translation_dataset = json_encode($translations_data);
        $complaintTitle->save();

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Complaint Title updated successfully.',
            'complaintTitle' => $complaintTitle,
        ], 201);

    }

    public function delete(ComplaintTitle $complaintTitle)
    {
        $complaintTitle->delete();
        return response()->json([
            'successMessage' => 'Complaint Title deleted successfully',
        ]);
    }   
    public function updateStatus(Request $request)
    {
        ComplaintTitle::where('id', $request->id)->update(['active'=> $request->status]);
        return response()->json([
            'successMessage' => 'Complaint Title status updated successfully',
        ]);
    }
}
