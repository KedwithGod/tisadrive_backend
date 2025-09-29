<?php

namespace App\Transformers;

use App\Models\Payment\BankInfo;
use App\Transformers\Driver\DriverBankInfoTransformer;
use App\Transformers\Transformer;
use App\Models\Admin\DriverBankInfo;
use App\Models\Field;

class FieldTransformer extends Transformer
{
    /**
    * Resources that can be included if requested.
    *
    * @var array
    */
    protected array $availableIncludes = [

    ];
    /**
     * Resources that can be included default.
     *
     * @var array
     */
    protected array $defaultIncludes = [

    ];
    /**
     * A Fractal transformer.
     *
     * @param Field $method
     * @return array
     */
    public function transform(Field $field)
    {
        $params =  [
            'id'=>$field->id,
            'method_id'=>$field->method_id,
            'input_field_name'=>$field->input_field_name,
            'placeholder'=>$field->placeholder,
            'is_required'=>$field->is_required,
            'input_field_type'=>$field->input_field_type,
        ];

        return $params;
    }

}
