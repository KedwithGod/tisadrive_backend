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
use App\Models\Support\SupportTicketTitle;

class SupportTicketTitleController extends BaseController
{

    protected $imageUploader;
    protected $category;

    public function __construct(ImageUploaderContract $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }

    public function index() 
    {
        $app_for = env("APP_FOR");
        return Inertia::render('pages/support/title/index',['app_for'=>$app_for]);
    }
    // List of User
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = SupportTicketTitle::query()->orderBy('created_at','DESC');;
        

        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
     {
        $app_for = env("APP_FOR");
        $category = SupportTicketCategory::get();
        return Inertia::render('pages/support/title/create', ['app_for'=>$app_for,'category'=> $category,]);
    }
    public function store(Request $request)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // dd($request->all());
         // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|array|max:100',
            // 'category_type' => 'required',
            'title_type' => 'required',
            'user_type' => 'required'
        ]);

            // $created_params['category_type'] = implode(',', $validated['category_type']);

        $created_params['title'] = $validated['title']['en'];
        $created_params['title_type'] = $validated['title_type'];
        $created_params['user_type'] = $validated['user_type'];
        $created_params['active'] = true;

        // Create a new service location
        $ticket_title = SupportTicketTitle::create($created_params);

        foreach ($validated['title'] as $code => $language) {
            $translationData[] = ['title' => $language, 'locale' => $code, 'ticket_title_id' => $ticket_title->id];
            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->title = $language;
        }
        $ticket_title->supportTicketTitleTranslationWords()->insert($translationData);
        $ticket_title->translation_dataset = json_encode($translations_data);
        $ticket_title->save();
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Title created successfully.',
            'ticket_title' => $ticket_title,
        ], 201);
    }

    public function edit($id)
    {

        $title = SupportTicketTitle::find($id);
        foreach ($title->supportTicketTitleTranslationWords as $language) {
            $ticketTitle[$language->locale]  = $language->title;
        }
        $title->title = $ticketTitle  ?? null;
        $app_for = env("APP_FOR");
        $category = SupportTicketCategory::get();

        return Inertia::render(
            'pages/support/title/create', ['title'=>$title,'app_for'=>$app_for,'category'=> $category,]
        );
    }

    public function update(Request $request, SupportTicketTitle $title)
    {


        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        // dd($request->all());
         // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|array|max:100',
            // 'category_type' => 'required',
            'title_type' => 'required',
            'user_type' => 'required',
        ]);

        $updated_params['title'] = $validated['title']['en'];
        // $updated_params['category_type'] = implode(',', $validated['category_type']); // Convert array to string
        $updated_params['title_type'] = $validated['title_type'];
        $updated_params['user_type'] = $validated['user_type'];

        // Create a new service location
        $title->supportTicketTitleTranslationWords()->delete();

        $title->update($updated_params);

        foreach ($validated['title'] as $code => $language) {
            $translationData[] = ['title' => $language, 'locale' => $code, 'ticket_title_id' => $title->id];
            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->title = $language;
        }
        $title->supportTicketTitleTranslationWords()->insert($translationData);
        $title->translation_dataset = json_encode($translations_data);
        $title->save();
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Title created successfully.',
            'title' => $title,
        ], 201);

        }

    public function destroy(SupportTicketTitle $title)
    {
        $title->delete();
        return response()->json([
            'successMessage' => 'Title deleted successfully',
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
        SupportTicketTitle::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Title status updated successfully',
        ]);


    }

   

}
