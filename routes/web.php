<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Role1Controller;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\ServiceLocationController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\RentalPackageTypeController;
use App\Http\Controllers\SetPriceController;
use App\Http\Controllers\ComplaintTitleController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\SosController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\CancellationController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\UserController ;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\SmsGatewayController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\MailConfigurationController;
use App\Http\Controllers\DriverManagementController;
use App\Http\Controllers\FleetDriverController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ManageOwnerController;
use App\Http\Controllers\GoodsTypeController;
use App\Http\Controllers\BannerImageController;
use App\Http\Controllers\OwnerManagementController;
use App\Http\Controllers\OnboardingScreenController;
use App\Http\Controllers\InvoiceConfigurationController;
use App\Http\Controllers\MapSettingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TripRequestController;
use App\Http\Controllers\DeliveryRequestController;
use App\Http\Controllers\ManageFleetController;
use App\Http\Controllers\DispatcherController ;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\LandingSiteController;
use App\Http\Controllers\UserWebBookingController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LandingHeaderController;
use App\Http\Controllers\LandingHomeController;
use App\Http\Controllers\LandingDriverController;
use App\Http\Controllers\LandingUserController;
use App\Http\Controllers\LandingContactController;
use App\Http\Controllers\LandingAboutsController;
use App\Http\Controllers\LandingQuickLinkController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\OwnerDashBoardController;
use App\Http\Controllers\Web\Admin\DispatcherCreateRequestController;
use App\Http\Controllers\RecaptchaController;
use App\Http\Controllers\Web\PayPalController;
use App\Http\Controllers\Web\StripeController;
use App\Http\Controllers\Web\FlutterwaveController;
use App\Http\Controllers\Web\CashfreeController;
use App\Http\Controllers\Web\KhaltiController;
use App\Http\Controllers\Web\RazorPayController;
use App\Http\Controllers\Web\MercadopagoController;
use App\Http\Controllers\Web\CcavenueController;
use App\Http\Controllers\Web\EasypaisaController;
use App\Http\Controllers\Api\V1\Request\EtaController;
use App\Http\Controllers\Web\PaystackController;
use App\Http\Controllers\Web\PayphoneController;
use App\Http\Controllers\Web\PaytechController;
use App\Http\Controllers\Web\FlexpaieController;
use App\Http\Controllers\BankInfoController;
use App\Http\Controllers\Web\IncentiveController;
use App\Http\Controllers\NotificationChannelController;
use Inertia\Inertia;
use App\Http\Controllers\Dompdf;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\support\SupportTicketCategoryController;
use App\Http\Controllers\support\SupportTicketTitleController;
use App\Http\Controllers\support\SupportTicketsListController;
use App\Http\Controllers\ColourController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
 * These routes use the root namespace 'App\Http\Controllers\Web'.
 */
Route::namespace('Web')->group(function () {

    // All the folder based web routes
    include_route_files('web');

});

Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {
        
        Route::get('/dashboard', [DashBoardController::class, 'index'])->name('dashboard');
        Route::get('/owner-dashboard', [OwnerDashBoardController::class, 'index'])->name('owner.dashboard');

        Route::get('/dashboard/today-earnings', [DashBoardController::class, 'todayEarnings'])->name('dashboard-todayEarnings');
        Route::get('/dashboard/overall-earnings', [DashBoardController::class, 'overallEarnings'])->name('dashboard-overallEarnings');
        Route::get('/dashboard/cancel-chart', [DashBoardController::class, 'cancelChart'])->name('dashboard-cancelChart');



        Route::get('/dashboard/{id}', [DashBoardController::class, 'serviceLocationIndex'])->name('serviceLocation.dashboard');

        Route::get('/individual-owner-dashboard', [OwnerDashBoardController::class, 'IndividualDashboard'])->name('owner.IndividualDashboard');



Route::get('/overall-menu', [DashBoardController::class, 'overallMenu'])->name('overall.menu');

Route::group(['prefix' => 'roles'], function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/list', [RoleController::class, 'getRoles'])->name('roles.list');
    Route::post('/', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/edit/{role}', [RoleController::class, 'edit']);
    Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/{role}', [RoleController::class, 'destroy']);
});

Route::group(['prefix' => 'permissions'], function () {
    Route::get('/{permission}', [PermissionController::class, 'index'])->name('permission.index');
    Route::post('/{role}', [PermissionController::class, 'store'])->name('permission.store');
});


// service locations
Route::group(['prefix' => 'service-locations'], function () {
    Route::get('/', [ServiceLocationController::class, 'index'])->name('servicelocation.index');
    Route::get('/list', [ServiceLocationController::class, 'list'])->name('service-location-list');
    Route::get('/create', [ServiceLocationController::class, 'create'])->name('servicelocation.create');
    Route::get('/edit/{id}', [ServiceLocationController::class, 'edit'])->name('servicelocation.edit');
    Route::post('/store', [ServiceLocationController::class, 'store'])->name('servicelocation.store');
    Route::post('/update/{location}', [ServiceLocationController::class, 'update'])->name('servicelocation.update');
    Route::post('/toggle/{location}', [ServiceLocationController::class, 'toggle'])->name('servicelocation.toggle');
    // Route::delete('/delete/{location}', [ServiceLocationController::class, 'delete'])->name('servicelocation.delete');
});



// rental package types
Route::group(['prefix' => 'rental-package-types'], function () {
    Route::get('/', [RentalPackageTypeController::class, 'index'])->name('rentalpackagetype.index');
    Route::get('/create', [RentalPackageTypeController::class, 'create'])->name('rentalpackagetype.create');
    Route::post('/store', [RentalPackageTypeController::class, 'store'])->name('rentalpackagetype.store');
    Route::get('/list', [RentalPackageTypeController::class, 'list'])->name('rentalpackagetype.list');
    Route::get('/edit/{id}', [RentalPackageTypeController::class, 'edit'])->name('rentalpackagetype.edit');
    Route::post('/update/{packageType}', [RentalPackageTypeController::class, 'update'])->name('rentalpackagetype.update');
    Route::post('/update-status', [RentalPackageTypeController::class, 'updateStatus'])->name('rentalpackagetype.updateStatus');
    Route::delete('/delete/{packageType}', [RentalPackageTypeController::class, 'destroy'])->name('rentalpackagetype.delete');

});



// set prices
Route::group(['prefix' => 'set-prices'], function () {
    Route::get('/', [SetPriceController::class, 'index'])->name('setprice.index');
    Route::get('/create', [SetPriceController::class, 'create'])->name('setprice.create');
    Route::get('/vehicle_types', [SetPriceController::class, 'fetchVehicleTypes'])->name('setprice.vehiclelist');
    Route::post('/store', [SetPriceController::class, 'store'])->name('setprice.store');
    Route::get('/list', [SetPriceController::class, 'list'])->name('setprice.list');
    Route::get('/edit/{id}', [SetPriceController::class, 'edit'])->name('setprice.edit');
    Route::post('/update/{zoneTypePrice}', [SetPriceController::class, 'update'])->name('setprice.update');
    Route::delete('/delete/{id}', [SetPriceController::class, 'destroy'])->name('setprice.delete');
    Route::post('/update-status', [SetPriceController::class, 'updateStatus'])->name('setprice.updateStatus');
//package-price    
    Route::get('/packages/{zoneType}', [SetPriceController::class, 'packageIndex'])->name('setprice.packageIndex');
    Route::get('/packages/list/{zoneTypePrice}', [SetPriceController::class, 'packageList'])->name('setprice.packageList');
    Route::get('/packages/create/{zoneTypePrice}', [SetPriceController::class, 'packageCreate'])->name('setprice.package-create');
    Route::post('/packages/store', [SetPriceController::class, 'packageStore'])->name('setprice.packageStore');
    Route::get('/packages/edit/{zoneTypePackage}', [SetPriceController::class, 'packageEdit'])->name('setprice.package-edit');
    Route::post('/packages/update/{zoneTypePackage}', [SetPriceController::class, 'updatePackage'])->name('setprice.package-update');
    Route::delete('/packages/delete/{zoneTypePackage}', [SetPriceController::class, 'destroyPackage'])->name('setprice.package-delete');
    Route::post('/packages/update-status', [SetPriceController::class, 'updatePackageStatus'])->name('setprice.updatePackageStatus');

// Surge Price
    Route::get('/surge/{zoneType}', [SetPriceController::class, 'surge'])->name('zone.surge');
    Route::post('/surge/update/{zoneType}', [SetPriceController::class, 'updateSurge'])->name('zone.updateSurge');

});

// vehicle Type
Route::group(['prefix' => 'vehicle_type'], function () {
    Route::get('/', [VehicleTypeController::class, 'index'])->name('vehicletype.index');
    Route::get('/create', [VehicleTypeController::class, 'create'])->name('vehicletype.create');
    Route::post('/store', [VehicleTypeController::class, 'store'])->name('vehicletype.store');
    Route::get('/list', [VehicleTypeController::class, 'list'])->name('vehicletype.list');
    Route::get('/edit/{id}', [VehicleTypeController::class, 'edit'])->name('vehicletype.edit');
    Route::post('/update/{vehicle_type}', [VehicleTypeController::class, 'update'])->name('vehicletype.update');
    // Route::delete('/delete/{vehicle_type}', [VehicleTypeController::class, 'destroy'])->name('vehicletype.delete');
    Route::post('/update-status', [VehicleTypeController::class, 'updateStatus'])->name('vehicletype.updateStatus');

});


//emergency number
Route::group(['prefix' => 'sos'], function () {
    Route::get('/', [SosController::class, 'index'])->name('sos.index');
    Route::middleware('remove_empty_query')->get('/list', [SosController::class, 'list'])->name('sos.list');
    Route::get('/create', [SosController::class, 'create'])->name('sos.create');
    Route::post('/store', [SosController::class, 'store'])->name('sos.store');
    Route::get('/edit/{id}', [SosController::class, 'edit'])->name('sos.edit');
    Route::post('/update/{sos}', [SosController::class, 'update'])->name('sos.update');
    Route::post('/update-status', [SosController::class, 'updateStatus'])->name('sos.updateStatus');
    Route::delete('/delete/{sos}', [SosController::class, 'destroy'])->name('sos.delete');

});

Route::group(['prefix' => 'driver-bank-info'], function () {
    Route::get('/', [BankInfoController::class, 'index'])->name('bank.index');
    Route::get('/list', [BankInfoController::class, 'list'])->name('bank.list');
    Route::get('/create', [BankInfoController::class, 'create'])->name('bank.create');
    Route::post('/store', [BankInfoController::class, 'store'])->name('bank.store');
    Route::get('/edit/{method}', [BankInfoController::class, 'edit'])->name('bank.edit');
    Route::post('/update/{method}', [BankInfoController::class, 'update'])->name('bank.update');
    Route::post('/update-status', [BankInfoController::class, 'updateStatus'])->name('bank.updateStatus');
    Route::delete('/delete/{method}', [BankInfoController::class, 'destroy'])->name('bank.delete');

});

//promo code
Route::group(['prefix' => 'promo-code'], function () {
    Route::get('/', [PromoCodeController::class, 'index'])->name('promocode.index');
    Route::middleware('remove_empty_query')->get('/list', [PromoCodeController::class, 'list'])->name('promocode.list');
    Route::get('/userList', [PromoCodeController::class, 'userList'])->name('promocode.userList');
    Route::get('/create', [PromoCodeController::class, 'create'])->name('promocode.create');
    Route::post('/store', [PromoCodeController::class, 'store'])->name('promocode.store');
    Route::get('/edit/{id}', [PromoCodeController::class, 'edit'])->name('promocode.edit');
    Route::get('/fetch', [PromoCodeController::class, 'fetchServiceLocation'])->name('promocode.fetchServiceLocation');//service location list
    Route::post('/update/{promo}', [PromoCodeController::class, 'update'])->name('promocode.update');
    Route::delete('/delete/{promo}', [PromoCodeController::class, 'destroy'])->name('promocode.delete');
    Route::post('/update-status', [PromoCodeController::class, 'updateStatus'])->name('promocode.updateStatus');

});


//push notification
Route::group(['prefix' => 'push-notifications'], function () {
    Route::get('/', [PushNotificationController::class, 'index'])->name('pushnotification.index');
    Route::get('/create', [PushNotificationController::class, 'create'])->name('pushnotification.create');
    Route::middleware('remove_empty_query')->get('/list', [PushNotificationController::class, 'fetch'])->name('pushnotification.list');
    Route::get('/edit/{notification}', [PushNotificationController::class, 'edit'])->name('pushnotification.edit');
    Route::delete('/delete/{notification}', [PushNotificationController::class, 'delete'])->name('pushnotification.delete');
    Route::post('/send-push', [PushNotificationController::class, 'sendPush'])->name('pushnotification.send-push');
    Route::post('/update', [PushNotificationController::class, 'update'])->name('pushnotification.update');
});

//cancellation
Route::group(['prefix' => 'cancellation'], function () {
    Route::get('/', [CancellationController::class, 'index'])->name('cancellation.index');
    Route::middleware('remove_empty_query')->get('/list', [CancellationController::class, 'list'])->name('cancellation.list');
    Route::get('/create', [CancellationController::class, 'create'])->name('cancellation.create');
    Route::post('/store', [CancellationController::class, 'store'])->name('cancellation.store');
    Route::get('/edit/{id}', [CancellationController::class, 'edit'])->name('cancellation.edit');
    Route::post('/update/{cancellationReason}', [CancellationController::class, 'update'])->name('cancellation.update');
    Route::post('/update-status', [CancellationController::class, 'updateStatus'])->name('cancellation.updateStatus');
    Route::delete('/delete/{cancellationReason}', [CancellationController::class, 'delete'])->name('cancellation.delete');
});


//faq
Route::group(['prefix' => 'faq'], function () {
    Route::get('/', [FaqController::class, 'index'])->name('faq.index');
    Route::middleware('remove_empty_query')->get('/list', [FaqController::class, 'list'])->name('faq.list');
    Route::get('/create', [FaqController::class, 'create'])->name('faq.create');
    Route::post('/store', [FaqController::class, 'store'])->name('faq.store');
    Route::get('/edit/{id}', [FaqController::class, 'edit'])->name('faq.edit');
    Route::post('/update/{faq}', [FaqController::class, 'update'])->name('faq.update');
    Route::post('/update-status', [FaqController::class, 'updateStatus'])->name('faq.updateStatus');
    Route::delete('/delete/{faq}', [FaqController::class, 'destroy'])->name('faq.delete');

});

// Complaint Tittle
Route::group(['prefix' => 'complaint-title'], function () {
    Route::get('/', [ComplaintTitleController::class, 'index'])->name('complainttitle.index');
    Route::get('/create', [ComplaintTitleController::class, 'create'])->name('complainttitle.create');
    Route::middleware('remove_empty_query')->get('/list', [ComplaintTitleController::class, 'list'])->name('complainttitle.list');
    Route::post('/store', [ComplaintTitleController::class, 'store'])->name('complainttitle.store');
    Route::get('/edit/{id}', [ComplaintTitleController::class, 'edit'])->name('complainttitle.edit');
    Route::post('/update/{complaintTitle}', [ComplaintTitleController::class, 'update'])->name('complainttitle.update');
    Route::post('/update-status', [ComplaintTitleController::class, 'updateStatus'])->name('complainttitle.updateStatus');
    Route::delete('/delete/{complaintTitle}', [ComplaintTitleController::class, 'delete'])->name('complainttitle.delete');
});

//driver complaint
Route::group(['prefix' => 'driver-complaint'], function () {
    Route::get('/general-complaint', [ComplaintController::class, 'driverGeneralComplaint'])->name('drivergeneralcomplaint.driverGeneralComplaint');
    Route::middleware('remove_empty_query')->get('/list', [ComplaintController::class, 'driverList'])->name('driverGeneralComplaint.listComplaint');
    Route::get('/taken/{complaint}', [ComplaintController::class, 'taken'])->name('driverGeneralComplaint.taken');



    Route::get('/request-complaint', [ComplaintController::class, 'driverRequestComplaint'])->name('driverrequestcomplaint.driverRequestComplaint');
    Route::middleware('remove_empty_query')->get('/driver-request-list', [ComplaintController::class, 'driverRequestList'])->name('driverrequestcomplaint.requestListComplaint');

});

//user complaint
Route::group(['prefix' => 'user-complaint'], function () {
    Route::get('/general-complaint', [ComplaintController::class, 'userGeneralComplaint'])->name('usergeneralcomplaint.userGeneralComplaint');
    Route::middleware('remove_empty_query')->get('/list', [ComplaintController::class, 'list'])->name('usergeneralcomplaint.listComplaint');
    Route::get('/taken/{complaint}', [ComplaintController::class, 'taken'])->name('usergeneralcomplaint.taken');

    Route::get('/request-complaint', [ComplaintController::class, 'userRequestComplaint'])->name('userrequestcomplaint.userRequestComplaint');
    Route::middleware('remove_empty_query')->get('/request-list', [ComplaintController::class, 'requestList'])->name('usergeneralcomplaint.requestComplaint');

});


//owner complaint
Route::group(['prefix' => 'owner-complaint'], function () {
    Route::get('/general-complaint', [ComplaintController::class, 'ownerGeneralComplaint'])->name('ownergeneralcomplaint.ownerGeneralComplaint');
    Route::middleware('remove_empty_query')->get('/list', [ComplaintController::class, 'ownerList'])->name('ownergeneralcomplaint.listComplaint');
    Route::get('/taken/{complaint}', [ComplaintController::class, 'taken'])->name('ownergeneralcomplaint.taken');

    Route::get('/request-complaint', [ComplaintController::class, 'ownerRequestComplaint'])->name('ownerrequestcomplaint.ownerRequestComplaint');
    Route::middleware('remove_empty_query')->get('/request-list', [ComplaintController::class, 'OwnerRequestList'])->name('ownerrequestcomplaint.requestComplaint');

});

//user complaint


// payment gateway
Route::group(['prefix' => 'payment-gateway'], function () {
    Route::get('/', [PaymentGatewayController::class, 'index'])->name('paymentgateway.index');
    Route::post('/update', [PaymentGatewayController::class, 'update'])->name('paymentgateway.update');
    Route::post('/update-statuss', [PaymentGatewayController::class, 'updateStatus'])->name('paymentgateway.updateStatus');

});



// sms gateway
Route::group(['prefix' => 'sms-gateway'], function () {
    Route::get('/', [SmsGatewayController::class, 'index'])->name('smsgateway.index');
    Route::post('/update', [SmsGatewayController::class, 'update']);
});


// firebase
Route::group(['prefix' => 'firebase'], function () 
{
    Route::get('/', [FirebaseController::class, 'index'])->name('firebase.index');
    Route::get('/get', [FirebaseController::class, 'get'])->name('firebase.get');
    Route::post('/update', [FirebaseController::class, 'update']);
});
// mail configuration
Route::group(['prefix' => 'mail-configuration'], function () {
    Route::get('/', [MailConfigurationController::class, 'index'])->name('mailconfiguration.index');
    Route::post('/update', [MailConfigurationController::class, 'update']);
});

// recaptcha
Route::group(['prefix' => 'recaptcha'], function () {
    Route::get('/', [RecaptchaController::class, 'index'])->name('recaptcha.index');
    Route::post('/update', [RecaptchaController::class, 'update']);
});
    

// email template
Route::group(['prefix' => 'mail-template'], function () {
    Route::get('/', [EmailTemplateController::class, 'index'])->name('mail-template.index');
    Route::get('/create', [EmailTemplateController::class, 'create'])->name('mail-template.create');
    Route::get('/list', [EmailTemplateController::class, 'list'])->name('mail-template.list');
    Route::post('/store', [EmailTemplateController::class, 'store'])->name('mail-template.store');
    Route::get('/edit/{id}', [EmailTemplateController::class, 'edit'])->name('mail-template.edit');
    Route::post('/update/{emails}', [EmailTemplateController::class, 'update'])->name('mail-template.update');
    Route::delete('/delete/{emails}', [EmailTemplateController::class, 'destroy'])->name('mail-template.destroy');
});

// email template
Route::group(['prefix' => 'map'], function () {
    Route::get('/heat_map', [MapsettingController::class, 'heatmap'])->name('map.heatmap');
    Route::get('/gods_eye', [MapsettingController::class, 'godseye'])->name('map.godseye');


});


// approved drivers
Route::group(['prefix' => 'approved-drivers'], function () {
    Route::get('/', [DriverManagementController::class, 'approvedDriverIndex'])->name('approveddriver.Index');
    Route::get('/create', [DriverManagementController::class, 'create'])->name('approveddriver.create');    
    Route::post('/store', [DriverManagementController::class, 'store'])->name('approveddriver.store');
    Route::get('/edit/{id}', [DriverManagementController::class, 'edit'])->name('approveddriver.edit');
    Route::post('/update/{driver}', [DriverManagementController::class, 'update'])->name('approveddriver.update');
    Route::get('/password/edit/{id}', [DriverManagementController::class, 'editPassword'])->name('drivers.password.edit');
    Route::post('/password/update/{driver}', [DriverManagementController::class, 'updatePasswords'])->name('drivers.password.update');
    Route::post('/disapprove/{driver}', [DriverManagementController::class, 'disapprove'])->name('approveddriver.disapprove');
     Route::get('/view-profile/{driver}', [DriverManagementController::class, 'viewProfile'])->name('approveddriver.viewProfile');
     Route::get('/document-upolad', [DriverManagementController::class, 'uploadDocument'])->name('approveddriver.uploadDocument');  
     Route::get('/check-mobile/{mobile}', [DriverManagementController::class, 'checkMobileExists']);
     Route::get('/check-email/{email}', [DriverManagementController::class, 'checkEmailExists']);   
     Route::get('/check-mobile/{mobile}/{id}', [DriverManagementController::class, 'checkMobileExists'])->name('approveddriver.checkMobileExists');
     Route::get('/check-email/{email}/{id}', [DriverManagementController::class, 'checkEmailExists'])->name('approveddriver.checkEmailExists');
     Route::middleware('remove_empty_query')->get('/list', [DriverManagementController::class, 'list'])->name('approveddriver.list');
     Route::post('update/decline/reason', [DriverManagementController::class, 'UpdateDriverDeclineReason'])->name('approveddriver.UpdateDriverDeclineReason');

     Route::get('/document/{driver}', [DriverManagementController::class, 'approvedDriverViewDocument'])->name('approveddriver.ViewDocument');
     Route::get('/document/list/{driver}', [DriverManagementController::class, 'driverDocumentList'])->name('approveddriver.driverDocumentList');
     Route::middleware('remove_empty_query')->get('document/list/{driverId}', [DriverManagementController::class, 'documentList'])->name('approveddriver.listDocument');

     Route::get('/document-upload/{document}/{driverId}', [DriverManagementController::class, 'documentUpload'])->name('approveddriver.documentUpload');
     Route::post('/document-upload/{document}/{driverId}', [DriverManagementController::class, 'documentUploadStore'])->name('approveddriver.documentUploadStore');
     Route::get('/document-toggle/{documentId}/{driverId}/{status}', [DriverManagementController::class, 'approveDriverDocument'])->name('approveddriver.approveDriverDocument');

     Route::get('/update-documents/{driverId}', [DriverManagementController::class, 'updateAndApprove']);
     

     Route::delete('/delete/{driver}', [DriverManagementController::class, 'destroy']);
    // wallet-history/list
    Route::post('/wallet-add-amount/{driver}', [DriverManagementController::class, 'walletAddAmount'])->name('approveddriver.addAmount');

    Route::get('/wallet-history/list/{driver}', [DriverManagementController::class, 'walletHistoryList'])->name('approveddriver.walletHistoryList');
    Route::get('/request/list/{driver}', [DriverManagementController::class, 'requestList'])->name('approveddrivers.requestList');
    Route::patch('/restore/{id}', [DriverManagementController::class, 'restoreUser'])->name('approveddrivers.restore');

    Route::get('/documents/{driver}', [DriverManagementController::class, 'approveDocumentStatus'])->name('approveddriver.document');

});


//pending drivers
Route::group(['prefix' => 'pending-drivers'], function () {
    Route::get('/', [DriverManagementController::class, 'pendingDriverIndex'])->name('pendingdriver.indexIndex');     
});

//pending drivers
Route::group(['prefix' => 'drivers-levelup'], function () {
    // Route::get('/', [DriverManagementController::class, 'driverLevelUpIndex'])->name('driverlevelup.index');     
    Route::get('/list', [DriverManagementController::class, 'driverLevelList'])->name('driverlevelup.list');     
    Route::get('/{zoneType}', [DriverManagementController::class, 'driverLevelUpIndex'])->name('driverlevelup.index');
    // Route::get('/list/{zoneType}', [DriverManagementController::class, 'driverLevelList'])->name('driverlevelup.list');
    Route::post('/store', [DriverManagementController::class, 'driverLevelStore'])->name('approveddriver.driverLeveStore');
    Route::get('/edit/{level}', [DriverManagementController::class, 'driverLevelEdit'])->name('driverlevelup.edit');
    Route::post('/settingsUpdate', [DriverManagementController::class, 'settingsUpdate'])->name('driverlevelup.settingsUpdate');
    Route::post('/update/{level}', [DriverManagementController::class, 'driverLevelUpdate'])->name('approveddriver.driverLevelUpdate');
    Route::delete('/delete/{level}', [DriverManagementController::class, 'driverLevelDelete'])->name('approveddriver.driverLevelDelete');

    Route::get('/create/{zoneType}', [DriverManagementController::class, 'driverLevelUpCreate'])->name('driverlevelup.create');     
});


//drivers rating
Route::group(['prefix' => 'drivers-rating'], function () {
    Route::get('/', [DriverManagementController::class, 'driverRatingIndex'])->name('driversrating.driverRatingIndex');
    Route::middleware('remove_empty_query')->get('/list', [DriverManagementController::class, 'driverRatingList'])->name('driversrating.list');
    Route::get('/view-profile/{driver}', [DriverManagementController::class, 'viewDriverRating'])->name('driversrating.viewDriverRating');
    Route::get('/request-list/{driver}', [DriverManagementController::class, 'driverRatinghistory'])->name('driversRequestRating.history');

});

//delete request drivers
Route::group(['prefix' => 'delete-request-drivers'], function () {
    Route::get('/', [DriverManagementController::class, 'deleteRequestDriversIndex'])->name('deleterequestdrivers.index');
    Route::middleware('remove_empty_query')->get('/list', [DriverManagementController::class, 'deleteRequestList'])->name('deleterequestdrivers.list');
    Route::delete('/delete/{driver}', [DriverManagementController::class, 'destroyDriver'])->name('deleterequestdrivers.destroyDriver');

}); 

//driver needed document
Route::group(['prefix' => 'driver-needed-documents'], function () {
    Route::get('/', [DriverManagementController::class, 'driverNeededDocumentIndex'])->name('driverneededdocuments.Index');
    Route::middleware('remove_empty_query')->get('/list', [DriverManagementController::class, 'driverNeededDocumentList'])->name('driverneededdocuments.list');
    Route::get('/create', [DriverManagementController::class, 'driverNeededDocumentCreate'])->name('driverneededdocuments.Create');
    Route::post('/store', [DriverManagementController::class, 'driverNeededDocumentStore'])->name('driverneededdocuments.store');
    Route::post('/update/{driverNeededDocument}', [DriverManagementController::class, 'driverNeededDocumentUpdate'])->name('driverneededdocuments.Update');
    Route::get('/edit/{driverNeededDocument}', [DriverManagementController::class, 'driverNeededDocumentEdit'])->name('driverneededdocuments.edit');
    Route::post('/update-status', [DriverManagementController::class, 'updateDocumentStatus'])->name('driverneededdocuments.updateDocumentStatus');
    Route::delete('/delete/{driverNeededDocument}', [DriverManagementController::class, 'destroyDriverDocument'])->name('driverneededdocuments.destroyDriverDocument');

});

//withdrawal request drivers
Route::group(['prefix' => 'withdrawal-request-drivers'], function () {
    Route::get('/', [DriverManagementController::class, 'WithdrawalRequestDriversIndex'])->name('withdrawalrequestdrivers.index');
    Route::middleware('remove_empty_query')->get('/list', [DriverManagementController::class, 'WithdrawalRequestDriversList'])->name('withdrawalrequestdrivers.list');   
    Route::get('/view-in-detail/{driver}', [DriverManagementController::class, 'WithdrawalRequestDriversViewDetails'])->name('withdrawalrequestdrivers.ViewDetails');
//updatePaymentStatus
    Route::middleware('remove_empty_query')->get('/amounts/{driver_id}', [DriverManagementController::class, 'WithdrawalRequestAmount'])->name('withdrawalrequestAmount.list');   
    Route::post('/update-status', [DriverManagementController::class, 'updatePaymentStatus'])->name('withdrawalrequest.updateStatus');

});

//negative balance drivers
Route::group(['prefix' => 'negative-balance-drivers'], function () {
    Route::get('/', [DriverManagementController::class, 'negativeBalanceDriversIndex'])->name('negativebalancedrivers.index');
    Route::middleware('remove_empty_query')->get('/list', [DriverManagementController::class, 'negativeBalanceDriversList'])->name('negativebalancedrivers.list');
    Route::get('/view-profile/{driver}', [DriverManagementController::class, 'negativeBalanceDriverPaymentHistory'])->name('negativebalancedrivers.payment');

});


//admin
Route::group(['prefix' => 'admins'], function () {
    Route::get('/', [AdminController::class, 'index'])->name('admins.index');
    Route::middleware('remove_empty_query')->get('/list', [AdminController::class, 'list'])->name('admins.list');
    Route::get('/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('/store', [AdminController::class, 'store'])->name('admins.store');
    Route::post('/update/{adminDetail}', [AdminController::class, 'update'])->name('admins.update');
    Route::get('/edit/{adminDetail}', [AdminController::class, 'edit'])->name('admins.edit');
    Route::get('/password/edit/{adminDetail}', [AdminController::class, 'editPassword'])->name('adminDetail.password.edit');
    Route::post('/password/update/{adminDetail}', [AdminController::class, 'updatePasswords'])->name('adminDetail.password.update');
    Route::delete('/delete/{adminDetail}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::post('/update-status', [AdminController::class, 'updateStatus'])->name('admin.updateDocumentStatus');
    Route::get('/password/edit/{adminDetail}', [AdminController::class, 'editPassword'])->name('admins.password.edit');
    Route::post('/password/update/{adminDetail}', [AdminController::class, 'updatePasswords'])->name('admins.password.update');
});



//reports
Route::group(['prefix' => 'report'], function () {
    Route::get('/user-report', [ReportController::class, 'userReport'])->name('report.userReport');
    Route::post('/user-report-download', [ReportController::class, 'userReportDownload'])->name('report.userReportDownload');
    Route::get('/driver-report', [ReportController::class, 'driverReport'])->name('report.driverReport');
    Route::post('/driver-report-download', [ReportController::class, 'driverReportDownload'])->name('report.driverReportDownload');
    Route::get('/getVehicleTypes', [ReportController::class, 'getVehicleTypes'])->name('report.getVehicletypes');
    Route::get('/owner-report', [ReportController::class, 'ownerReport'])->name('report.ownerReport');
    Route::post('/owner-report-download', [ReportController::class, 'ownerReportDownload'])->name('report.ownerReportDownload');
    Route::get('/finance-report', [ReportController::class, 'financeReport'])->name('report.financeReport');
    Route::post('/finance-report-download', [ReportController::class, 'financeReportDownload'])->name('report.financeReportDownload');
    Route::get('/fleet-report', [ReportController::class, 'fleetReport'])->name('report.fleetReport');
    Route::get('/list-fleets', [ReportController::class, 'listFleet'])->name('report.listFleet');
    Route::post('/fleet-report-download', [ReportController::class, 'fleetReportDownload'])->name('report.fleetReportDownload');

    Route::get('/driver-duty-report', [ReportController::class, 'driverDutyReport'])->name('report.driverDutyReport');
    Route::post('/driver-duty-report-download', [ReportController::class, 'driverDutyReportDownload'])->name('report.driverDutyReportDownload');
    Route::get('/getDrivers', [ReportController::class, 'getDrivers'])->name('report.getDrivers');

    // Route::get('view-invoice/{request_detail}',[ReportController::class, 'downloadInvoice']);
    Route::post('/download-invoice', [ReportController::class, 'downloadInvoice']);
    


});
Route::get('/download-pdf', [ReportController::class, 'downloadPdf'])->name('download.pdf');
Route::post('/download-invoice', [ReportController::class, 'downloadInvoice']);

// manage owners
Route::group(['prefix' => 'manage-owners'], function () {
    Route::get('/', [ManageOwnerController::class, 'index'])->name('manageowners.index');
    Route::get('/create', [ManageOwnerController::class, 'create'])->name('manageowners.Create');
    Route::middleware('remove_empty_query')->get('/list', [ManageOwnerController::class, 'list'])->name('manageowners.list');
    Route::post('/store', [ManageOwnerController::class, 'store'])->name('manageowners.store');
    Route::get('/edit/{owner}', [ManageOwnerController::class, 'edit'])->name('manageowners.edit');
    Route::post('/update/{owner}', [ManageOwnerController::class, 'update'])->name('manageowners.update');
    Route::get('/password/edit/{owner}', [ManageOwnerController::class, 'editPassword'])->name('owner.password.edit');
    Route::post('/password/update/{owner}', [ManageOwnerController::class, 'updatePasswords'])->name('owner.password.update');
    Route::post('/approve/{owner}', [ManageOwnerController::class, 'approve'])->name('manageowners.approve');
    Route::delete('/delete/{owner}', [ManageOwnerController::class, 'delete'])->name('manageowners.delete');
    Route::get('/document/{owner}', [ManageOwnerController::class, 'document'])->name('manageowners.document');
    Route::get('/document/list/{owner}', [ManageOwnerController::class, 'driverDocumentList'])->name('manageowners.driverDocumentList');
    Route::get('/check-mobile/{mobile}', [ManageOwnerController::class, 'checkMobileExists']);
    Route::get('/check-email/{email}', [ManageOwnerController::class, 'checkEmailExists']);   
    Route::get('/check-mobile/{mobile}/{id}', [ManageOwnerController::class, 'checkMobileExists'])->name('manageowners.checkMobileExists');
    Route::get('/check-email/{email}/{id}', [ManageOwnerController::class, 'checkEmailExists'])->name('manageowners.checkEmailExists');
    Route::get('/document-upload/{document}/{ownerId}', [ManageOwnerController::class, 'documentUpload'])->name('manageowners.documentUpload');
    Route::post('/document-upload/{document}/{ownerId}', [ManageOwnerController::class, 'documentUploadStore'])->name('manageowners.documentUploadStore');
    Route::get('/document-toggle/{documentId}/{ownerId}/{status}', [ManageOwnerController::class, 'approvOwnerDocument'])->name('manageowners.approveOwnerDocument');
    Route::get('/update/{ownerId}', [ManageOwnerController::class, 'updateAndApprove']);
    Route::get('/owner-payment-history/{owner}', [ManageOwnerController::class, 'ownerPaymentHistory'])->name('manageowners.ownerPaymentHistory');
    Route::get('/view-profile/{owner}', [ManageOwnerController::class, 'viewProfile'])->name('manageowners.viewProfile');
    Route::post('/update-status', [ManageOwnerController::class, 'updateStatus'])->name('manageowners.updateStatus');
    Route::get('/view-owner-driver/list/{owner}', [ManageOwnerController::class, 'driverList'])->name('manageowners.driverList');
    Route::get('/view-owner-fleet/list/{owner}', [ManageOwnerController::class, 'fleetList'])->name('manageowners.fleetList');

     // wallet-history/list
     Route::post('/wallet-add-amount/{owner}', [ManageOwnerController::class, 'walletAddAmount'])->name('manageowners.addAmount');

     Route::get('/wallet-history/list/{owner}', [ManageOwnerController::class, 'walletHistoryList'])->name('manageowners.walletHistoryList');
     Route::get('/deleted-owner', [ManageOwnerController::class, 'deletedOwner'])->name('owner.deleted-owner');
     Route::get('/deletedList', [ManageOwnerController::class, 'deletedList'])->name('owner.deletedList');
     Route::patch('/restore/{id}', [ManageOwnerController::class, 'restoreOwner'])->name('owner.restore');
});
    //withdrawal request owners
    Route::group(['prefix' => 'withdrawal-request-owners'], function () {
        Route::get('/', [ManageOwnerController::class, 'WithdrawalRequestOwnersIndex'])->name('withdrawalrequestOwners.index');
        Route::middleware('remove_empty_query')->get('/list', [ManageOwnerController::class, 'WithdrawalRequestOwnersList'])->name('withdrawalrequestOwners.list');   
        Route::get('/view-in-detail/{owner}', [ManageOwnerController::class, 'WithdrawalRequestOwnersViewDetails'])->name('withdrawalrequestOwners.ViewDetails');
    //updatePaymentStatus
        Route::middleware('remove_empty_query')->get('/amounts/{owner_id}', [ManageOwnerController::class, 'WithdrawalRequestAmount'])->name('withdrawalrequestOwner.list');   
        Route::post('/update-status', [ManageOwnerController::class, 'updatePaymentStatus'])->name('withdrawalrequestOwners.updateStatus');

    });

//fleet needed documents
Route::group(['prefix' => 'fleet-needed-documents'], function () {
    Route::get('/', [ManageFleetController::class, 'fleetNeededDocumentIndex'])->name('fleetneeddocuments.index');
    Route::middleware('remove_empty_query')->get('/list', [ManageFleetController::class, 'fleetNeededDocumentList'])->name('fleetneeddocuments.list');
    Route::get('/create', [ManageFleetController::class, 'fleetNeededDocumentCreate'])->name('fleetneeddocuments.create');
    Route::post('/store', [ManageFleetController::class, 'fleetNeededDocumentStore'])->name('fleetneeddocuments.store');
    Route::get('/edit/{document}', [ManageFleetController::class, 'fleetNeededDocumentEdit'])->name('fleetneeddocuments.edit');
    Route::post('/update/{document}', [ManageFleetController::class, 'fleetNeededDocumentUpdate'])->name('fleetneeddocuments.update');
    Route::post('/toggle', [ManageFleetController::class, 'fleetNeededDocumentToggle'])->name('fleetneeddocuments.updatestatus');
    Route::delete('/delete/{document}', [ManageFleetController::class, 'fleetNeededDocumentDelete'])->name('fleetneeddocuments.delete');
});

// manage fleet
Route::group(['prefix' => 'manage-fleet'], function () {
    Route::get('/', [ManageFleetController::class, 'index'])->name('managefleets.index');
    Route::get('/create', [ManageFleetController::class, 'create'])->name('managefleets.Create');
    Route::middleware('remove_empty_query')->get('/list', [ManageFleetController::class, 'list'])->name('managefleets.list');
    Route::post('/store', [ManageFleetController::class, 'store'])->name('managefleets.store');
    Route::get('/edit/{fleet}', [ManageFleetController::class, 'edit'])->name('managefleets.edit');
    Route::post('/update/{fleet}', [ManageFleetController::class, 'update'])->name('managefleets.update');
    Route::post('/assign/{fleet}/{driver}', [ManageFleetController::class, 'assignDriver'])->name('managefleets.assignDriver');
    Route::post('/approve/{fleet}', [ManageFleetController::class, 'approve'])->name('managefleets.approve');
    Route::delete('/delete/{fleet}', [ManageFleetController::class, 'delete'])->name('managefleets.delete');
    Route::get('/document/{fleet}', [ManageFleetController::class, 'document'])->name('managefleets.document');
    Route::get('/document/list/{fleet}', [ManageFleetController::class, 'listDocument'])->name('managefleets.listDocument');
    Route::get('/listFleetDriver/{fleet}', [ManageFleetController::class, 'listFleetDrivers'])->name('managefleets.listFleetDrivers');
   
    Route::get('/document-upload/{document}/{fleetId}', [ManageFleetController::class, 'documentUpload'])->name('managefleets.documentUpload');
    Route::post('/document-upload/{document}/{fleetId}', [ManageFleetController::class, 'documentUploadStore'])->name('managefleets.documentUploadStore');
    Route::get('/document-toggle/{documentId}/{fleetId}/{status}', [ManageFleetController::class, 'approvfleetDocument'])->name('managefleets.approvefleetDocument');
    Route::get('/update-document/{fleetId}', [ManageFleetController::class, 'updateAndApprove']);
    Route::get('/fleet-payment-history/{fleet}', [ManageFleetController::class, 'fleetPaymentHistory'])->name('managefleets.fleetPaymentHistory');
    Route::get('/documents/{fleet}', [ManageFleetController::class, 'approveDocumentStatus'])->name('approveddriver.approveDocumentStatus');
});
//fleet drivers
Route::group(['prefix' => 'fleet-drivers'], function () {
    Route::get('/', [FleetDriverController::class, 'index'])->name('approvedFleetdriver.Index');
    Route::get('/pending', [FleetDriverController::class, 'pendingIndex'])->name('approvedFleetdriver.pendingIndex');
    Route::middleware('remove_empty_query')->get('list', [FleetDriverController::class, 'listDrivers'])->name('fleet-drivers.list');
    Route::post('/store', [FleetDriverController::class, 'store'])->name('fleet-drivers.store');
    Route::get('/edit/{driver}', [FleetDriverController::class, 'edit'])->name('fleet-drivers.edit');
    Route::get('/view-profile/{driver}', [FleetDriverController::class, 'viewProfile'])->name('fleet-drivers.viewProfile');
    Route::get('create', [FleetDriverController::class, 'create'])->name('fleet-drivers.create');
    Route::post('/update/{driver}', [FleetDriverController::class, 'update'])->name('fleet-drivers.update');
    Route::post('/approve/{driver}', [FleetDriverController::class, 'approve'])->name('fleet-drivers.approve');
    Route::delete('/delete/{driver}', [FleetDriverController::class, 'delete'])->name('fleet-drivers.delete');
    Route::get('/ownerList', [FleetDriverController::class, 'listOwnersByLocation'])->name('fleet-drivers.listOwnersByLocation');
    Route::get('/list-owners', [FleetDriverController::class, 'listOwners'])->name('fleet-drivers.listOwners');
    Route::get('/document/{driver}', [FleetDriverController::class, 'approvedDriverViewDocument'])->name('approvedFleetdriver.ViewDocument');
    Route::get('/document/list/{driver}', [FleetDriverController::class, 'driverDocumentList'])->name('approvedFleetdriver.driverDocumentList');
    Route::middleware('remove_empty_query')->get('document/list/{driverId}', [FleetDriverController::class, 'documentList'])->name('approvedFleetdriver.listDocument');
    Route::get('/document-upload/{document}/{driverId}', [FleetDriverController::class, 'documentUpload'])->name('approvedFleetdriver.documentUpload');
    Route::post('/document-upload-store/{document}/{driverId}', [FleetDriverController::class, 'documentUploadStore'])->name('approvedFleetdriver.documentUploadStore');
    Route::get('/document-toggle/{documentId}/{driverId}/{status}', [FleetDriverController::class, 'approveDriverDocument'])->name('approvedFleetdriver.approveDriverDocument');
    Route::get('/update/{driverId}', [FleetDriverController::class, 'updateAndApprove']);
    Route::get('/pending-drivers', [FleetDriverController::class, 'pendingDriverIndex'])->name('pendingdriver.fleetIndex');  
    Route::get('/password/edit/{driver}', [FleetDriverController::class, 'editPassword'])->name('fleet-drivers.password.edit');
    Route::post('/password/update/{driver}', [FleetDriverController::class, 'updatePasswords'])->name('fleet-drivers.password.update'); 
    Route::post('update/decline/reason', [FleetDriverController::class, 'UpdateDriverDeclineReason'])->name('approveddriver.UpdateDriverReason');

    Route::get('/documents/{driver}', [FleetDriverController::class, 'approveDocumentStatus'])->name('approveddriver.approveDocument');

    Route::get('/check-mobile/{mobile}', [FleetDriverController::class, 'checkMobileExists']);
    Route::get('/check-email/{email}', [FleetDriverController::class, 'checkEmailExists']);   
    Route::get('/check-mobile/{mobile}/{id}', [FleetDriverController::class, 'checkMobileExists'])->name('approvedFleetdriver.checkMobileExists');
    Route::get('/check-email/{email}/{id}', [FleetDriverController::class, 'checkEmailExists'])->name('approvedFleetdriver.checkEmailExists');

});



//goods type
Route::group(['prefix' => 'goods-type'], function () {
    Route::get('/', [GoodsTypeController::class, 'index'])->name('goodstype.index');
    Route::get('/create', [GoodsTypeController::class, 'create'])->name('goodstype.create');
    Route::post('/store', [GoodsTypeController::class, 'store'])->name('goodstype.store');
    Route::get('/list', [GoodsTypeController::class, 'list'])->name('goodstype.list');
    Route::get('/edit/{id}', [GoodsTypeController::class, 'edit'])->name('goodstype.edit');
    Route::post('/update/{goods_type}', [GoodsTypeController::class, 'update'])->name('goodstype.update');
    Route::delete('/delete/{goods_type}', [GoodsTypeController::class, 'destroy'])->name('goodstype.delete');
    Route::post('/update-status', [GoodsTypeController::class, 'updateStatus'])->name('goodstype.updateStatus');
});


//banner image
Route::group(['prefix' => 'banner-image'], function () {
    Route::get('/', [BannerImageController::class, 'index'])->name('bannerimage.index');
    Route::get('/create', [BannerImageController::class, 'create'])->name('bannerimage.create');
    Route::get('/list', [BannerImageController::class, 'list'])->name('bannerimage.list');
    Route::post('/update/{bannerimage}', [BannerImageController::class, 'update'])->name('bannerimage.update');
    Route::post('/store', [BannerImageController::class, 'store'])->name('bannerimage.store');
    Route::get('/edit/{id}', [BannerImageController::class, 'edit'])->name('bannerimage.edit');    
    Route::delete('/delete/{bannerimage}', [BannerImageController::class, 'destroy'])->name('bannerimage.delete');    
    Route::post('/update-status', [BannerImageController::class, 'updateStatus'])->name('bannerimage.updateStatus');
});


//owner needed document
Route::group(['prefix' => 'owner-needed-documents'], function () {
    Route::get('/', [OwnerManagementController::class, 'ownerNeededDocumentIndex'])->name('ownerneeddocuments.index');
    Route::middleware('remove_empty_query')->get('/list', [OwnerManagementController::class, 'ownerNeededDocumentList'])->name('ownerneeddocuments.list');
    Route::get('/create', [OwnerManagementController::class, 'ownerNeededDocumentCreate'])->name('ownerneeddocuments.create');
    Route::post('/store', [OwnerManagementController::class, 'ownerNeededDocumentStore'])->name('ownerneeddocuments.store');
    Route::get('/edit/{document}', [OwnerManagementController::class, 'ownerNeededDocumentEdit'])->name('ownerneeddocuments.edit');
    Route::post('/update/{document}', [OwnerManagementController::class, 'ownerNeededDocumentUpdate'])->name('ownerneeddocuments.update');
    Route::post('/toggle', [OwnerManagementController::class, 'ownerNeededDocumentToggle'])->name('ownerneeddocuments.updatestatus');
    Route::delete('/delete/{document}', [OwnerManagementController::class, 'ownerNeededDocumentDelete'])->name('ownerneeddocuments.delete');
});


//onboarding screen
Route::group(['prefix' => 'onboarding-screen'], function () {
    Route::get('/', [OnboardingScreenController::class, 'index'])->name('onboardingscreen.index');
    Route::middleware('remove_empty_query')->get('/list', [OnboardingScreenController::class, 'list'])->name('onboardingscreen.list');
    // Route::get('/editPage', [OnboardingScreenController::class, 'editpage'])->name('onboardingscreen.editpage');
    Route::get('/edit/{id}', [OnboardingScreenController::class, 'edit'])->name('onboardingscreen.edit');
    Route::post('/update/{onboarding}', [OnboardingScreenController::class, 'update'])->name('onboardingscreen.update');
    Route::post('/update-status', [OnboardingScreenController::class, 'updateStatus'])->name('onboardingscreen.updateStatus');
});

//invoice configuration
Route::group(['prefix' => 'invoice-configuration'], function () {
    Route::get('/', [InvoiceConfigurationController::class, 'index'])->name('invoiceconfiguration.index');
    Route::post('/update', [InvoiceConfigurationController::class, 'update']);
});


// notification channel
Route::group(['prefix' => 'notification-channel'], function () {
    Route::get('/', [NotificationChannelController::class, 'index'])->name('notificationchannel.index');
    Route::middleware('remove_empty_query')->get('/list', [NotificationChannelController::class, 'list'])->name('notificationchannel.list');
    Route::get('/edit/{id}', [NotificationChannelController::class, 'edit'])->name('notificationchannel.edit');
    Route::get('/user-invoice-edit/{id}', [NotificationChannelController::class, 'userInvoiceEdit'])->name('notificationchannel.userInvoiceEdit');
    Route::get('/driver-invoice-edit/{id}', [NotificationChannelController::class, 'driverInvoiceEdit'])->name('notificationchannel.driverInvoiceEdit');
    Route::post('/update/{notification}', [NotificationChannelController::class, 'update']);
    Route::post('/update-push-template/{notification}', [NotificationChannelController::class, 'updatePushTemplate']);
    Route::post('/update-status', [NotificationChannelController::class, 'updateStatus'])->name('notificationchannel.updateStatus');

    Route::get('/push-template/edit/{id}', [NotificationChannelController::class, 'editPushTemplate'])->name('notificationchannel.editPushTemplate');
});

//map settings
Route::group(['prefix' => 'map-setting'], function () {
    Route::get('/', [MapsettingController::class, 'index'])->name('mapsettings.index');
    Route::post('/update', [MapsettingController::class, 'update'])->name('mapsettings.update');

});


//general settings
Route::group(['prefix' => 'general-settings'], function () {
    Route::get('/', [SettingController::class, 'generalSettings'])->name('settings.generalSettings');
    Route::post('/update', [SettingController::class, 'updateGeneralSettings'])->name('settings.updateGeneralSettings');
    Route::post('/update-status', [SettingController::class, 'updateStatus'])->name('settings.updateStatus');

});

//customization settings
Route::group(['prefix' => 'customization-settings'], function () {
    Route::get('/', [SettingController::class, 'customizationSettings'])->name('settings.customizationSettings');
    Route::post('/update', [SettingController::class, 'updateCustomizationSettings'])->name('settings.updateCustomizationSettings');
    Route::post('/update-status', [SettingController::class, 'updateCustomizationStatus'])->name('settings.updateCustomizationStatus');

});

//Peak Zone Setting
Route::group(['prefix' => 'peakzone-setting'], function () {
    Route::get('/', [SettingController::class, 'peakZoneSettings'])->name('settings.peakZoneSettings');
    Route::post('/update', [SettingController::class, 'updatePeakZoneSettings'])->name('settings.updatePeakZoneSettings');

});

// transportRide Settings
Route::group(['prefix' => 'transport-ride-settings'], function () {
    Route::get('/', [SettingController::class, 'transportRideSettings'])->name('settings.transportRideSettings');
    Route::post('/update', [SettingController::class, 'updateTransportSettings'])->name('settings.updateTransportSettings');
    Route::post('/update-status', [SettingController::class, 'updateTransportStatus'])->name('settings.updateTransportStatus');
});

// transportRide Settings
Route::group(['prefix' => 'bid-ride-settings'], function () {
    Route::get('/', [SettingController::class, 'bidRideSettings'])->name('settings.bidRideSettings');
    Route::post('/update', [SettingController::class, 'updateBidSettings'])->name('settings.updateBidSettings');
});

// wallet Settings
Route::group(['prefix' => 'wallet-settings'], function () {
    Route::get('/', [SettingController::class, 'walletSettings'])->name('settings.walletSettings');
    Route::post('/update', [SettingController::class, 'updateWalletSettings'])->name('settings.updateWalletSettings');

});

// tip Settings
Route::group(['prefix' => 'tip-settings'], function () {
    Route::get('/', [SettingController::class, 'tipSettings'])->name('settings.tipSettings');
    Route::post('/update', [SettingController::class, 'updateTipSettings'])->name('settings.updateTipSettings');
    Route::post('/update-status', [SettingController::class, 'updateTipStatus'])->name('settings.updateTipStatus');

});

// referral Settings
Route::group(['prefix' => 'referral-settings'], function () {
    Route::get('/', [SettingController::class, 'referralSettings'])->name('settings.referralSettings');
    Route::post('/update', [SettingController::class, 'updateReferralSettings'])->name('settings.updateRefrerralSettings');
    Route::post('/toggle', [SettingController::class, 'updateReferralToggle']);

});


//  trip ride  request
Route::group(['prefix' => 'rides-request'], function () {
    Route::get('/', [TripRequestController::class, 'ridesRequest'])->name('triprequest.ridesRequest');
    Route::middleware('remove_empty_query')->get('/list', [TripRequestController::class, 'list'])->name('triprequest.list');
    Route::post('/driver/{driver}', [TripRequestController::class, 'driverFind'])->name('triprequest.driverFind');
    Route::get('/view/{requestmodel}', [TripRequestController::class, 'viewDetails'])->name('triprequest.viewDetails');
    Route::get('/cancel/{requestmodel}', [TripRequestController::class, 'cancelRide'])->name('triprequest.cancel');
    Route::get('/detail/{request}', [TripRequestController::class, 'sosDetail'])->name('triprequest.sosDetail');
    Route::get('/download-invoice/{requestmodel}', [TripRequestController::class, 'downloadInvoice']);
    
    // send invoice mail
    Route::post('/send-invoice-mail/{requestmodel}', [TripRequestController::class, 'sendInvoicemail']);
});


// trip scheduled rides
Route::group(['prefix' => 'scheduled-rides'], function () {
    Route::get('/', [TripRequestController::class, 'scheduledRides'])->name('triprequest.scheduledRides');
});

// trip outstation rides
Route::group(['prefix' => 'out-station-rides'], function () {
    Route::get('/', [TripRequestController::class, 'outstationRides'])->name('triprequest.outstationRides');
});

// trip cancellation rides
Route::group(['prefix' => 'cancellation-rides'], function () {
    Route::get('/', [TripRequestController::class, 'cancellationRides'])->name('triprequest.cancellationRides');
    Route::get('/view', [TripRequestController::class, 'cancellationRidesViewDetails'])->name('triprequest.cancellationRidesViewDetails');
});


//  delivery ride request
Route::group(['prefix' => 'delivery-rides-request'], function () {
    Route::get('/', [DeliveryRequestController::class, 'ridesRequest'])->name('deliveryTriprequest.ridesRequest');
    Route::middleware('remove_empty_query')->get('/list', [DeliveryRequestController::class, 'list'])->name('deliveryTriprequest.list');
    Route::post('/driver/{driver}', [DeliveryRequestController::class, 'driverFind'])->name('deliveryTriprequest.driverFind');
    Route::get('/view/{requestmodel}', [DeliveryRequestController::class, 'viewDetails'])->name('deliveryTriprequest.viewDetails');
    Route::get('/cancel/{requestmodel}', [DeliveryRequestController::class, 'cancelRide'])->name('deliveryTriprequest.cancel');
    Route::get('/download-invoice/{requestmodel}', [DeliveryRequestController::class, 'downloadInvoice']);
});



//  delivery scheduled rides
Route::group(['prefix' => 'delivery-scheduled-rides'], function () {
    Route::get('/', [DeliveryRequestController::class, 'scheduledRides'])->name('delivery-scheduled-rides.scheduledRides');
    Route::get('/view', [DeliveryRequestController::class, 'scheduledRidesViewDetails'])->name('delivery-scheduled-rides.viewDetails');
});


//  delivery cancellation rides
Route::group(['prefix' => 'delivery-cancellation-rides'], function () {
    Route::get('/', [DeliveryRequestController::class, 'cancellationRides'])->name('deliveryrequest.cancellationRides');
    Route::get('/view', [DeliveryRequestController::class, 'cancellationRidesViewDetails'])->name('deliveryrequest.viewCancelDetails');
});


// ongoing rides
Route::group(['prefix' => 'ongoing-rides'], function () {
    Route::get('/', [TripRequestController::class, 'ongoingRidesRequest'])->name('triprequest.ongoingRides');
    Route::get('/find-ride/{request}', [TripRequestController::class, 'ongoingRideDetail'])->name('triprequest.ongoingRideDetail');
    Route::get('/assign/{request}', [TripRequestController::class, 'assignView'])->name('triprequest.assignView');
    Route::post('/assign-driver/{requestmodel}', [TripRequestController::class, 'assignDriver'])->name('triprequest.assignDriver');
});




 





// image
Route::group(['prefix' => 'images'], function () {
    Route::get('/', [imageController::class, 'index'])->name('images.index');
    Route::get('/create', [imageController::class, 'create'])->name('images.create');
    Route::get('/update', [imageController::class, 'update'])->name('images.update');
});

// user
    Route::group(['prefix' => 'users'], function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::middleware('remove_empty_query')->get('/list', [UserController::class, 'list'])->name('users.list');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/update/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/password/edit/{id}', [UserController::class, 'editPassword'])->name('users.password.edit');
    Route::post('/password/update/{user}', [UserController::class, 'updatePasswords'])->name('users.password.update');
    Route::get('/check-mobile/{mobile}', [UserController::class, 'checkMobileExists']);
    Route::get('/check-email/{email}', [UserController::class, 'checkEmailExists']);
    Route::get('/check-mobile/{mobile}/{id}', [UserController::class, 'checkMobileExists'])->name('users.checkMobileExists');
    Route::get('/check-email/{email}/{id}', [UserController::class, 'checkEmailExists'])->name('users.checkEmailExists');

    Route::post('/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::get('/view-profile/{user}', [UserController::class, 'viewProfile'])->name('users.view-profile');
    Route::delete('/delete/{user}', [UserController::class, 'destroy']);
    // wallet-history/list
    Route::post('/wallet-add-amount/{user}', [UserController::class, 'walletAddAmount'])->name('users.addAmount');

    Route::get('/wallet-history/list/{user}', [UserController::class, 'walletHistoryList'])->name('users.walletHistoryList');

    Route::get('/request/list/{user}', [UserController::class, 'requestList'])->name('users.requestList');

    Route::get('/rating-list/{user}', [UserController::class, 'ratinghistory'])->name('users.ratinghistory');

    Route::get('/deleted-user', [UserController::class, 'deletedUser'])->name('users.deleted-users');
    Route::get('/deletedList', [UserController::class, 'deletedList'])->name('users.deletedList');
    // Route::get('/user-dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');
    Route::patch('/restore/{id}', [UserController::class, 'restoreUser'])->name('users.restore');

});


// vehicle model
Route::group(['prefix' => 'zones'], function () {
    Route::get('/', [ZoneController::class, 'index'])->name('zone.index');
    Route::get('/create', [ZoneController::class, 'create'])->name('zone.create');
    Route::post('/store', [ZoneController::class, 'store'])->name('zone.store');
    Route::get('/fetch', [ZoneController::class, 'fetch'])->name('zone.fetch');//table fetch
    Route::middleware('remove_empty_query')->get('/list', [ZoneController::class, 'list'])->name('zone.list');//service location list
    Route::get('/edit/{id}', [ZoneController::class, 'edit'])->name('zone.edit');
    Route::post('/update/{zone}', [ZoneController::class, 'update'])->name('zone.update');
    Route::put('/isactive/{id}', [ZoneController::class, 'isActive']);
    Route::delete('/delete/{zone}', [ZoneController::class, 'destroy']);
    Route::post('/update-status', [ZoneController::class, 'updateStatus'])->name('zone.updateStatus');
    Route::get('/map/{id}', [ZoneController::class, 'map'])->name('zone.map');


});

// vehicle model
Route::group(['prefix' => 'incentives'], function () {

    Route::get('/{zoneType}', [IncentiveController::class, 'index'])->name('incentives.index');
    Route::post('/update', [IncentiveController::class, 'update'])->name('incentives.update');

});



    
   
    Route::post('/set-locale', function (Request $request) {session(['selectedLocale' => $request->locale]);
        return response()->json(['status' => 'success']);
    });

// chat template
Route::group(['prefix' => 'chat'], function () {
    Route::get('/', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/fetch-user', [ChatController::class, 'fetchUser'])->name('chat.fetchUser');
    Route::get('/messages/{conversationId}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/createChat', [ChatController::class, 'createChat'])->name('chat.createChat');
    Route::post('/send-admin', [ChatController::class, 'sendAdmin'])->name('chat.sendAdmin');
    Route::post('/close-chat', [ChatController::class, 'closeChat'])->name('chat.closeChat');
    Route::get('/fetchChat', [ChatController::class, 'fetchChats'])->name('chat.fetchChats');
    Route::get('/readAll', [ChatController::class, 'readAll'])->name('chat.readAll');
    Route::get('/search-user', [ChatController::class, 'searchUser'])->name('chat.searchUser');
    Route::get('/verify-chat', [ChatController::class, 'verifyChat'])->name('chat.verifyChat');


});

// support ticket category
Route::group(['prefix' => 'category'], function () {
    Route::get('/', [SupportTicketCategoryController::class, 'index'])->name('category.index');
    Route::middleware('remove_empty_query')->get('/list', [SupportTicketCategoryController::class, 'list'])->name('category.list');
    Route::get('/create', [SupportTicketCategoryController::class, 'create'])->name('category.create');
    Route::post('/store', [SupportTicketCategoryController::class, 'store'])->name('category.store');
    Route::get('/edit/{id}', [SupportTicketCategoryController::class, 'edit'])->name('category.edit');
    Route::post('/update/{category}', [SupportTicketCategoryController::class, 'update'])->name('category.update');
    Route::delete('/delete/{category}', [SupportTicketCategoryController::class, 'destroy']);
});


// support ticket title
Route::group(['prefix' => 'title'], function () {
    Route::get('/', [SupportTicketTitleController::class, 'index'])->name('title.index');
    Route::middleware('remove_empty_query')->get('/list', [SupportTicketTitleController::class, 'list'])->name('title.list');
    Route::get('/create', [SupportTicketTitleController::class, 'create'])->name('title.create');
    Route::post('/store', [SupportTicketTitleController::class, 'store'])->name('title.store');
    Route::get('/edit/{id}', [SupportTicketTitleController::class, 'edit'])->name('title.edit');
    Route::post('/update/{title}', [SupportTicketTitleController::class, 'update'])->name('title.update');
    Route::delete('/delete/{title}', [SupportTicketTitleController::class, 'destroy']);
    Route::post('/update-status', [SupportTicketTitleController::class, 'updateStatus'])->name('title.updateStatus');
});

// support ticket list
Route::group(['prefix' => 'support-tickets'], function () {
    Route::get('/', [SupportTicketsListController::class, 'index'])->name('support-tickets.index');
    Route::middleware('remove_empty_query')->get('/list', [SupportTicketsListController::class, 'list'])->name('support-tickets.list');
    Route::post('/update/{support_ticket}', [SupportTicketsListController::class, 'updateAssingTO'])->name('support-tickets.updateAssingTO');
    Route::get('/ticket-counts', [SupportTicketsListController::class, 'getTicketCounts']);
    Route::middleware('remove_empty_query')->get('/individual_list', [SupportTicketsListController::class, 'individualList'])->name('support-tickets.individualList');
    Route::get('/view-details/{supportTicket}', [SupportTicketsListController::class, 'viewTicketDetails'])->name('ticket.viewDetails_');
    Route::post('/ticket/reply/{supportTicket}', [SupportTicketsListController::class, 'replyMessage'])->name('ticket.replyMessage_');
    Route::post('/update-status', [SupportTicketsListController::class, 'updateStatus'])->name('ticket.updateStatus');
});

});
    // Track Request
    Route::get('/track/request/{request}', [TripRequestController::class, 'trackRequest'])->name('triprequest.trackRequest');

// landing template
// Route::group(['prefix' => 'landing'], function () {
    Route::get('/', [LandingHomeController::class, 'homepage'])->name('landing.index');
    Route::get('/driver', [LandingDriverController::class, 'driverpage'])->name('landing.driver');
    Route::get('/aboutus', [LandingAboutsController::class, 'aboutuspage'])->name('landing.aboutus');
    Route::get('/user', [LandingUserController::class, 'userpage'])->name('landing.user');
    Route::get('/contact', [LandingContactController::class, 'contactpage'])->name('landing.contact');
    Route::get('/privacy', [LandingQuickLinkController::class, 'privacypage'])->name('landing.privacy');
    Route::get('/compliance', [LandingQuickLinkController::class, 'compliancepage'])->name('landing.compliance');
    Route::get('/terms', [LandingQuickLinkController::class, 'termspage'])->name('landing.terms');
    Route::get('/dmv', [LandingQuickLinkController::class, 'dmvpage'])->name('landing.dmv');


    Route::get('/mi-login', [DashBoardController::class, 'overRideIndex'])->name('overrideloginToDashboard');

Route::middleware('auth')->group(function () {
    Route::get('/create-booking', [UserWebBookingController::class, 'booking'])->name('web-booking.create-booking');
    Route::post('/logout', [UserWebBookingController::class, 'logout'])->name('logout');
    Route::get('/profile', [UserWebBookingController::class, 'profile'])->name('web-booking.profile');
    Route::post('/user/update-profile', [UserWebBookingController::class, 'updateProfile'])->name('user.updateProfile');
    Route::get('/history', [UserWebBookingController::class, 'history'])->name('web-booking.history');
    Route::get('/history/view/{requestmodel}', [UserWebBookingController::class, 'viewDetails'])->name('history.viewDetails');
    Route::middleware('remove_empty_query')->get('/webuser/list', [UserWebBookingController::class, 'list'])->name('web-users.list');
    Route::middleware('remove_empty_query')->get('/create-booking', [UserWebBookingController::class, 'booking'])->name('web-booking.create-booking');
    Route::post('/web-create-request',[UserWebBookingController::class,'createRequest']);

    Route::get('/get-support', [UserWebBookingController::class, 'getSupport'])->name('web-booking.getSupport');
    Route::middleware('remove_empty_query')->get('/get-support/list', [UserWebBookingController::class, 'supportList'])->name('web-users.supportList');
    Route::get('/create-ticket', [UserWebBookingController::class, 'createTicket'])->name('ticket.createTicket');
    Route::post('/ticket/store', [UserWebBookingController::class, 'store'])->name('ticket.store');
    Route::get('/ticket/view/{supportTicket}', [UserWebBookingController::class, 'viewTicketDetails'])->name('ticket.viewDetails');
    Route::post('/ticket/reply/{supportTicket}', [UserWebBookingController::class, 'replyMessage'])->name('ticket.replyMessage');
    Route::get('/fetch-user', [UserWebBookingController::class, 'fetchUser'])->name('chat.fetchUser_');

});

// landingsite-homepage 
Route::group(['prefix' => 'landing-home'], function () {
    Route::get('/', [LandingHomeController::class, 'index'])->name('landing_home.index');
    Route::middleware('remove_empty_query')->get('/list', [LandingHomeController::class, 'list'])->name('landing_home.list');
    Route::get('/create', [LandingHomeController::class, 'create'])->name('landing_home.create');
    Route::post('/store', [LandingHomeController::class, 'store'])->name('landing_home.store');
    Route::get('/edit/{id}', [LandingHomeController::class, 'edit'])->name('landing_home.edit');
    Route::post('/update/{landingHome}', [LandingHomeController::class, 'update'])->name('landing_home.update');
    Route::delete('/delete/{landingHome}', [LandingHomeController::class, 'destroy'])->name('landing_home.delete');
});


// landingsite-aboutus 
Route::group(['prefix' => 'landing-aboutus'], function () {
    Route::get('/', [LandingAboutsController::class, 'index'])->name('landing_abouts.index');
    Route::middleware('remove_empty_query')->get('/list', [LandingAboutsController::class, 'list'])->name('landing_abouts.list');
    Route::get('/create', [LandingAboutsController::class, 'create'])->name('landing_abouts.create');
    Route::post('/store', [LandingAboutsController::class, 'store'])->name('landing_abouts.store');
    Route::get('/edit/{id}', [LandingAboutsController::class, 'edit'])->name('landing_abouts.edit');
    Route::post('/update/{landingAbouts}', [LandingAboutsController::class, 'update'])->name('landing_abouts.update');
    Route::delete('/delete/{landingAbouts}', [LandingAboutsController::class, 'destroy'])->name('landing_abouts.delete');
});

Route::group(['prefix' => 'landing-driver'], function () {
    Route::get('/', [LandingDriverController::class, 'index'])->name('landing_driver.index');
    Route::middleware('remove_empty_query')->get('/list', [LandingDriverController::class, 'list'])->name('landing_driver.list');
    Route::get('/create', [LandingDriverController::class, 'create'])->name('landing_driver.create');
    Route::post('/store', [LandingDriverController::class, 'store'])->name('landing_driver.store');
    Route::get('/edit/{id}', [LandingDriverController::class, 'edit'])->name('landing_driver.edit');
    Route::post('/update/{landingDriver}', [LandingDriverController::class, 'update'])->name('landing_driver.update');
    Route::delete('/delete/{landingDriver}', [LandingDriverController::class, 'destroy'])->name('landing_driver.delete');
});

Route::group(['prefix' => 'landing-user'], function () {
    Route::get('/', [LandingUserController::class, 'index'])->name('landing_user.index');
    Route::middleware('remove_empty_query')->get('/list', [LandingUserController::class, 'list'])->name('landing_user.list');
    Route::get('/create', [LandingUserController::class, 'create'])->name('landing_user.create');
    Route::post('/store', [LandingUserController::class, 'store'])->name('landing_user.store');
    Route::get('/edit/{id}', [LandingUserController::class, 'edit'])->name('landing_user.edit');
    Route::post('/update/{landingUser}', [LandingUserController::class, 'update'])->name('landing_user.update');
    Route::delete('/delete/{landingUser}', [LandingUserController::class, 'destroy'])->name('landing_user.delete');
});

// landingsite-Header-Footer 
Route::group(['prefix' => 'landing-header'], function () {
    Route::get('/', [LandingHeaderController::class, 'home'])->name('landing_header.index');
    Route::get('/list', [LandingHeaderController::class, 'list'])->name('landing_header.list');
    Route::get('/create', [LandingHeaderController::class, 'create'])->name('landing_header.create');
    Route::post('/store', [LandingHeaderController::class, 'store'])->name('landing_header.store');
    Route::get('/edit/{id}', [LandingHeaderController::class, 'edit'])->name('landing_header.edit');
    Route::post('/update/{landingHeader}', [LandingHeaderController::class, 'update'])->name('landing_header.update');
    Route::delete('/delete/{landingHeader}', [LandingHeaderController::class, 'destroy'])->name('landing_header.delete');
    Route::get('/get-color-settings', [LandingHeaderController::class, 'getColorSettings']);
    Route::post('/update-color-settings', [LandingHeaderController::class, 'updateColorSettings']);
});

// landingsite-Quick Links 
Route::group(['prefix' => 'landing-quicklink'], function () {
    Route::get('/', [LandingQuickLinkController::class, 'index'])->name('landing_quicklink.index');
    Route::get('/list', [LandingQuickLinkController::class, 'list'])->name('landing_quicklink.list');
    Route::get('/create', [LandingQuickLinkController::class, 'create'])->name('landing_quicklink.create');
    Route::post('/store', [LandingQuickLinkController::class, 'store'])->name('landing_quicklink.store');
    Route::get('/edit/{id}', [LandingQuickLinkController::class, 'edit'])->name('landing_quicklink.edit');
    Route::post('/update/{landingQuickLink}', [LandingQuickLinkController::class, 'update'])->name('landing_quicklink.update');
    Route::delete('/delete/{landingQuickLink}', [LandingQuickLinkController::class, 'destroy'])->name('landing_quicklink.delete');
});

// landingsite-Contact 
Route::group(['prefix' => 'landing-contact'], function () {
    Route::get('/', [LandingContactController::class, 'index'])->name('landing_contact.index');
    Route::get('/list', [LandingContactController::class, 'list'])->name('landing_contact.list');
    Route::get('/create', [LandingContactController::class, 'create'])->name('landing_contact.create');
    Route::post('/store', [LandingContactController::class, 'store'])->name('landing_contact.store');
    Route::get('/edit/{id}', [LandingContactController::class, 'edit'])->name('landing_contact.edit');
    Route::post('/update/{landingContact}', [LandingContactController::class, 'update'])->name('landing_contact.update');
    Route::delete('/delete/{landingContact}', [LandingContactController::class, 'destroy'])->name('landing_contact.delete');

    Route::post('/contactmessage', [LandingContactController::class, 'contact_message'])->name('landing_contact.contactmessage');
});


//paymentGateways
//paypall
    // paypal?amount=100&payment_for=wallet&currency=USD&user_id=2&payment_for=wallet&request_id
    Route::get('paypal',  [PayPalController::class, 'index'])->name('paypal');
    Route::post('paypal/payment', [PayPalController::class, 'payment'])->name('paypal.payment');
    Route::get('paypal/payment/success', [PayPalController::class, 'paymentSuccess'])->name('paypal.payment.success');
    Route::get('paypal/payment/cancel', [PayPalController::class, 'paymentCancel'])->name('paypal.payment/cancel');
//stripe
    // stripe?amount=100&payment_for=wallet&currency=USD&user_id=2&payment_for=wallet&request_id
    Route::get('stripe', [StripeController::class, 'stripe'])->name('stripe');
    Route::post('stripe-checkout',  [StripeController::class, 'stripeCheckout'])->name('checkout.process');
    Route::get('stripe-checkout-success', [StripeController::class, 'stripeCheckoutSuccess'])->name('checkout.success');
    Route::get('stripe-checkout-error', [StripeController::class, 'stripeCheckoutError'])->name('checkout.failure');
//fluterwave
    // flutterwave?amount=100&payment_for=wallet&currency=USD&user_id=2&payment_for=wallet&request_id
    Route::get('flutterwave', [FlutterwaveController::class, 'index'])->name('flutterwave');
    Route::get('flutterwave/payment/success',  [FlutterwaveController::class, 'flutterwaveCheckout'])->name('flutterwave.success');

//cashfree   
    
    Route::get('cashfree', [CashfreeController::class,'create'])->name('cashfree');
    Route::post('cashfree/payments/store', [CashfreeController::class,'store'])->name('store');
    Route::any('cashfree/payments/success', [CashfreeController::class,'success'])->name('cashfree.success');

//paystack
    // paystack?amount=100&payment_for=wallet&currency=USD&user_id=2&payment_for=wallet&request_id
    Route::get('paystack', [PaystackController::class, 'index'])->name('paystack');
    Route::get('paystack/payment/success', [PaystackController::class, 'paystackCheckout'])->name('paystack.checkout');
//khalti
    Route::get('khalti', [KhaltiController::class,'index'])->name('khalti');
    Route::post('khalti/checkout', [KhaltiController::class,'khaltiCheckoutsuccess'])->name('khalti.success');
//razorpay
    Route::get('/razorpay', [RazorPayController::class,'razorpay'])->name('razorpay');
    Route::get('/payment-success', [RazorPayController::class,'razorpay_success'])->name('razorpay.success');
//mercadopago
    // mercadopago?amount=100&payment_for=wallet&currency=USD&user_id=2&payment_for=wallet&request_id
    Route::get('mercadopago', [MercadopagoController::class,'mercadepago'])->name('mercadopago');

    Route::get('mercadopago-pix', [MercadopagoController::class,'mercadopagoPix'])->name('mercadopago.pix');


    Route::get('mercadopago/payment/success', [MercadopagoController::class,'mercadopagoCheckout'])->name('mercadopago.success');

    Route::any('/webhook/mercadopago',[MercadopagoController::class,'mercadopagoPix'])->name('mercadopago.webhook');

//paytech
    // paytech?amount=100&payment_for=wallet&currency=USD&user_id=2&payment_for=wallet&request_id
    Route::get('paytech', [PaytechController::class, 'index'])->name('paytech');
    Route::post('/paytech/initiate', [PaytechController::class, 'initiatePayTech'])->name('paytech.initiate');
    Route::get('paytech/payment/success', [PaytechController::class, 'paytechCheckout'])->name('paytech.checkout');

// flexipay
    // paytech?amount=100&payment_for=wallet&currency=USD&user_id=2&payment_for=wallet&request_id
    Route::get('flexpaie', [FlexpaieController::class, 'index'])->name('flexpaie');
    Route::post('flexpaie/pay', [FlexpaieController::class, 'makePayment'])->name('flexpaie.pay');
    Route::get('flexpaie/payment/success', [FlexpaieController::class, 'flexpaieCheckout'])->name('flexpaie.checkout');

//ccavenue   Not completed
    // ccavenue?amount=100&payment_for=wallet&currency=USD&user_id=2&payment_for=wallet&request_id
    Route::get('ccavenue',  [CcavenueController::class,'index'])->name('ccavenue');
    Route::post('ccavenue/checkout', [CcavenueController::class,'ccavenueCheckout'])->name('ccavenue.checkout');
    Route::get('ccavenue/payment/success', [CcavenueController::class,'success'])->name('ccavenue.payment.response');
    Route::get('ccavenue/payment/failure', [CcavenueController::class,'failure'])->name('ccavenue.payment.cancel');


//payphone
    Route::group(['prefix'=> 'payphone'],function() {
        Route::get('/', [PayphoneController::class,'index'])->name('payphone.checkout');
        Route::get('/payment-success', [PayphoneController::class,'payphoneCheckout'])->name('payphone.success');
    });

//easypaisa
    Route::group(['prefix'=> 'easypaisa'],function() {
        Route::get('/', [EasypaisaController::class,'index'])->name('easypaisa.checkout');
        Route::get('/payment-success', [EasypaisaController::class,'easypaisaCheckout'])->name('easypaisa.success');
    });

    Route::view("success",'success');
    Route::view("failure",'failure')->name('failure');
    Route::view("pending",'pending');

    Route::get('/profile-edit',  [UserController::class,'profileEdit']);
    // update-profile
    Route::post('/update-profile',  [UserController::class,'updateProfile']);
    Route::post('/update-password',  [UserController::class,'updatePassword']);


    Route::middleware('guest')->get('/mi-admin',  [LoginController::class,'adminLogin'])->name('login.admin');

    Route::group(['prefix'=>'login','middleware'=>'redirect_dynamic_login'],function(){

        Route::middleware('guest')->get('/{redirect}',  [LoginController::class,'dynamicLoginUrl'])->name('login.{redirect}');

    });


       
   

 


    Route::get('/status', function() {
        return response()->json(['status' => 'Application is running']);
    });

    Route::get('/healthcheck', function() {
        return response()->json(['status' => 'Healthy']);
    });

       Route::any('/{page?}', function () {
        return Inertia::render('pages/404'); // Assuming you have a Vue component for 404 at `resources/js/Pages/pages/404.vue`
    })->where('page', '.*');

