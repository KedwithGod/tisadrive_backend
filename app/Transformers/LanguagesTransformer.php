<?php

namespace App\Transformers;

use App\Models\Languages;


class LanguagesTransformer extends Transformer {
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
	 * @param Language $lang
	 * @return array
	 */
	public function transform(Languages $lang) {
		return [
			'id' => $lang->id,
			'lang' => $lang->code,
			'name' => $lang->name,
			'default_status' => $lang->default_status,
		];
	}

	

}
