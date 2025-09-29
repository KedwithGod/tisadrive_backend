<?php

namespace App\Transformers\Driver;

use App\Transformers\Transformer;
use App\Models\Admin\DriverBankInfo;

class DriverBankInfoTransformer extends Transformer
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [

    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(DriverBankInfo $driverBankInfo)
    {
        // dd($driverBankInfo->bankInfo);

        $params = [
            'id' => $driverBankInfo->id,
            'method_id' => $driverBankInfo->method_id,
            'field_id' => $driverBankInfo->field_id,
            'driver_id' => $driverBankInfo->driver_id,
            'value' => $driverBankInfo->value,
        ];

        return $params;
    }
}
