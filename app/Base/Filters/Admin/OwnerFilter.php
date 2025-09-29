<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;
use Carbon\Carbon;

/**
 * Test filter to demonstrate the custom filter usage.
 * Delete later.
 */
class OwnerFilter implements FilterContract {
	/**
	 * The available filters.
	 *
	 * @return array
	 */
	public function filters() {
		return [
			'status','search','service_location_id','approveStatus','owner_id',
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

	public function service_location_id($builder, $value = null) {
        if($value !== "all"){
            $builder->where('service_location_id', $value);
        }else{
            $builder;
        }
    }
	public function status($builder, $value = 0) 
    {
		$builder->where('active', (integer)$value);
    }

	public function approveStatus($builder, $value = 0) 
    {
        if($value){
            $builder->where('approve',true);
        }else{
            $builder->where('approve',false);
        }
    }
    public function search($builder, $value=null) {
        $builder->where('name','LIKE',"%".$value."%")
                ->orwhere('mobile','LIKE','%'.$value.'%');
    }
    public function owner_id($builder,$value=null)
    {
        $builder->where('owner_id',$value);
    }
}
