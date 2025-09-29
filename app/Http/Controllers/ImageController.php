<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;

class ImageController extends Controller
{
    //
    public function index()
    {
        return Inertia::render('pages/images/index');
    }

    public function create()
    {


        return Inertia::render('pages/images/create');
    }

    public function update()
    {

        return Inertia::render('pages/images/update');
    }
}
