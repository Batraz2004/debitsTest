<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('registration', [RegistrationController::class, 'createUser']);
Route::post('login', [LoginController::class, 'login']);

Route::prefix('account')->middleware('auth:sanctum')->group(function(){
    Route::post('deposit',[AccountController::class,'createDeposit']);
    Route::get('balance/{user_id}',[AccountController::class,'getBalance']);
    Route::post('withdraw',[AccountController::class,'withdraw']);
    Route::post('transfer',[AccountController::class,'createTransfer']);
});

