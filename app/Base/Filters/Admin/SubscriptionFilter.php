<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;
use Carbon\Carbon;

/**
 * Test filter to demonstrate the custom filter usage.
 * Delete later.
 */
class SubscriptionFilter implements FilterContract {
	/**
	 * The available filters.
	 *
	 * @return array
	 */
	public function filters() {
        return [
            'status','search'
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
	public function status($builder, $value = 0) 
    {
		$builder->where('active', $value);
    }
    public function search($builder, $value=null) {
        $builder->where('name','LIKE','%'.$value.'%');
    }

}
