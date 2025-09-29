<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;
use Carbon\Carbon;

/**
 * Test filter to demonstrate the custom filter usage.
 * Delete later.
 */
class PriceFilter implements FilterContract {
	/**
	 * The available filters.
	 *
	 * @return array
	 */
	public function filters() {
        return [
            'zone_id',
            'type_id',
            'status',
            'transport_type',
        ];
	}
    /**
    * Default column to sort.
    *
    * @return string
    */
    public function defaultSort()
    {
        return '-created_at';
    }
	public function status($builder, $value = 0) {
		$builder->where('active', $value);
    }
    public function zone_id($builder, $value = null)
    {
        $builder->whereHas('zoneTypePrice', function ($q) use ($value) {
            $q->whereIn('zone_id',$value);
        });
    }

    public function type_id($builder, $value = null)
    {
        $builder->whereHas('vehicleType', function ($q) use ($value) {
            $q->whereIn('type_id',$value);
        });
    }



}
