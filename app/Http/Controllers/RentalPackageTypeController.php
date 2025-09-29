<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Admin\PackageType;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Master\CommonMasterFilter;

class RentalPackageTypeController extends Controller
{
    public function index() {
        return Inertia::render('pages/rental_package_types/index');
    }
    public function list(Request $request, QueryFilterContract $queryFilter)
    {
        $query = PackageType::query();
    
        // Apply the filters and paginate the results
        $results = $queryFilter->builder($query)
                              ->customFilter(new CommonMasterFilter)
                              ->paginate();
    
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    
    public function create() {

        return Inertia::render('pages/rental_package_types/create',);
    }
    public function store(Request $request)
    {

        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
         // Validate the incoming request
         $created_params = $request->validate([
            'name' => 'required',
            'transport_type' => 'required',
            'description' => 'required',
            'short_description' => 'required',

        ]);

        // Create a new PackageType
        $packageType = PackageType::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Package Type created successfully.',
            'packageType' => $packageType,
        ], 201);
    }
    public function edit($id)
    {

        $packageType = PackageType::find($id);
        return Inertia::render(
            'pages/rental_package_types/create',
            ['packageType' => $packageType,]
        );
    }    
    public function update(Request $request, PackageType $packageType) 
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // dd($packageType);
        // Create a new PackageType
        $packageType->update($request->all());

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Package Type created successfully.',
            'packageType' => $packageType,
        ], 201);    
    }
    public function destroy(PackageType $packageType)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $packageType->delete();

        return response()->json([
            'successMessage' => 'Package Type deleted successfully',
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
        PackageType::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Package Type status updated successfully',
        ]);


    }


}
