<?php

use Go2Flow\SaasRegisterLogin\Http\Controllers\API\UserController;

use Go2Flow\SaasRegisterLogin\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Go2Flow\SaasRegisterLogin\Http\Controllers',
    'prefix' => 'srl'
], function () {

    Route::group(['middleware' => config('saas-register-login.auth_middleware', ['web', 'auth'])], function () {

    });
    Route::group(['middleware' => config('saas-register-login.open_middleware', ['web'])], function () {
        Route::get('email/verify/{id}/{hash}', [UserController::class, 'verify'])->name('verification.verify');
        Route::get('/impersonate/{user}', [UserController::class, 'impersonate'])->name('impersonate');
        Route::get('/team/change/{team}', [TeamController::class, 'change']);
    });
});
