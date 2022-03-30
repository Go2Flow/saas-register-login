<?php

use Go2Flow\SaasRegisterLogin\Http\Controllers\API\PermissionController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\UserController;
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
    });
});
