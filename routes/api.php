<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Go2Flow\SaasRegisterLogin\Http\Controllers',
    'prefix' => 'api'
], function () {

    Route::group(['middleware' => ['api'], 'prefix' => 'saas-register-login'], function () {

        //Route::get('/client/payment/methods', GetClientPaymentMethodsController::class);

    });
});
