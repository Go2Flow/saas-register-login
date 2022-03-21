<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Go2Flow\SaasRegisterLogin\Http\Controllers',
    'prefix' => 'srl'
], function () {

    Route::group(['middleware' => config('saas-register-login.middleware', ['web', 'auth'])], function () {

    });
});