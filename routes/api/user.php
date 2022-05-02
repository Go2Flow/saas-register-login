<?php

use Go2Flow\SaasRegisterLogin\Http\Controllers\API\PermissionController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\TeamController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::post('logout', [UserController::class, 'logout']);

Route::get('/permission/{permissionName}', [PermissionController::class, 'check']);

// Team
Route::get('/team', [TeamController::class, 'current']);
Route::get('/teams', [TeamController::class, 'teams']);
Route::get('/team/users', [TeamController::class, 'users']);
Route::get('/team/roles', [TeamController::class, 'roles']);

Route::post('/team/create', [TeamController::class, 'create']);
Route::post('/team/{team}/update/general', [TeamController::class, 'updateGeneral']);
Route::post('/team/{team}/update/bank', [TeamController::class, 'updateBank']);
Route::post('/team/{team}/invite', [TeamController::class, 'invite']);
Route::get('/team/{team}/pending', [TeamController::class, 'pending']);
Route::post('/team/{team}/invite/{invitation}/delete', [TeamController::class, 'inviteDelete']);
Route::post('/team/{team}/user/{user}/remove', [TeamController::class, 'removeUser']);

// Team API Tokens
Route::post('/team/{team}/tokens/create', [TeamController::class, 'createToken']);
Route::get('/team/{team}/tokens/list', [TeamController::class, 'listTokens']);
Route::post('/team/{team}/tokens/delete/{tokenId}', [TeamController::class, 'deleteToken']);
