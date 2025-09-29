<?php
use Illuminate\Support\Facades\Route;

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

/*
 * These routes are prefixed with 'api/v1/user'.
 * These routes use the root namespace 'App\Http\Controllers\Api\V1\User'.
 * These routes use the middleware group 'auth'.
 */
Route::prefix('user')->namespace('User')->group(function () {
    // Get the logged in user.
    Route::middleware(['auth:sanctum','throttle:120,1'])->get('/', 'AccountController@me');
    /**
     * These routes use the middleware group 'role'.
     * These routes are accessible only by a user with the 'user' role.
     */
    Route::middleware(['auth:sanctum','throttle:120,1'])->group(function () {

        // Update user profile.
        Route::post('profile', 'ProfileController@updateProfile');
        Route::post('driver-profile', 'ProfileController@updateDriverProfile');
        Route::post('update-my-lang', 'ProfileController@updateMyLanguage');
        Route::post('update-bank-info','ProfileController@updateBankinfo');
        Route::get('get-bank-info','ProfileController@getBankInfo');
        // Add Favourite location
        Route::get('list-favourite-location','ProfileController@FavouriteLocationList');
        Route::post('add-favourite-location','ProfileController@addFavouriteLocation');
        Route::get('delete-favourite-location/{favourite_location}','ProfileController@deleteFavouriteLocation');
        // Delete user Account.
        Route::post('delete-user-account','ProfileController@userDeleteAccount');
        Route::post('update-location', 'ProfileController@updateLocation');

    });
});


