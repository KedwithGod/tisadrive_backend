<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\XenditController;

// Route::get('/xendit/checkout', function () {
//     return view('xendit.checkout');
// });

Route::get('/xendit',[XenditController::class, 'index'])->name('xendit');

Route::post('/xendit/create-invoice', [XenditController::class, 'createInvoice'])->name('xendit.create.invoice');

Route::any('/xendit-success', [XenditController::class, 'invoiceCallback'])->name('xendit.callback');

