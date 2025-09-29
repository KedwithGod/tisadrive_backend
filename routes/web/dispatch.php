<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DispatcherController;
use App\Http\Controllers\Web\Admin\DispatcherCreateRequestController;
use App\Http\Controllers\Api\V1\Request\EtaController;

Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {


Route::group(['prefix' => 'dispatcher'], function () {
    // Route::get('/', [DispatcherController::class, 'index'])->name('dispatch.dashboard');
    Route::get('/godeye', [DispatcherController::class, 'godseye'])->name('dispatch.godeye');
    Route::get('/bookride', [DispatcherController::class, 'bookride'])->name('dispatch.dashboard');
    Route::get('/advanced', [DispatcherController::class, 'advanced'])->name('dispatch.advanced');

    //ride request
    Route::get('/rides_request', [DispatcherController::class, 'rideRequest'])->name('dispatch.rideRequest');
    Route::get('/rides_request/list', [DispatcherController::class, 'list'])->name('dispatch.rideRequest.list');
    Route::post('/rides_request/driver/{driver}', [DispatcherController::class, 'driverFind'])->name('dispatch.triprequest.driverFind');
    Route::get('/rides_request/view/{requestmodel}', [DispatcherController::class, 'viewDetails'])->name('dispatch.triprequest.viewDetails');
    Route::get('/rides_request/cancel/{requestmodel}', [DispatcherController::class, 'cancelRide'])->name('dispatch.triprequest.cancel');
    Route::get('/rides_request/download-invoice/{requestmodel}', [DispatcherController::class, 'downloadInvoice']);

    Route::get('/ongoing_request', [DispatcherController::class, 'ongoingRequest'])->name('dispatch.dispatch.ongoingRequest');
    Route::get('/ongoing_request/find-ride/{request}', [DispatcherController::class, 'ongoingRideDetail'])->name('dispatch.dispatch.ongoingRideDetail');
    Route::get('/ongoing_request/assign/{request}', [DispatcherController::class, 'assignView'])->name('dispatch.dispatch.assignView');
    Route::post('/ongoing_request/assign-driver/{requestmodel}', [DispatcherController::class, 'assignDriver'])->name('dispatch.dispatch.assignDriver');

});
});

Route::group(['prefix' => 'dispatch'], function () {
    Route::get('/', [DispatcherController::class, 'bookingRequest'])->name('dispatch.index');
    Route::post('/create-request',[DispatcherCreateRequestController::class,'createRequest']);
    Route::post('request/eta',[EtaController::class,'eta']);
    Route::post('request/list_packages',[EtaController::class,'listPackages']);
    Route::post('serviceVerify', [EtaController::class,'serviceVerify']);
    Route::get('/dispatchLocations', [DispatcherCreateRequestController::class,'dispatchLocations']);

    // Route::get('/request-complaint', [ComplaintController::class, 'userRequestComplaint'])->name('dispatch.userRequestComplaint');
    
    Route::get('fetch-user-detail',[DispatcherController::class,'fetchUserIfExists']);

});