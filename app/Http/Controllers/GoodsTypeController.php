<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Admin\GoodsType;
use Illuminate\Http\Request;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\GoodsTypeFilter;

class GoodsTypeController extends Controller
{
    public function index() {
        return Inertia::render('pages/goods_type/index');
    }
    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = GoodsType::with('goodsTypeTranslationWords')->orderBy('created_at','DESC');
// dd("ssss");
        $results = $queryFilter->builder($query)->customFilter(new GoodsTypeFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create() {
        return Inertia::render('pages/goods_type/create');
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
            'languageFields' => 'required|array',
            // 'goods_type_name' => 'required',
            'goods_types_for' => 'required',
        ]);

        $created_params['goods_types_for'] = $validated['goods_types_for'];
        $created_params['goods_type_name'] = $validated['languageFields']['en'];
        $created_params['active'] = true;

        // Create a new service location
        $goodsType = GoodsType::create($created_params);

        foreach ($validated['languageFields'] as $code => $language) {
            $translationData[] = ['name' => $language, 'locale' => $code, 'goods_type_id' => $goodsType->id];
            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->name = $language;
        }
        $goodsType->goodsTypeTranslationWords()->insert($translationData);
        $goodsType->translation_dataset = json_encode($translations_data);
        $goodsType->save();
        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Goods Type created successfully.',
            'goodsType' => $goodsType,
        ], 201);
    }
    public function edit($id)
    {

        $goodsType = GoodsType::find($id);
        foreach ($goodsType->goodsTypeTranslationWords as $language) {
            $languageFields[$language->locale]  = $language->name;
        }
        $goodsType->languageFields = $languageFields  ?? null;
        return Inertia::render(
            'pages/goods_type/create',
            ['goodsType' => $goodsType,]
        );
    }
    public function update(Request $request, GoodsType $goodsType)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
         // Validate the incoming request
        $validated = $request->validate([
            'languageFields' => 'required|array',
            'goods_types_for' => 'required',

        ]);

        $updated_params['goods_types_for'] = $validated['goods_types_for'];
        $updated_params['goods_type_name'] = $validated['languageFields']['en'];

        $goodsType->goodsTypeTranslationWords()->delete();

        $goodsType->update($updated_params);
        foreach ($validated['languageFields'] as $code => $language) {
            $translationData[] = ['name' => $language, 'locale' => $code, 'goods_type_id' => $goodsType->id];
            $translations_data[$code] = new \stdClass();
            $translations_data[$code]->locale = $code;
            $translations_data[$code]->name = $language;
        }
        $goodsType->goodsTypeTranslationWords()->insert($translationData);
        $goodsType->translation_dataset = json_encode($translations_data);
        $goodsType->save();

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Goods Type updated successfully.',
            'goodsType' => $goodsType,
        ], 201);

    }
    public function destroy(GoodsType $goodsType)
    {
        if(env('APP_FOR') == 'demo'){
            return response()->json([
                'alertMessage' => 'You are not Authorized'
            ], 403);
        }
        $goodsType->delete();

        return response()->json([
            'successMessage' => 'Goods Type deleted successfully',
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
        GoodsType::where('id', $request->id)->update(['active'=> $request->status]);

        return response()->json([
            'successMessage' => 'Goods Type status updated successfully',
        ]);


    }

}
