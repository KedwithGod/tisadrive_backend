<?php

namespace App\Transformers;

use App\Models\Admin\ServiceLocation;

class ServiceLocationTransformer extends Transformer {
	/**
	 * A Fractal transformer.
	 *
	 * @param ServiceLocation $serviceLocation
	 * @return array
	 */
	public function transform(ServiceLocation $serviceLocation) {
		$params = [
			'id' => $serviceLocation->id,
			'name' => $serviceLocation->name,
			'currency_name' => $serviceLocation->currency_name,
			'currency_symbol' => $serviceLocation->currency_symbol,
			'currency_code' => $serviceLocation->currency_code,
			'timezone' => $serviceLocation->timezone,
			'active' => $serviceLocation->active,
			'translation_dataset'=> $serviceLocation->translation_dataset,	
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

		if($serviceLocation->translation_dataset){
			foreach (json_decode($serviceLocation->translation_dataset) as $key => $tranlation) {

				if($tranlation->locale=='en'){
	
					$params['name'] = $tranlation->name;
				   
				   
				}
				if($tranlation->locale==$current_locale){
	
					$params['name'] = $tranlation->name;
	
					break; 
				}
				
				
			}

		}

        return $params;
	}
}
