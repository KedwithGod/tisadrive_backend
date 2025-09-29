<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Transformers\CountryTransformer;

class InstallationController extends Controller
{

    public function install() {
        
        return Inertia::render('pages/installation/index');
    }
}
