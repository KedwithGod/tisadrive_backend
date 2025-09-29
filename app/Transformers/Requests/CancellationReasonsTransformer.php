<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Admin\CancellationReason;

class CancellationReasonsTransformer extends Transformer
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
     * @param CancellationReason $reason
     * @return array
     */
    public function transform(CancellationReason $reason)
    {
        $params =  [
            'id' => $reason->id,
            'user_type' => $reason->user_type,
            'arrival_status' => $reason->arrival_status,
            'reason' => $reason->reason
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
        if($reason->translation_dataset){
            foreach (json_decode($reason->translation_dataset) as $key => $tranlation) {

                if($tranlation->locale=='en'){
    
                    $params['reason'] = $tranlation->name;
                   
                   
                }
                if($tranlation->locale==$current_locale){
    
                    $params['reason'] = $tranlation->name;
    
                    break; 
                }
                
                
            }
        }

        return $params;

    }
}
