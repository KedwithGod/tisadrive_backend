<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Common\CountryController;
use App\Http\Controllers\Web\Admin\LanguagesController;
use App\Http\Controllers\Web\Admin\PeakZoneController;
use App\Http\Controllers\PermissionController;
/*
|--------------------------------------------------------------------------
| SPA Auth Routes
|--------------------------------------------------------------------------
|
| These routes are prefixed with '/'.
| These routes use the root namespace 'App\Http\Controllers\Web'.
|
 */


Route::middleware('auth:sanctum', config('jetstream.auth_session'),)->group(function () {

    Route::controller(CountryController::class)->group(function () {
        Route::group(['prefix' => 'country'], function () {

            Route::get('/', 'index')->name('countries.index');
            Route::get('/list', 'list')->name('countries.list');
            Route::get('/create', 'create')->name('countries.create');
            Route::post('/store', 'store')->name('countries.store');
            Route::post('/toggle_status/{country}', 'toggleStatus')->name('countries.toggle');
            Route::get('/{country}', 'edit')->name('countries.edit');
            Route::post('/update/{country}', 'update')->name('countries.update');
        });
    });

    Route::controller(LanguagesController::class)->group(function () {
    
        Route::group(['prefix' => 'languages'], function () {
            Route::get('/', 'index')->name('languages.index');
            Route::get('/create', 'create')->name('languages.create');
            Route::get('/list', 'list')->name('languages.list');
            Route::get('/browse/{id}', 'browse')->name('languages.browse');
            Route::get('load-translation/{id}','loadTranslation');
            Route::post('/store', 'store')->name('languages.store');
            Route::post('auto-translate/{id}','autoTranslate');
            Route::post('translate/update/{id}','updateTranslate');
            Route::post('auto-translate-all/{id}','autoTranslateAll');
            Route::put('/update/{language}', 'update')->name('language.update');
            Route::put('/status/{language}', 'status');
            Route::delete('/delete/{language}', 'delete');
            Route::get('download-translation/{id}','downloadTranslation');
            Route::post('default-set/{lang}','updateAppLocale');
        });

        Route::get('current-languages','CurrenetLanguagelist')->name('current-languages');
        Route::get('current-locations','serviceLocationlist')->name('current-locations');
        Route::get('current-notifications','adminNotification')->name('current-notifications');
        // mark-notification-as-read
        Route::post('mark-notification-as-read','readNotification')->name('read-notifications');
        
    });


    Route::get('user/permissions',[PermissionController::class, 'userPermissions']);

    Route::namespace('Admin')->group(function () {
        Route::prefix('subscription')->group(function() {
            Route::get('/', 'SubscriptionController@index');
            Route::middleware('remove_empty_query')->get('/list', 'SubscriptionController@fetch');
            Route::get('/create', 'SubscriptionController@create');
            Route::get('/edit/{plan}', 'SubscriptionController@getById');
            Route::post('/store', 'SubscriptionController@store');
            Route::post('/update/{plan}', 'SubscriptionController@update');
            Route::post('/update-status/{plan}', 'SubscriptionController@toggleStatus');
            Route::delete('/delete/{plan}', 'SubscriptionController@delete');
        });

        Route::group(['prefix' => 'peak_zone'], function () {
            // prefix('zone')->group(function () {
            Route::get('/', 'PeakZoneController@index');
            Route::get('/fetch', 'PeakZoneController@getAllZone');
            Route::get('/map/{zone}', 'PeakZoneController@zoneMapView');

            Route::post('update_status/{peak_zones}', 'PeakZoneController@updateStatus');

        });

        Route::controller(AirportController::class)->prefix('airport')->group(function () {
            Route::get('/', 'index');
            Route::get('/fetch', 'getAllAirports');
            Route::get('/list', 'list');
            Route::get('/map/{id}', 'airportMapView');
            Route::get('/create', 'create');
            Route::get('/edit/{id}', 'getById');
            Route::post('update/{airport}', 'update');
            Route::post('store', 'store');
            Route::get('/{id}', 'getById');
            Route::delete('/delete/{airport}', 'delete');
            Route::post('/update-status/{airport}', 'toggleAirportStatus');
        });

    });
});