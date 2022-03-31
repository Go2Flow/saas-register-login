<?php

use Go2Flow\SaasRegisterLogin\Http\Controllers\API\PermissionController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\ReferralController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\UserController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\ValidationController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\WorldCountryCurrencyController;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Go2Flow\SaasRegisterLogin\Http\Controllers',
    'prefix' => 'api'
], function () {

    Route::group(['middleware' => ['api'], 'prefix' => 'srl'], function () {

        Route::post('login', [UserController::class, 'login']);
        Route::post('register', [UserController::class, 'register']);
        Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('/email/verify/resend/{user}', [UserController::class, 'resend'])->name('verification.resend');

        Route::get('/permission/{permissionName}', [PermissionController::class, 'check'])->middleware('auth:sanctum');

        Route::post('/validator/vat_id', [ValidationController::class, 'validateVatId']);
        Route::post('/validator/country', [ValidationController::class, 'validateCountry']);

        // WorldCountryCurrency START
        Route::get('/options/country', [WorldCountryCurrencyController::class, 'getCountryOptions']);
        Route::get('/options/currency', [WorldCountryCurrencyController::class, 'getCurrencyOptions']);
        Route::get('/options/language', [WorldCountryCurrencyController::class, 'getLanguageOptions']);
        // WorldCountryCurrency END
        Route::get('/options/referral', [ReferralController::class, 'getReferralOptions']);
    });
});
