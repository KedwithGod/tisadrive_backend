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


Route::namespace('Request')->prefix('dispatcher')->group(function () {
    Route::post('request/eta', 'EtaController@eta');
    Route::post('request/list_packages', 'EtaController@listPackages');
});


