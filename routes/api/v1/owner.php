<?php

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| These routes are prefixed with 'api/v1'.
| These routes use the root namespace 'App\Http\Controllers\Api\V1'.
|
 */
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\Route;

/**
 * These routes are prefixed with 'api/v1'.
 * These routes use the root namespace 'App\Http\Controllers\Api\V1\Driver'.
 * These routes use the middleware group 'auth'.
 */


Route::prefix('owner')->namespace('Owner')->middleware(['auth:sanctum','throttle:120,1'])->group(function () {
        Route::get('list-fleets','FleetController@index');
        Route::get('fleet/documents/needed','FleetController@neededDocuments');
        Route::get('list-drivers','FleetController@listDrivers');
        Route::post('assign-driver/{fleet}','FleetController@assignDriver');
        Route::post('add-fleet','FleetController@storeFleet');
        Route::post('add-drivers','FleetDriversController@addDriver');
        Route::get('delete-driver/{driver}','FleetDriversController@deleteDriver');

        //Owner Dashboard
        Route::post('dashboard','OwnerController@ownerDashboard');
        Route::post('fleet-dashboard','OwnerController@fleetDashboard');

        Route::post('fleet-driver-dashboard','OwnerController@fleetDriverDashboard');


});
