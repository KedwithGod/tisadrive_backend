<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;

class VehicleTypeFilter implements FilterContract
{
    public function filters()
    {
        return [
            'status',
            'transport_type',
            'dispatch_type',
            'search',
        ];
    }

    public function defaultSort()
    {
        return '-created_at';
    }

    public function status($builder, $value = null)
    {
        if ($value === '1') {
            $builder->where('active', true);
        } else {
            $builder->where('active', false);
        }
    }
    
    public function transport_type($builder, $value = null)
    {
        $builder->where('is_taxi', $value);
    }

    public function dispatch_type($builder, $value = null)
    {
        $builder->where('trip_dispatch_type', $value);
    }
    public function search($builder, $value=null) {
        $builder->where('name','LIKE',"%".$value."%");
    }
}
