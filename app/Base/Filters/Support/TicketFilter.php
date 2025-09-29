<?php

namespace App\Base\Filters\Support;

use App\Base\Libraries\QueryFilter\FilterContract;
use Carbon\Carbon;

/**
 * Test filter to demonstrate the custom filter usage.
 * Delete later.
 */
class TicketFilter implements FilterContract {
	/**
	 * The available filters.
	 *
	 * @return array
	 */
	public function filters() {
		return [
			'title_id',
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

    public function approve($builder,$value=0)
    {
        $builder->where('approve',$value);
    }
    public function title_id($builder,$value="all")
    {
        if($value !== "all"){
            $builder->where('title_id',$value);
        }
    }
    public function transport_type($builder,$value='both')
    {
        if($value !== "all"){
            $builder->where('transport_type',$value);
        }
    }
    public function vehicle_type($builder,$value=null)
    {
        $builder->whereHas('driverVehicleTypeDetail', function ($q) use ($value) {
            $q->whereIn('vehicle_type',$value);
        });
    }
    public function owner_id($builder,$value=null)
    {
        $builder->where('owner_id',$value);
    }
    public function fleet_vehicle_type($builder,$value=null)
    {
        $builder->whereHas('fleetDetail', function ($q) use ($value) {
            $q->whereIn('vehicle_type',$value);
        });
    }
    public function search($builder, $value=null) {
        $builder->where('name','LIKE','%'.$value.'%');
    }
}
