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

/**
 * These routes are prefixed with 'api/v1/masters'.
 * These routes use the root namespace 'App\Http\Controllers\Api\V1\Master'.
 * These routes use the middleware group 'auth'.
 */

use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\Route;


Route::namespace('Common')->group(function () {

    Route::get('countries', 'CountryController@index'); 
    Route::get('on-boarding', 'CountryController@onBoarding');    
    Route::get('on-boarding-driver', 'CountryController@onBoardingDriver');    
    Route::get('on-boarding-owner', 'CountryController@onBoardingOwner');    

    
    // Masters Crud
    Route::prefix('common')->group(function () {

        
        // Get owner driverModule
        Route::get('modules', 'CarMakeAndModelController@getAppModule');     
        
        // Test Api
        Route::get('test-api','CarMakeAndModelController@testApi');


        Route::get('ride_modules', 'CarMakeAndModelController@mobileAppMenu');        

         // goods type
         Route::get('goods-types', 'GoodsTypesController@index');
   

        Route::middleware(['auth:sanctum','throttle:120,1'])->group(function () {

            // goods type
            // Route::get('goods-types', 'GoodsTypesController@index');

            // List Cancallation Reasons
            Route::get('cancallation/reasons', 'CancellationReasonsController@index');
            // List Faq
            Route::get('faq/list/{lat}/{lng}', 'FaqController@index');
            // List Sos
            Route::get('sos/list/{lat}/{lng}', 'SosController@index');
            // Store Sos by users
            Route::post('sos/store', 'SosController@store');
            // Delete Sos by User
            Route::post('sos/delete/{sos}', 'SosController@delete');
            // List Complaint titles
            Route::get('complaint-titles', 'ComplaintsController@index');
            // Make a complaint
            Route::post('make-complaint', 'ComplaintsController@makeComplaint');

            //List Ticket Titles
            Route::get('ticket-titles', 'SupportTicketController@index');
            // Make a Ticket
            Route::post('make-ticket', 'SupportTicketController@makeTicket');
            // Reply message for ticket
            Route::post('reply-message/{supportTicket}', 'SupportTicketController@replyMessage');
            //View Ticket Details
            Route::get('view-ticket/{supportTicket}', 'SupportTicketController@viewTicketDetails');
            //Ticket List 
            Route::get('list', 'SupportTicketController@tikcetList');


        });
    });
    // Validate Company key api
    // Route::post('validate-company-key', 'CompanyKeyController@validateCompanyKey');
});

Route::namespace('VehicleType')->prefix('types')->group(function () {
    // get types depends service location
    Route::get('/{service_location}', 'VehicleTypeController@getVehicleTypesByServiceLocation');
    Route::get('/sub-vehicle/{service_location}', 'VehicleTypeController@getSubVehicleTypesByServiceLocation');
});

Route::namespace('Notification')->prefix('notifications')->middleware(['auth:sanctum','throttle:120,1'])->group(function (){
    Route::get('get-notification', 'ShowNotificationController@getNotifications');
    Route::any('delete-notification/{notification}', 'ShowNotificationController@deleteNotification');

});