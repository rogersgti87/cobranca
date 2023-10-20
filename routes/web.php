<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\CustomerServiceController;
use App\Http\Controllers\admin\InvoiceController;
use App\Http\Controllers\admin\LogController;


use App\Http\Controllers\front\HomeController;
use App\Http\Controllers\front\ContactController;

use App\Http\Controllers\WebHookController;

Route::get('/',[HomeController::class, 'index']);
Route::get('/contact',[ContactController::class,'index']);

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::prefix('webhook')->group(function () {
    Route::post('email', [WebHookController::class,'email']);
    Route::post('paghiper', [WebHookController::class,'paghiper']);
    Route::post('mercadopago', [WebHookController::class,'mercadopago']);
    Route::post('intermediumbillet', [WebHookController::class,'intermediumbillet']);
    Route::post('intermediumbilletpix', [WebHookController::class,'intermediumbilletpix']);
    Route::post('intermediumpix', [WebHookController::class,'intermediumpix']);
    Route::post('whatsapp-messages', [WebHookController::class,'whatsappmessage']);
    Route::get('teste', [WebHookController::class,'teste']);
});


Route::group(['prefix' => 'admin','middleware' => ['auth']], function(){

    Route::get('/',[AdminController::class,'index']);
    Route::get('chart-invoices',[AdminController::class,'chartinvoices']);

    Route::get('users',[UserController::class,'index']);
    Route::get('users/form',[UserController::class,'form']);
    Route::post('users',[UserController::class,'store']);
    Route::post('users/{id}',[UserController::class,'update']);
    Route::delete('users',[UserController::class,'destroy']);
    Route::get('users/getsession',[UserController::class,'getSession']);
    Route::get('users/getqrcode',[UserController::class,'getQRCode']);
    Route::post('user-default-whatsapp/{access_token}',[UserController::class,'defaultWhatsapp']);
    Route::post('user-inter',[UserController::class,'inter']);
    Route::post('user-ph',[UserController::class,'ph']);
    Route::post('user-mp',[UserController::class,'mp']);

    Route::get('settings',[SettingController::class,'form']);
    Route::put('settings',[SettingController::class,'update']);



    Route::get('customers',[CustomerController::class,'index']);
    Route::get('customers/form',[CustomerController::class,'form']);
    Route::post('customers',[CustomerController::class,'store']);
    Route::put('customers/{id}',[CustomerController::class,'update']);
    Route::post('customers/copy',[CustomerController::class,'copy']);
    Route::delete('customers',[CustomerController::class,'destroy']);


    Route::get('services',[ServiceController::class,'index']);
    Route::get('services/form',[ServiceController::class,'form']);
    Route::post('services',[ServiceController::class,'store']);
    Route::put('services/{id}',[ServiceController::class,'update']);
    Route::post('services/copy',[ServiceController::class,'copy']);
    Route::delete('services',[ServiceController::class,'destroy']);

    Route::get('customer-services',[CustomerServiceController::class,'index']);
    Route::get('customer-services/form',[CustomerServiceController::class,'form']);
    Route::post('customer-services',[CustomerServiceController::class,'store']);
    Route::put('customer-services/{id}',[CustomerServiceController::class,'update']);
    Route::post('customer-services/copy',[CustomerServiceController::class,'copy']);
    Route::delete('customer-services/{id}',[CustomerServiceController::class,'destroy']);
    Route::get('load-customer-services/{customer_id}',[CustomerServiceController::class,'load']);

    Route::get('invoices',[InvoiceController::class,'index']);
    Route::get('invoices/form',[InvoiceController::class,'form']);
    Route::post('invoices',[InvoiceController::class,'store']);
    Route::put('invoices/{id}',[InvoiceController::class,'update']);
    Route::post('invoices/copy',[InvoiceController::class,'copy']);
    Route::delete('invoices/{id}',[InvoiceController::class,'destroy']);
    Route::get('load-invoices/{invoice_id}',[InvoiceController::class,'load']);
    Route::get('load-invoice-notifications/{invoice_id}',[InvoiceController::class,'loadnotifications']);
    Route::get('invoices-check-status/{invoice_id}',[InvoiceController::class,'checkStatus']);
    Route::get('load-invoices',[InvoiceController::class,'loadinvoices']);
    Route::post('invoice-notificate/{invoice_id}',[InvoiceController::class,'invoiceNotificate']);
    Route::get('invoice-error/{id}',[InvoiceController::class,'error']);


    Route::get('logs',[LogController::class,'index']);
    Route::get('logs-list',[LogController::class,'list']);
    Route::get('logs/{id}',[LogController::class,'getlog']);


});


   require __DIR__.'/auth.php';


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
