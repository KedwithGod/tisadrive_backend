<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;
use App\Models\Payment\BankInfo;
use App\Models\Method;
use App\Models\Field;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BankInfoController extends Controller
{
    //
    public function index()
    {
        return Inertia::render('pages/bankinfo/index');
    }

    public function create()
    {
        return Inertia::render('pages/bankinfo/create');
    }
    public function list(QueryFilterContract $queryFilter ,Request $request)
    {
        $query = Method::with('fields')->paginate(); // Ensure fields are loaded

        return response()->json([
            'results' => $query->items(),
            'paginator' => $query,
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'method' =>  ['required', 'string',Rule::unique('methods','method_name')],
            'fields' => 'required|array',
            'fields.*.input_field_name' => 'required|string',
            'fields.*.placeholder' => 'nullable|string',
            'fields.*.isRequired' => 'boolean',
            'fields.*.input_field_type' => 'required|string',
        ]);
    // dd($request->all());

    $formattedmethodName = $this->formatFieldName($validatedData['method']);
    

    // Step 1: Store the method
        $method = Method::create([
            'method_name' => $formattedmethodName,
        ]);
    
        // Step 2: Store each field linked to the method
        foreach ($validatedData['fields'] as $fieldData) {

            $formattedFieldName = $this->formatFieldName($fieldData['input_field_name']);

            Field::create([
                'method_id' => $method->id,
                'input_field_name' => $formattedFieldName,
                'placeholder' => $fieldData['placeholder'],
                'is_required' => $fieldData['isRequired'] ?? false,
                'input_field_type' => $fieldData['input_field_type'],
            ]);
        }
    
        return response()->json(['message' => 'Data stored successfully'], 201);
    }
    private function formatFieldName($name)
    {
        // Example: test_Ac_number dgfd => test_ac_number_dgf
        // Convert spaces and underscores to a single underscore, and convert everything to lowercase
        return Str::slug(str_replace(' ', '_', $name), '_');
    }

    public function edit($id)
    {
        // Fetch the method and associated fields
        $method = Method::with('fields')->findOrFail($id);
        
        return Inertia::render('pages/bankinfo/edit', [
            'method' => $method,
        ]);
    }
    
    public function update(Request $request, Method $method)
    {
        $validatedData = $request->validate([
            'method' => ['required', 'string',Rule::unique('methods','method_name')->ignore($method->id)],
            'fields' => 'required|array',
            'fields.*.id' => 'sometimes|integer|exists:fields,id',
            'fields.*.input_field_name' => 'required|string',
            'fields.*.placeholder' => 'nullable|string',
            'fields.*.is_required' => 'boolean',
            'fields.*.input_field_type' => 'required|string',
            'removedFields' => 'array', // Validate removed fields array
            'removedFields.*' => 'integer|exists:fields,id'
        ]);
    
        // Step 1: Update the method
        $method->update(['method_name' => $validatedData['method']]);
    
        // Step 2: Delete removed fields
        if (!empty($validatedData['removedFields'])) {
            Field::whereIn('id', $validatedData['removedFields'])->delete();
        }
    
        // Step 3: Update or create fields
        foreach ($validatedData['fields'] as $fieldData) {
            if (isset($fieldData['id'])) {
                $field = Field::find($fieldData['id']);
                $field->update([
                    'input_field_name' => $fieldData['input_field_name'],
                    'placeholder' => $fieldData['placeholder'],
                    'is_required' => $fieldData['is_required'],
                    'input_field_type' => $fieldData['input_field_type'],
                ]);
            } else {
                $method->fields()->create([
                    'input_field_name' => $fieldData['input_field_name'],
                    'placeholder' => $fieldData['placeholder'],
                    'is_required' => $fieldData['is_required'],
                    'input_field_type' => $fieldData['input_field_type'],
                ]);
            }
        }
    
        return response()->json(['message' => 'Data updated successfully'], 200);
    }

    public function destroy(Method $method)
    {
        $method->delete();

        return response()->json([
            'successMessage' => 'Bank Info deleted successfully',
        ]);
    } 


    public function updateStatus(Request $request)
    {
        // dd($request->all());
        Method::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Field status updated successfully',
        ]);


    }


}
