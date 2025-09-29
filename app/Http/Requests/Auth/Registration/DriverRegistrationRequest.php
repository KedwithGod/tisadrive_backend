<?php

namespace App\Http\Requests\Auth\Registration;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\DB;

class DriverRegistrationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'last_name' => 'max:50',
            'email' => 'sometimes|required|email|max:150',
            'password' => 'sometimes|required|min:8',
            // 'uuid' => 'required|uuid|exists:mobile_otp_verifications,id,verified,1',
            'mobile' => 'sometimes|required',
            'country' =>[
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('countries')
                        ->where('code', $value)
                        ->orWhere('dial_code', $value)
                        ->exists();
        
                    if (! $exists) {
                        $fail('The selected country is invalid.');
                    }
                },
            ],
            'device_token'=>'required',
            'login_by'=>'required|in:android,ios',
            'vehicle_type'=>'sometimes|required|exists:vehicle_types,id',
            'address'=>'min:15',
            'postal_code'=>'min:6|max:6',
            // 'car_make'=>'sometimes|required|exists:car_makes,id',
            // 'car_model'=>'sometimes|required|exists:car_models,id',
            'car_color'=>'sometimes|required',
            'car_number'=>'sometimes|required',
            'is_company_driver'=>'sometimes|required|boolean',
            'service_location_id'=>'sometimes|required'//|exists,service_locations,id
        ];
    }
}
