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
 * These routes are prefixed with 'api/v1/payment'.
 * These routes use the root namespace 'App\Http\Controllers\Api\V1\Payment'.
 * These routes use the middleware group 'auth'.
 */
Route::prefix('payment')->namespace('Payment')->middleware(['auth:sanctum','throttle:30,1'])->group(function () {

    /**
     * These routes use the middleware group 'role'.
     * These routes are accessible only by a user with the 'user' role.
     */
        Route::prefix('cards')->group(function(){
            Route::get('list','PaymentController@listCards');
            Route::post('make-default','PaymentController@makeDefaultCard');
            Route::post('delete/{card}','PaymentController@deleteCard');
        });


        Route::prefix('wallet')->group(function () {
            Route::get('history', 'PaymentController@walletHistory');
            Route::get('withdrawal-requests','PaymentController@withDrawalRequests');
            Route::post('request-for-withdrawal','PaymentController@requestForWithdrawal');
            Route::post('transfer-money-from-wallet', 'PaymentController@transferMoneyFromWallet');
            Route::post('convert-point-to-wallet', 'PaymentController@transferCreditFromPoints');
        });

        // List,Add,Active/Inactive & Delete Cards Api

        // Add cards feature by using Stripe payment gateway
        Route::prefix('stripe')->namespace('Stripe')->group(function () {
            Route::post('create-setup-intent', 'StripeController@createStripeIntent');
            Route::post('save-card','StripeController@saveCard');
            Route::post('add-money-to-wallet','StripeController@addMoneyToWalletByStripe');
        });

        Route::prefix('orange')->namespace('Orange')->group(function () {
            Route::get('/','OrangeController@makePayment');
        }); 

        Route::prefix('mercadopago')->namespace('Mercadopago')->group(function () {
            Route::get('/','MercadoPagoController@makePayment');
        }); 

        Route::prefix('bankily')->namespace('Bankily')->group(function () {
            Route::get('/authenticate','BankilyController@authenticate');
            Route::get('/refresh','BankilyController@refresh');
            Route::get('/payment','BankilyController@payment');
            Route::get('/status','BankilyController@status');
        }); 
});

 Route::prefix('payment')->namespace('Payment')->group(function () {
          
        Route::middleware(['auth:sanctum','throttle:30,1'])->get('gateway','PaymentController@paymentGateways');

        Route::get('gateway-for-ride','PaymentController@paymentGatewaysForRide');


          Route::prefix('stripe')->namespace('Stripe')->group(function () {
            Route::post('listen-webhooks','StripeController@listenWebHooks');

        }); 

});

