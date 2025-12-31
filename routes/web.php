<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\CustomerServiceController;
use App\Http\Controllers\admin\InvoiceController;
use App\Http\Controllers\admin\PayableController;
use App\Http\Controllers\admin\PayableCategoryController;
use App\Http\Controllers\admin\SupplierController;
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
    Route::any('asaas', function(\Illuminate\Http\Request $request) {
        if ($request->isMethod('get')) {
            return response()->json(['message' => 'Esta rota aceita apenas requisições POST'], 200);
        }
        return app(WebHookController::class)->Asaas($request);
    });
    Route::post('whatsapp/{user_id}', [WebHookController::class,'whatsapp']);
    Route::get('teste', [WebHookController::class,'teste']);
});


Route::group(['prefix' => 'admin','middleware' => ['auth']], function(){

    Route::get('/',[AdminController::class,'index']);
    Route::get('chart-invoices',[AdminController::class,'chartinvoices']);
    Route::get('chart-payables',[AdminController::class,'chartPayables']);

    Route::get('users',[UserController::class,'index']);
    Route::get('users/form',[UserController::class,'form']);
    Route::post('users',[UserController::class,'store']);
    Route::post('users/{id}',[UserController::class,'update']);
    Route::delete('users',[UserController::class,'destroy']);
    Route::post('users-whatsapp',[UserController::class,'createWhatsapp']);
    Route::get('users-whatsapp',[UserController::class,'loadwhatsapp']);
    Route::get('users-whatsapp-status',[UserController::class,'statusWhatsapp']);
    Route::get('users-whatsapp-qrcode',[UserController::class,'qrcodeWhatsapp']);
    Route::get('users-whatsapp-logout',[UserController::class,'logoutWhatsapp']);
    Route::get('users-whatsapp-delete',[UserController::class,'deleteWhatsapp']);

    Route::post('user-inter',[UserController::class,'inter']);
    Route::post('user-ph',[UserController::class,'ph']);
    Route::post('user-mp',[UserController::class,'mp']);
    Route::post('user-asaas',[UserController::class,'asaas']);

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
    Route::post('invoices/{id}',[InvoiceController::class,'update']);
    Route::post('invoices/copy',[InvoiceController::class,'copy']);
    Route::delete('invoices/{id}',[InvoiceController::class,'destroy']);
    Route::get('load-invoices/{invoice_id}',[InvoiceController::class,'load']);
    Route::get('load-invoice-notifications/{invoice_id}',[InvoiceController::class,'loadnotifications']);
    Route::get('invoices-check-status/{invoice_id}',[InvoiceController::class,'checkStatus']);
    Route::get('load-invoices',[InvoiceController::class,'loadinvoices']);
    Route::get('load-invoice-error',[InvoiceController::class,'loadinvoiceerror']);
    Route::post('invoice-notificate/{invoice_id}',[InvoiceController::class,'invoiceNotificate']);
    Route::get('invoice-error/{id}',[InvoiceController::class,'error']);


    Route::get('payables',[PayableController::class,'index']);
    Route::get('payables/form',[PayableController::class,'form']);
    Route::post('payables',[PayableController::class,'store']);
    Route::put('payables/{id}',[PayableController::class,'update']);
    Route::post('payables/copy',[PayableController::class,'copy']);
    Route::delete('payables/{id}',[PayableController::class,'destroy']);
    Route::get('load-payables',[PayableController::class,'loadPayables']);

    Route::get('payable-categories',[PayableCategoryController::class,'index']);
    Route::get('payable-categories/form',[PayableCategoryController::class,'form']);
    Route::post('payable-categories',[PayableCategoryController::class,'store']);
    Route::put('payable-categories/{id}',[PayableCategoryController::class,'update']);
    Route::delete('payable-categories',[PayableCategoryController::class,'destroy']);

    Route::get('suppliers',[SupplierController::class,'index']);
    Route::get('suppliers/form',[SupplierController::class,'form']);
    Route::post('suppliers',[SupplierController::class,'store']);
    Route::put('suppliers/{id}',[SupplierController::class,'update']);
    Route::post('suppliers/copy',[SupplierController::class,'copy']);
    Route::delete('suppliers',[SupplierController::class,'destroy']);
    Route::get('load-suppliers',[SupplierController::class,'loadSuppliers']);


    Route::get('logs',[LogController::class,'index']);
    Route::get('logs-list',[LogController::class,'list']);
    Route::get('logs/{id}',[LogController::class,'getlog']);


});


   require __DIR__.'/auth.php';


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
