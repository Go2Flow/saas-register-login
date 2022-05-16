<?php

use Go2Flow\PSPClient\Http\Controllers\API\PaymentController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\ReferralController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\TeamController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\UserController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\ValidationController;
use Go2Flow\SaasRegisterLogin\Http\Controllers\API\WorldCountryCurrencyController;
use Illuminate\Support\Facades\Route;

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);

Route::post('/email/verify/resend/{user}', [UserController::class, 'resend'])->name('verification.resend');
Route::post('/password/reset/mail', [UserController::class, 'sendResetPasswordMail'])->name('password.reset.send');
Route::post('/password/reset/submit', [UserController::class, 'passwordResetSave'])->name('password.reset.save');

// Validators
Route::post('/validator/vat_id', [ValidationController::class, 'validateVatId']);
Route::post('/validator/country', [ValidationController::class, 'validateCountry']);
Route::post('/validator/unique/team/name', [ValidationController::class, 'validateUniqueTeamName']);
Route::post('/validator/unique/user/email', [ValidationController::class, 'validateUniqueUserEmail']);

// WorldCountryCurrency START
Route::get('/options/country', [WorldCountryCurrencyController::class, 'getCountryOptions']);
Route::get('/options/currency', [WorldCountryCurrencyController::class, 'getCurrencyOptions']);
Route::get('/options/language', [WorldCountryCurrencyController::class, 'getLanguageOptions']);
// WorldCountryCurrency END
Route::get('/options/referral', [ReferralController::class, 'getReferralOptions']);

Route::get('/team/{team}/invite/{invitationid}/validate/{hash}', [TeamController::class, 'inviteValidate']);
Route::post('/team/{team}/invite/{invitationid}/accept/{hash}', [TeamController::class, 'acceptValidate']);
