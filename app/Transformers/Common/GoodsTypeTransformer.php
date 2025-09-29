<?php

namespace App\Transformers\Common;

use App\Transformers\Transformer;
use App\Models\Admin\GoodsType;

class GoodsTypeTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [

    ];

    /**
     * A Fractal transformer.
     *
     * @param GoodsType $goods_type
     * @return array
     */
    public function transform(GoodsType $goods_type)
    {
        $params =  [
            'id' => $goods_type->id,
            'goods_type_name' => $goods_type->goods_type_name,
            'goods_types_for' => $goods_type->goods_types_for,
            'translation_dataset'=> $goods_type->translation_dataset,
            'company_key' => $goods_type->company_key,
            'active' => $goods_type->active,
            'created_at'  => $goods_type->created_at,
            'updated_at'  => $goods_type->updated_at,
        ];


        $user = auth()->user();

        if($user!=null){

        $current_locale = $user->lang;

        }else{

            $current_locale='en';
            
        }

        if(!$current_locale){
            
            $current_locale='en';

        }

        if($goods_type->translation_dataset){
            foreach (json_decode($goods_type->translation_dataset) as $key => $tranlation) {

                if($tranlation->locale=='en'){
    
                    $params['goods_type_name'] = $tranlation->name;
                   
                   
                }
                if($tranlation->locale==$current_locale){
    
                    $params['goods_type_name'] = $tranlation->name;
    
                    break; 
                }
                
                
            }

        }
        

        return $params;

    }
}
