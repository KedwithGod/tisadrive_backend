<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Models\Admin\GoodsType;
use App\Http\Controllers\Api\V1\BaseController;
use App\Transformers\Common\GoodsTypeTransformer;

/**
 * @group Goods Types List
 *
 * APIs for vehilce management apis. i.e types,car makes,models apis
 */
class GoodsTypesController extends BaseController
{
    /**
     *
     * @var \App\Models\Admin\GoodsType
     */
    protected $goods_type;

    /**
     * CancellationReasonsController constructor.
     *
     * @param \App\Models\Admin\GoodsType $goods_type
     */
    public function __construct(GoodsType $goods_type)
    {
        $this->goods_type = $goods_type;
    }

    /**
    * Get All Goods Types
    * @response
    *   {
    *       "success": true,
    *       "message": "goods_types_listed",
    *       "data": [
    *           {
    *               "id": 1,
    *               "goods_type_name": "Timber/Plywood/Laminate",
    *               "translation_dataset": "{\"en\":{\"locale\":\"en\",\"name\":\"Timber\\/Plywood\\/Laminate\"}}",
    *               "goods_types_for": "both",
    *               "company_key": null,
    *               "active": 1,
    *               "created_at": "2024-10-23T12:59:08.000000Z",
    *               "updated_at": "2024-10-23T12:59:08.000000Z"
    *           },
    *       ]
    *   }
    */
    public function index()
    {
        if(request()->has('vehicle_type')){
            $query = $this->goods_type->where(function ($query){
                $query->where('goods_types_for',request()->vehicle_type)
                    ->orWhere('goods_types_for','both');
            })->whereActive(true)->get();
            $result = fractal($query, new GoodsTypeTransformer);
            return $this->respondSuccess($result, 'goods_types_listed');

        }else{
            $query = $this->goods_type->whereActive(true)->get();
            $result = fractal($query, new GoodsTypeTransformer);
            return $this->respondSuccess($result, 'goods_types_listed');

        }
    }
}
