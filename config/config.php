<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'auth_middleware' => ['web', 'auth'],
    'open_middleware' => ['web'],
    'is_multi_language' => true,
    'dev_psp_id' => '4053261b',
    'webhook' => 'https://courzly.com/api/psp-client/go2flow/finance/payment/status',
    'team_creation_queue' => 'default',
    'default_service_fee' => 1,
];
