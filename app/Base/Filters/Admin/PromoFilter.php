<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;

class PromoFilter implements FilterContract
{
    public function filters()
    {
        return [
            'status',
            'transport_type',
        ];
    }

    public function defaultSort()
    {
        return '-created_at';
    }

    public function status($builder, $value = null)
    {
        $builder->where('active', (int) $value);
    }
    
    public function transport_type($builder, $value = null)
    {
        if($value != 'all') {
            $builder->where('transport_type', $value);
        }
    }

}
