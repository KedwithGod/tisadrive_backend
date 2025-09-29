<?php

namespace App\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class CreateCountryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'dial_code' => 'required',
            'dial_min_length' => 'required',
            'dial_max_length' => 'required',
            'code' => 'required',
            'flag' => 'sometimes|file',
        ];
    }
}
