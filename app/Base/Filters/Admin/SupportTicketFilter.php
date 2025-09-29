<?php

namespace App\Base\Filters\Admin;

use App\Base\Libraries\QueryFilter\FilterContract;

class SupportTicketFilter implements FilterContract
{
    public function filters()
    {
        return [
            'support_type',
            'status',
            'service_location'
        ];
    }

    public function defaultSort()
    {
        return '-created_at';
    }

    public function support_type($builder, $value = null) 
    {
        $builder->where('support_type', $value);
    }
    public function status($builder, $value = null) 
    {
        $builder->where('status', $value);
    }
    public function service_location($builder, $value = null) 
    {
        $builder->where('service_location', $value);
    }
    public function ticket_id($builder, $value = null)
    {
        if ($value) {
            $builder->where('ticket_id', 'LIKE', '%' . $value . '%');
        }
    }
}
