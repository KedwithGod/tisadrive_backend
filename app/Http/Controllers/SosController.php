<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\SosFilter;
use Illuminate\Http\Request;
use App\Models\Admin\Sos;

class SosController extends Controller
{
    public function index() {
        return Inertia::render('pages/sos/index');
    }
    public function list(QueryFilterContract $queryFilter)
    {
        $query = Sos::query();

        $results = $queryFilter->builder($query)->customFilter(new SosFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create() {

        return Inertia::render('pages/sos/create',['sos' => null]);
    }
    public function store(Request $request)
    {
         // Validate the incoming request
         $request->validate([
            'name' => 'required',
            'number' => 'required',
        ]);

        $created_params = $request->only(['name','number']);
        $created_params['active'] = true;
        $created_params['created_by']=auth()->user()->id;

        // Create a new service location
        $sos = Sos::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Sos created successfully.',
            'sos' => $sos,
        ], 201);
    }
    public function edit($id)
    {

        $sos = Sos::find($id);
// dd($sos);
        return Inertia::render(
            'pages/sos/create',
            ['sos' => $sos,]
        );
    }
    public function update(Request $request, Sos $sos)
    {
// dd($request);

         // Validate the incoming request
         $request->validate([
            'name' => 'required',
            'number' => 'required',
        ]);

        $updated_params = $request->only(['name','number']);

        $updated_params['active'] = true;

        $sos->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Sos created  updated successfully.',
            'sos' => $sos,
        ], 201);

    }
    public function destroy(Sos $sos)
    {
        $sos->delete();

        return response()->json([
            'successMessage' => 'Sos deleted successfully',
        ]);
    }   
    public function updateStatus(Request $request)
    {
        // dd($request->all());
        Sos::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Sos status updated successfully',
        ]);


    }
}
