<?php

namespace App\Transformers;

use App\Models\Payment\BankInfo;
use App\Transformers\Driver\DriverBankInfoTransformer;
use App\Transformers\Transformer;
use App\Models\Admin\DriverBankInfo;
use App\Models\Method;
use App\Transformers\FieldTransformer;
use App\Base\Constants\Auth\Role;
use App\Transformers\Owner\OwnerBankInfoTransformer;

class BankInfoTransformer extends Transformer
{
    /**
    * Resources that can be included if requested.
    *
    * @var array
    */
    protected array $availableIncludes = [
        'driver_bank_info',

    ];
    /**
     * Resources that can be included default.
     *
     * @var array
     */
    protected array $defaultIncludes = [
        'fields','driver_bank_info'

    ];
    /**
     * A Fractal transformer.
     *
     * @param Method $method
     * @return array
     */
    public function transform(Method $method)
    {
        $params =  [
            'id'=>$method->id,
            'method_name'=>$method->method_name,
            'active'=>$method->active,
        ];

        return $params;
    }

    /**
     * Include the driver document of the driver needed document.
     *
     * @param Method $method
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeFields(Method $method)
    {
        $result = $method->fields;
        // dd($result);

        return $result
        ? $this->collection($result, new FieldTransformer)
        : $this->null();
    }

    /**
     * Include the driver document of the driver needed document.
     *
     * @param Method $method
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeDriverBankInfo(Method $method)
    {
        if (auth()->user()->hasRole(Role::DRIVER)) 
        {   
         $result = $method->driverBankInfo()->where('driver_id', auth()->user()->driver->id)->get();
        // dd($result);
        return $result
            ? $this->collection($result, new DriverBankInfoTransformer)
            : $this->null();

        }else{

            $owner = auth()->user()->owner;

        //   dd($owner);
        $result = $method->ownerBankInfo()->where('owner_id', $owner->id)->get();
        
        return $result
            ? $this->collection($result, new OwnerBankInfoTransformer)
            : $this->null();
        }

    }
}
