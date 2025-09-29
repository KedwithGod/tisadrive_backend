<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Transformers\CountryTransformer;

class LandingSiteController extends Controller
{
    public function index() {
        return Inertia::render('pages/landing/index');
    }

    public function driver() {

        return Inertia::render('pages/landing/driver');
    }

    public function user() {

        return Inertia::render('pages/landing/user');
    }

    public function contact() {

        return Inertia::render('pages/landing/contact');
    }

    public function privacy() {

        return Inertia::render('pages/landing/privacy');
    }

    public function terms() {

        return Inertia::render('pages/landing/terms');
    }

    public function compliance() {

        return Inertia::render('pages/landing/compliance');
    }

    public function dmv() {

        return Inertia::render('pages/landing/dmv');
    }


    // public function booking() {

    //     return Inertia::render('pages/landing/user-web/booking');
    // }

    // public function profile() {

    //     return Inertia::render('pages/landing/user-web/profile');
    // }

    // public function history() {

    //     return Inertia::render('pages/landing/user-web/history');
    // }
}
