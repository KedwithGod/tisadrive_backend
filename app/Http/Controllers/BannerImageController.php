<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Master\BannerImage;
use Illuminate\Http\Request;
use App\Base\Services\ImageUploader\ImageUploader;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Libraries\QueryFilter\QueryFilterContract;

use Illuminate\Support\Facades\Validator;

class BannerImageController extends Controller
{

    protected $imageUploader;
    protected $bannerimage;

    public function __construct(ImageUploaderContract $imageUploader,BannerImage $bannerimage)
    {
        $this->imageUploader = $imageUploader;
        
        $this->bannerimage = $bannerimage;
    }
    public function index() {
        return Inertia::render('pages/banner_image/index');
    }

    public function list(QueryFilterContract $queryFilter ,Request $request)
    {
        $query = BannerImage::query()->paginate();
        // dd($query);
        // $results = $queryFilter->builder($query)->paginate();
        // dd($results);

        return response()->json([
            'results' => $query->items(),
            'paginator' => $query,
        ]);
    }
    public function create() {
        return Inertia::render('pages/banner_image/create');
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
            'image'  => 'required'
        ]);

        $created_params = $request->all();
        $created_params['active'] = true;

        if ($uploadedFile = $request->file('image')) {
            $created_params['image'] = $this->imageUploader->file($uploadedFile)
                ->saveBannerImage();
        }
        BannerImage::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Banner Image created successfully.'
        ], 201);
    } 


    public function edit($id)
    {

        $bannerimage = BannerImage::find($id);
        // dd( $bannerimage);
        return Inertia::render(
            'pages/banner_image/create',
            ['bannerimage' => $bannerimage,]
        );
    }

    public function update(Request $request, BannerImage $bannerimage)
    {

        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
         // Validate the incoming request
         $request->validate([
            'image'  => 'required',
        ]);

        $updated_params = $request->only(['image']);
        $updated_params['active'] = true;

        if ($uploadedFile = $request->file('image')) {
            $updated_params['image'] = $this->imageUploader->file($uploadedFile)
                ->saveBannerImage();
        }



        $bannerimage->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Banner Image updated successfully.',
            'bannerimage' => $bannerimage,
        ], 201);

    }
    public function destroy(BannerImage $bannerimage)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $bannerimage->delete();

        return response()->json([
            'successMessage' => 'Banner Image deleted successfully',
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
        BannerImage::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Banner Image status updated successfully',
        ]);


    }
}
