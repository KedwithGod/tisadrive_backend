<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;

class GoodsTypeFilter implements FilterContract
{
    public function filters()
    {
        return [
            'status',
            'goods_types_for',
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
    
    public function goods_types_for($builder, $value = null)
    {
        $builder->where('goods_types_for', $value);
    }

    public function search($builder, $value=null) {
        $builder->where('goods_type_name','LIKE',"%".$value."%");
    }
}
