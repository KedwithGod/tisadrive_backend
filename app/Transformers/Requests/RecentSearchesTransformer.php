<?php

namespace App\Transformers\Requests;

use App\Transformers\Transformer;
use App\Models\Request\RecentSearch;
use App\Transformers\Requests\SearchStopsTransformer;

class RecentSearchesTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [

    ];

    /**
     * Resources that can be included default.
     *
     * @var array
     */
    protected array $defaultIncludes = [
        'searchStops'

    ];

    /**
     * A Fractal transformer.
     *
     * @param RecentSearch $search
     * @return array
     */
    public function transform(RecentSearch $search)
    {
        return [
            'id' => $search->id,
            'user_id' => $search->user_id,
            'pick_lat' => $search->pick_lat,
            'pick_lng' => $search->pick_lng,
            'drop_lat' => $search->drop_lat,
            'drop_lng' => $search->drop_lng,
            'pick_address' => $search->pick_address,
            'pick_short_address' => $search->pick_short_address,
            'drop_address' => $search->drop_address,
            'drop_short_address' => $search->drop_short_address,
            'pickup_poc_name' => $search->pickup_poc_name,
            'pickup_poc_mobile' => $search->pickup_poc_mobile,
            'pickup_poc_instruction' => $search->pickup_poc_instruction,
            'drop_poc_name' => $search->drop_poc_name,
            'drop_poc_mobile' => $search->drop_poc_mobile,
            'drop_poc_instruction' => $search->drop_poc_instruction,
            'total_distance' => $search->total_distance,
            'total_time' => $search->total_time,
            'poly_line' => $search->poly_line,
            'transport_type' => $search->transport_type,
           
        ];
    }



    /**
     * Include the Stops of the search.
     *
     * @param RecentSearch $search
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeSearchStops(RecentSearch $search)
    {
        $stops = $search->searchStops;

        return $stops
        ? $this->collection($stops, new SearchStopsTransformer)
        : $this->null();
    }

}
