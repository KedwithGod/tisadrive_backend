<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;
use Carbon\Carbon;

/**
 * Test filter to demonstrate the custom filter usage.
 * Delete later.
 */
class UserFilter implements FilterContract {
	/**
	 * The available filters.
	 *
	 * @return array
	 */
	public function filters() {
		return [
			'status','date_option','search','country','unit','timezone'
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
    
	public function country($builder, $value = null) {
		$builder->where('country', $value);
    }
    
	public function unit($builder, $value = 0) {
		$builder->where('unit', $value);
    }
    
	public function timezone($builder, $value = 0) {
		$builder->where('timezone', $value);
    }

    
   public function date_option($builder, $value = 0)
    {
        if ($value == 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($value == 'week') {
            $from = now()->startOfWeek();
            $to = now()->endOfWeek();
        } elseif ($value == 'month') {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();
        } elseif ($value == 'year') {
            $from = now()->startOfYear();
            $to = now()->endOfYear();
        } else {
            $date = explode('<>', $value);
            $from = Carbon::parse($date[0])->startOfDay();
            $to = Carbon::parse($date[1])->endOfDay();
        }
        $builder->whereBetween('created_at', [$from, $to]);
    }

}
