<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Public routes
Route::get('me', [MeController::class,'getMe']);

// Routes for authenticated users only
Route::middleware(['auth:api'])->group(function(){
    Route::post('logout',[LoginController::class, 'logout']);

});


// Routes for guests only
Route::middleware(['guest:api'])->group(function(){
    Route::post('register',[RegisterController::class, 'register']);
    Route::post('verification/verify/{user}',[VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('verification/resend',[VerificationController::class, 'resend']);

    Route::post('login',[LoginController::class, 'login']);

    //reset password by sending a link to the user's email
    Route::post('password/email',[ForgotPasswordController::class, 'sendResetLinkEmail']);
    //form for resetting the password (link from email will redirect to this route)
    Route::post('password/reset',[ResetPasswordController::class, 'reset']);

});