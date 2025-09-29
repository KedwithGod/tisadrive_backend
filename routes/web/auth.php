<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\Registration\UserRegistrationController;
use App\Http\Controllers\Web\User\WebUserLoginController;

Route::controller(LoginController::class)->group(function () {
    Route::post('user/login','loginSpaUser')->name('spa-user-login');
    Route::post('owner-login','loginFleetowners')->name('spa-owner-login');
    Route::post('dispatch-login','loginDispatchUsers')->name('spa-dispatcher-login');

});


// Login  Frontend
Route::middleware('guest')->controller(WebUserLoginController::class)->group(function () {


    Route::get('owner-login','Ownerindex')->name('owner-login');




});

Route::controller(UserRegistrationController::class)->group(function () {

    Route::post('user/register','register')->name('user-register');

});