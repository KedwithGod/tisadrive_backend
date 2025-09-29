<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Request\RecentSearchStop;

class SearchStopsTransformer extends Transformer
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
     * @param RecentSearchStops $stop
     * @return array
     */
    public function transform(RecentSearchStop $stop)
    {
        return [
            'id' => $stop->id,
            'recent_searche_id' => $stop->recent_searche_id,
            'address' => $stop->address,
            'short_address' => $stop->short_address,
            'latitude' => $stop->latitude,
            'longitude' => $stop->longitude,
            'poc_name' => $stop->poc_name,
            'poc_mobile' => $stop->poc_mobile,
            'order' => $stop->order,
            'poc_instruction' => $stop->poc_instruction,
           
        ];
    }



   

}
