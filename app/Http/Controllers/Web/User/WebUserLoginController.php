<?php

namespace App\Http\Controllers\Web\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use App\Http\Controllers\Controller;

class WebUserLoginController extends Controller
{
   
    //Ownerindex

    public function Ownerindex() 
    {
        return Inertia::render('Auth/OwnerLogin');
    }
    
}
