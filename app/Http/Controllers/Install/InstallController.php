<?php

namespace App\Http\Controllers\Install;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use App\Http\Controllers\Controller;

class InstallController extends Controller
{

    public function index() {

        return Inertia::render('pages/installation/index');
    }



     public function verification_submit(Request $request)
    {            
        
            $format = check_code_format($request->purchase_code);

            if($format['success'])
            {
                $UpdateSettingcontract = app("update-service");
                $softwarecheck = $UpdateSettingcontract->softupdate(); 
                return json_encode($softwarecheck);
            }
            else{
                return json_encode($format);
            } 
       

    }


}
