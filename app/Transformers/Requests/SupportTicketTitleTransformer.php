<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Support\SupportTicketTitle;

class SupportTicketTitleTransformer extends Transformer
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
    public function transform(SupportTicketTitle $title)
    {
        $params =  [
            'id' => $title->id,
            'title_type' => $title->title_type,
            'user_type' => $title->user_type,
            'title' => $title->title
        ];

        $user = auth()->user();
        $current_locale='en';

        if($user!=null && $user->lang!=null ){

        $current_locale = $user->lang;

        }

        if(!empty($title->translation_dataset)){
            foreach (json_decode($title->translation_dataset) as $key => $tranlation) {

                if($tranlation->locale=='en'){
    
                    $params['title'] = $tranlation->title;
                   
                   
                }
                if($tranlation->locale==$current_locale){
    
                    $params['title'] = $tranlation->title;
                    break; 
                }
                
                
            }
        }
       

        return $params;

    }
}
