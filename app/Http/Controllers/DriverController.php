<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index() {
        return Inertia::render('pages/driver/index');
    }

    public function create() {

        return Inertia::render('pages/driver/create');
    }

    public function update() {
        return Inertia::render('pages/driver/update');
    }

    public function deletedUser() {
        return Inertia::render('pages/user/deleted_users');
    }
}
