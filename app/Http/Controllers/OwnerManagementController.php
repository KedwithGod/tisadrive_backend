<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Models\Admin\OwnerNeededDocument;

use Illuminate\Http\Request;

class OwnerManagementController extends Controller
{
    public function ownerNeededDocumentIndex() {
        return Inertia::render('pages/owner_needed_documents/index');
    }

    public function ownerNeededDocumentList(QueryFilterContract $queryFilter,) {
        $query = OwnerNeededDocument::query();
        $results = $queryFilter->builder($query)->paginate();
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function ownerNeededDocumentCreate() 
    {
        return Inertia::render('pages/owner_needed_documents/create');
    }

    public function ownerNeededDocumentStore(Request $request) 
    {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required',
            'has_identify_number' => 'required',
            'has_expiry_date' => 'required',
        ]);
        if($request->has_identify_number){
            $validated['identify_number_locale_key'] = $request->identify_number_locale_key;
        }

        $validated['active'] = true;
        $validated['image_type'] = $request->image_type;
        $validated['document_name_front'] = $request->document_name_front;
        $validated['document_name_back'] = $request->document_name_back;

        $validated['is_editable'] = $request->is_editable;
        $validated['is_required'] = $request->is_required;



        $document = OwnerNeededDocument::create($validated);
        return response()->json([
            'successMessage' => 'Document created successfully.',
            'result' => $document,
        ],201);

    }
    public function ownerNeededDocumentEdit(OwnerNeededDocument $document,Request $request) 
    {
        return Inertia::render('pages/owner_needed_documents/create',['document'=>$document]);
    }
    public function ownerNeededDocumentUpdate(OwnerNeededDocument $document,Request $request) 
    {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required',
            'has_identify_number' => 'required',
            'has_expiry_date' => 'required',
        ]);
        $validated['image_type'] = $request->image_type;
        $validated['document_name_front'] = $request->document_name_front;
        $validated['document_name_back'] = $request->document_name_back;

        $validated['is_editable'] = $request->is_editable;
        $validated['is_required'] = $request->is_required;


        if($request->has_identify_number){
            $validated['identify_number_locale_key'] = $request->identify_number_locale_key;
        }
        $document->update($validated);
        return response()->json([
            'successMessage' => 'Document Updated successfully.',
            'result' => $document,
        ],201);
    }
    public function ownerNeededDocumentToggle(Request $request) {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        OwnerNeededDocument::where('id',$request->id)->update(['active'=>$request->status]);
        return response()->json([
            'successMessage' => 'Document Status updated successfully.',
        ],201);
    }
    public function ownerNeededDocumentDelete(OwnerNeededDocument $document) {
        if(env('APP_FOR') == 'demo') {
            return response()->json([
                'alertMessage' => 'You are not Authorized',
            ],403);
        }
        // dd($document);
        $document->delete();
        return response()->json([
            'successMessage' => 'Document Deleted successfully.',
        ],201);
    }
}
