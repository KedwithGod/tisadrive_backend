<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\Faq;
use Illuminate\Http\Request;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;

class FaqController extends Controller
{
    public function index() {
        return Inertia::render('pages/faq/index');
    }
    public function list(QueryFilterContract $queryFilter)
    {
        $query = Faq::query();

        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create() {

        return Inertia::render('pages/faq/create',['faq' => null]);
    }
    public function store(Request $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
         // Validate the incoming request
         $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'user_type' => 'required',
        ]);

        $created_params = $request->only(['question','answer','user_type']);
        $created_params['active'] = true;

        // Create a new service location
        $faq = Faq::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Faq created successfully.',
            'faq' => $faq,
        ], 201);
    }
    public function edit($id)
    {

        $faq = Faq::find($id);
// dd($faq->icon);
        return Inertia::render(
            'pages/faq/create',
            ['faq' => $faq,]
        );
    }
    public function update(Request $request, Faq $faq)
    {
// dd($request);
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }

         // Validate the incoming request
         $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'user_type' => 'required',
        ]);

        $updated_params = $request->only(['question','answer','user_type']);

        $updated_params['active'] = true;


        $faq->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Faq created  updated successfully.',
            'faq' => $faq,
        ], 201);

    }
    public function destroy(Faq $faq)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $faq->delete();

        return response()->json([
            'successMessage' => 'Faq deleted successfully',
        ]);
    }   
    public function updateStatus(Request $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // dd($request->all());
        Faq::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Faq status updated successfully',
        ]);


    }

}
