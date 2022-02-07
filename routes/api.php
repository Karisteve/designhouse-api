<?php

use App\Http\Controllers\MeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

//Route group for public users
Route::get('me',[MeController::class ,'getMe']);


//Route group for authenticated users only
Route::group(['middleware'=>['auth:api']],function()
    {
Route::get('greetings', function ()
{
    return "hello";
});
Route::post('logout', [LoginController::class, 'logout']);
Route::put('settings/profile', [SettingsController::class, 'updateProfile']);
Route::put('settings/password', [SettingsController::class, 'updatePassword']);

    }
);

//Route group for guests only
Route::group(['middleware'=>['guest:api']],function()
{

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [RegisterController::class, 'register']);
Route::post('verification/verify/{user}',[VerificationController::class ,'verify'])->name('verification.verify');
Route::post('verification/resend', [VerificationController::class, 'resend'])->name('verification.resend');
Route::post('password/email',[ForgotPasswordController::class,'sendResetLinkEmail']);
Route::post('password/reset',[ResetPasswordController::class,'reset']);

});


