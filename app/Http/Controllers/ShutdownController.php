<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShutdownController extends Controller
{
    // Method to shut down the application (put it in maintenance mode)
    public function shutdownApplication()
    {
        // Run the artisan down command
        \Artisan::call('down');

        return response()->json(['message' => 'Application is now in maintenance mode.']);
    }

    // Method to bring the application back up
    public function bringDown()
    {
        // Run the artisan up command
        \Artisan::call('up');

        return response()->json(['message' => 'Application is now live.']);
    }
}