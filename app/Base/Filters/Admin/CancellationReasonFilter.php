<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;

class CancellationReasonFilter implements FilterContract
{
    public function filters()
    {
        return [
            'status',
            'transport_type',
            'dispatch_type',
            'search',
            'payment_type',
            'user_type',
            'arrival_status'
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
        $builder->where('transport_type', $value);
    }
    public function user_type($builder, $value = null)
    {
        $builder->where('user_type', $value);
    }
    public function payment_type($builder, $value = null)
    {
        $builder->where('payment_type', $value);
    }

    public function arrival_status($builder, $value = null)
    {
        $builder->where('arrival_status', $value);
    }
    public function search($builder, $value=null) {
        $builder->where('reason','LIKE',"%".$value."%");
    }
}
