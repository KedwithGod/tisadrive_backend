<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingQuickLinkController;
use App\Http\Controllers\ShutdownController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::get('/shutdown', [ShutdownController::class, 'shutdownApplication']);
Route::get('/bring-down', [ShutdownController::class, 'bringDown']);

Route::namespace ('Api')->group(function () {

    /**
     * These routes are prefixed with 'api/v1'.
     * These routes use the root namespace 'App\Http\Controllers\Api\V1'.
     */
    Route::prefix('v1')->namespace('V1')->group(function () {
        include_route_files('api/v1');
    });

    Route::get('/privacy-content', [LandingQuickLinkController::class,'getPrivacyContent'])->name('privacy-content');
    Route::get('/terms-content', [LandingQuickLinkController::class,'getTermsContent'])->name('terms-content');
    Route::get('/compliance-content', [LandingQuickLinkController::class,'getComplianceContent'])->name('compliance-content');
    Route::get('/dmv-content', [LandingQuickLinkController::class,'getDmvContent'])->name('dmv-content');
});
