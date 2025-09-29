<?php

namespace App\Http\Controllers\support;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Transformers\CountryTransformer;
use App\Models\Support\SupportTicketCategory;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\V1\BaseController;
use Kreait\Firebase\Database;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;
use Illuminate\Support\Facades\Storage;
use App\Base\Filters\Master\CommonMasterFilter;
use Carbon\Carbon;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\Hash;


class SupportTicketCategoryController extends BaseController
{

    protected $imageUploader;
    protected $category;

    public function __construct(ImageUploaderContract $imageUploader, SupportTicketCategory $category)
    {
        $this->imageUploader = $imageUploader;
        $this->category = $category;
    }

    public function index() 
    {
        $app_for = env("APP_FOR");
        return Inertia::render('pages/support/category/index',['app_for'=>$app_for]);
    }
    // List of User
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = SupportTicketCategory::query();
        

        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
     {
        $app_for = env("APP_FOR");
        return Inertia::render('pages/support/category/create', ['app_for'=>$app_for,]);
    }
    public function store(Request $request)
    {

         // Validate the incoming request
         $request->validate([
            'category' => 'required',
        ]);

        $created_params = $request->only(['category']);

        // Create a new service location
        $category = SupportTicketCategory::create($created_params);

        //  $created_params =   $request->validate([
        //     'category' => 'required',
        // ]);

        // // Create a new category
        // $category = SupportTicketCategory::create($created_params);
     
// dd($category);
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'category created successfully.',
            'category' => $category,
        ], 201);
    }

    public function edit($id)
    {

        $category = SupportTicketCategory::find($id);
// dd($category);
        $app_for = env("APP_FOR");

        return Inertia::render(
            'pages/support/category/create', ['category'=>$category,'app_for'=>$app_for,]
        );
    }

    public function update(Request $request, SupportTicketCategory $category)
    {


        // Validate the incoming request
            $updated_params =  $request->validate([
                'category' => 'required',
            ]);

            $category->update($updated_params);

            // Optionally, return a response
            return response()->json([
                'successMessage' => 'Category updated successfully.',
                'category' => $category,
            ], 201);

        }

    public function destroy(SupportTicketCategory $category)
    {
        $category->delete();
        return response()->json([
            'successMessage' => 'category deleted successfully',
        ]);
    }   

   

}
