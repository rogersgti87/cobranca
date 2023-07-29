<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\ServiceController;

use App\Http\Controllers\front\HomeController;
use App\Http\Controllers\front\ContactController;


Route::get('/',[HomeController::class, 'index']);
Route::get('/contact',[ContactController::class,'index']);

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});


Route::group(['prefix' => 'admin','middleware' => ['auth']], function(){

    Route::get('/',[AdminController::class,'index']);

    Route::get('users',[UserController::class,'index']);
    Route::get('users/form',[UserController::class,'form']);
    Route::post('users',[UserController::class,'store']);
    Route::put('users/{id}',[UserController::class,'update']);
    Route::post('users/copy',[UserController::class,'copy']);
    Route::delete('users',[UserController::class,'destroy']);

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

});


   require __DIR__.'/auth.php';


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
