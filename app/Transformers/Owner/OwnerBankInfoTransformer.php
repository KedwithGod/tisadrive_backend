<?php

namespace App\Transformers\Owner;

use App\Transformers\Transformer;
use App\Models\Admin\OwnerBankInfo;

class OwnerBankInfoTransformer extends Transformer
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
    public function transform(OwnerBankInfo $ownerBankInfo)
    {
        // dd($ownerBankInfo->bankInfo);

        $params = [
            'id' => $ownerBankInfo->id,
            'method_id' => $ownerBankInfo->method_id,
            'field_id' => $ownerBankInfo->field_id,
            'owner_id' => $ownerBankInfo->owner_id,
            'value' => $ownerBankInfo->value,
        ];

        return $params;
    }
}
