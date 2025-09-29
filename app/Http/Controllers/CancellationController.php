<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\CancellationReason;
use Illuminate\Http\Request;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\CancellationReasonFilter;
use Illuminate\Support\Facades\Log;

class cancellationController extends Controller
{
    public function index() {
        return Inertia::render('pages/cancellation/index');
    }

    // List of Reason
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = CancellationReason::with('cancellationReasonTranslationWords')->orderBy('created_at','DESC');
        // dd("ssss");
        $results = $queryFilter->builder($query)->customFilter(new CancellationReasonFilter)->paginate();


        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function create() {

        return Inertia::render('pages/cancellation/create',);
    }

    public function store(Request $request)
    {
         // Validate the incoming request
        $validated = $request->validate([
            'languageFields' => 'required|array',
            'user_type' => 'required',
            'arrival_status' => 'required',
            'payment_type' => 'required',
            'transport_type' => 'required',
            // 'compensate_from' => 'required',
            'compensate_from' => 'required_if:payment_type,compensate|nullable|in:compensate_from_user,compensate_from_driver',
        ]);

        $created_params = $validated;
        $created_params['reason'] = $validated['languageFields']['en'];

        // Create a new Title
        $cancellationReason = CancellationReason::create($created_params);

        foreach ($validated['languageFields'] as $code => $language) {
            $translationData[] = ['name' => $language, 'locale' => $code, 'cancellation_reason_id' => $cancellationReason->id];
            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->name = $language;
        }
        $cancellationReason->cancellationReasonTranslationWords()->insert($translationData);
        $cancellationReason->translation_dataset = json_encode($translations_data);
        $cancellationReason->save();
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Cancellation Reason created successfully.',
            'cancellationReason' => $cancellationReason,
        ], 201);
    }
    public function edit($id)
    {

        $cancellationReason = CancellationReason::find($id);
        foreach ($cancellationReason->cancellationReasonTranslationWords as $language) {
            $languageFields[$language->locale]  = $language->name;
        }
        $cancellationReason->languageFields = $languageFields ?? null;
        return Inertia::render(
            'pages/cancellation/create',
            ['cancellationReason' => $cancellationReason,]
        );
    }
    public function update(Request $request, CancellationReason $cancellationReason)
    {
         // Validate the incoming request
        $validated = $request->validate([
            'languageFields' => 'required|array',
            'user_type' => 'required',
            'arrival_status' => 'required',
            'payment_type' => 'required',
            'transport_type' => 'required',
            // 'compensate_from' => 'required',
            'compensate_from' => 'required_if:payment_type,compensate|nullable|in:compensate_from_user,compensate_from_driver',
        ]);

        $updated_params = $request->only(['user_type','arrival_status','payment_type','transport_type',
        'compensate_from',
    ]);
        $updated_params['reason'] = $validated['languageFields']['en'];


        $cancellationReason->cancellationReasonTranslationWords()->delete();
        $cancellationReason->update($updated_params);
        foreach ($validated['languageFields'] as $code => $language) {
            $translationData[] = ['name' => $language, 'locale' => $code, 'cancellation_reason_id' => $cancellationReason->id];
            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->name = $language;
        }
        $cancellationReason->cancellationReasonTranslationWords()->insert($translationData);
        $cancellationReason->translation_dataset = json_encode($translations_data);
        $cancellationReason->save();

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Cancellation Reason updated successfully.',
            'cancellationReason' => $cancellationReason,
        ], 201);

    }

    public function delete(CancellationReason $cancellationReason)
    {
        $cancellationReason->delete();
        return response()->json([
            'successMessage' => 'Cancellation Reason deleted successfully',
        ]);
    }   
    public function updateStatus(Request $request)
    {
        CancellationReason::where('id', $request->id)->update(['active'=> $request->status]);
        return response()->json([
            'successMessage' => 'Cancellation Reason status updated successfully',
        ]);
    }
}
