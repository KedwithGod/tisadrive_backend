<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;

class ComplaintTitleFilter implements FilterContract
{
    public function filters()
    {
        return [
            'status',
            'user_type',
            'complaint_type',
            'search',
        ];
    }

    public function defaultSort()
    {
        return '-created_at';
    }

    public function status($builder, $value = null)
    {
        if ($value !== 'all') {
           $builder->where('active', $value === '1');
        }
    }
    public function user_type($builder, $value = null)
    {
        if ($value !== 'all') {
           $builder->where('user_type', $value);
        }
    }
    public function complaint_type($builder, $value = null)
    {
        if ($value !== 'all') {
           $builder->where('complaint_type', $value);
        }
    }
    
    public function search($builder, $value=null) {
        $builder->where('name','LIKE',"%".$value."%");
    }
}
