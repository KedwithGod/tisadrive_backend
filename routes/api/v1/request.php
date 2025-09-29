<?php

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| These routes are prefixed with 'api/v1'.
| These routes use the root namespace 'App\Http\Controllers\Api\V1'.
|
 */
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\Route;

/*
 * These routes are prefixed with 'api/v1/request'.
 * These routes use the root namespace 'App\Http\Controllers\Api\V1\Request'.
 * These routes use the middleware group 'auth'.
 */
Route::prefix('request')->namespace('Request')->middleware(['auth:sanctum','throttle:120,1'])->group(function () {

//outstation rides
    /**
     * These routes use the middleware group 'role'.
     * These routes are accessible only by a user with the 'user' role.
     */
        // List Packages
        Route::post('list-packages', 'EtaController@listPackages');

        Route::get('promocode-list', 'PromoCodeController@index');
        // Create Request
        Route::post('create', 'CreateRequestController@createRequest');
        Route::post('delivery/create', 'DeliveryCreateRequestController@createRequest');
        // Change Drop Location
        Route::post('change-drop-location', 'EtaController@changeDropLocation');
        // Cancel Request
        Route::post('cancel', 'UserCancelRequestController@cancelRequest');
        // Accept/Decline Bidd Request
        Route::post('respond-for-bid','CreateRequestController@respondForBid');
        //payment methodd
        Route::post('user/payment-method', 'UserCancelRequestController@paymentMethod');

        Route::post('user/payment-confirm', 'UserCancelRequestController@userPaymentConfirm');

        Route::post('user/driver-tip', 'UserCancelRequestController@driverTip');

        // Route::post('ready-to-pickup','DriverTripStartedController@readyToPickup');


    // Eta
    Route::post('eta', 'EtaController@eta');
    Route::post('serviceVerify', 'EtaController@serviceVerify');
    Route::post('list-recent-searches', 'EtaController@recentSearches');
    Route::get('get-directions', 'EtaController@getDirections');

    /**
     * These routes use the middleware group 'role'.
     * These routes are accessible only by a driver with the 'driver' role.
     */
        // Create Instant Ride
        Route::post('create-instant-ride','InstantRideController@createRequest');
        Route::post('create-delivery-instant-ride','InstantRideController@createDeliveryRequest');

        // Accet/Reject Request
        Route::post('respond', 'RequestAcceptRejectController@respondRequest');
        // Arrived
        Route::post('arrived', 'DriverArrivedController@arrivedRequest');
        // Trip started
        Route::post('started', 'DriverTripStartedController@tripStart');
        // Cancel Request
        Route::post('cancel/by-driver', 'DriverCancelRequestController@cancelRequest');
        // End Request
        Route::post('end', 'DriverEndRequestController@endRequest');
        // TripMeter Request
        Route::post('trip-meter', 'DriverEndRequestController@tripMeterRideUpdate');
        // Upload Delivery Proof
        Route::post('upload-proof','DriverDeliveryProofController@uploadDocument');
        // payment Conmfirm Request
        Route::post('payment-confirm', 'DriverEndRequestController@paymentConfirm');

        Route::post('payment-method', 'DriverEndRequestController@paymentMethod');
        
        Route::post('ready-to-pickup','DriverTripStartedController@readyToPickup');

        Route::post('stop-complete', 'DriverEndRequestController@tripEndBystop');

        Route::post('stop-otp-verify', 'DriverEndRequestController@stopOtpVerify');

        Route::post('additional-charge', 'DriverEndRequestController@additionalChargeUpdate');
        
        
    // History
    Route::get('history', 'RequestHistoryController@index');
    // Route::get('history/outstation', 'RequestHistoryController@outStationHistory');
    Route::get('history/{id}', 'RequestHistoryController@getById');
    Route::get('invoice/{requestmodel}', 'RequestHistoryController@invoice');
    // Rate the Request
    Route::post('rating', 'RatingsController@rateRequest');
    // Chat 
    Route::get('chat-history/{request}','ChatController@history');
    //Send Sms
    Route::post('send','ChatController@send');
    // Update Seen
    Route::post('seen','ChatController@updateSeen');
    
    //conversation
    Route::get('user-chat-history','ChatController@initiateConversation');
    Route::post('user-send-message','ChatController@sendMessage');     

});
 