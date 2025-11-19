<?php

use Laravel\Sanctum\Sanctum;

return [


    'stateful' => array_filter(array_map('trim', explode(',', env('SANCTUM_STATEFUL_DOMAINS', 
        'localhost:3000,127.0.0.1:3000,localhost:8000,127.0.0.1:8000'
    )))),


    'guard' => ['web'],


    'expiration' => null,


    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],

];
