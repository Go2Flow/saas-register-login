<?php

use Go2Flow\SaasRegisterLogin\Http\Controllers\API\PermissionController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\ReferralController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\TeamController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\UserController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\ValidationController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\WorldCountryCurrencyController;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Go2Flow\SaasRegisterLogin\Http\Controllers',
    'prefix' => 'api'
], function () {

    Route::group(['middleware' => ['api'], 'prefix' => 'srl'], function () {
        include_once 'api/public.php';
    });

    Route::group(['middleware' => ['api', 'auth:sanctum', 'auth-is-user'], 'prefix' => 'srl'], function () {
        include_once 'api/user.php';
    });

    Route::group(['middleware' => ['api', 'auth:sanctum', 'auth-is-team'], 'prefix' => 'srl'], function () {
        include_once 'api/team.php';
    });
});
