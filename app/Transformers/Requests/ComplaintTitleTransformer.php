<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Admin\ComplaintTitle;

class ComplaintTitleTransformer extends Transformer
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
     * @param ComplaintType $title
     * @return array
     */
    public function transform(ComplaintTitle $title)
    {
        $params =  [
            'id' => $title->id,
            'complaint_type' => $title->complaint_type,
            'user_type' => $title->user_type,
            'title' => $title->title
        ];


        $user = auth()->user();
        $current_locale='en';

        if($user!=null && $user->lang!=null ){

        $current_locale = $user->lang;

        }

        if($title->translation_dataset){
            foreach (json_decode($title->translation_dataset) as $key => $tranlation) {

                if($tranlation->locale=='en'){
    
                    $params['title'] = $tranlation->name;
                   
                   
                }
                if($tranlation->locale==$current_locale){
    
                    $params['title'] = $tranlation->name;
    
                    break; 
                }
                
                
            }
    
            return $params;
    
        }
        }
}
