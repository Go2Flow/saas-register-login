<?php

use Go2Flow\SaasRegisterLogin\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Go2Flow\SaasRegisterLogin\Http\Controllers',
    'prefix' => 'srl'
], function () {

    Route::group(['middleware' => config('saas-register-login.middleware', ['web', 'auth'])], function () {
        Route::get('email/verify/{id}/{hash}', [UserController::class, 'verify'])->name('verification.verify');
    });
});
