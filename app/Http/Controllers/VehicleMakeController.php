<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\VehicleMake;


class VehicleMakeController extends Controller
{
    //

    public function index() {
        return Inertia::render('pages/vehicle_make/index');
    }

    public function create() {


        return Inertia::render('pages/vehicle_make/create');
    }

    public function update() {
        return Inertia::render('pages/vehicle_make/update');
    }

}
