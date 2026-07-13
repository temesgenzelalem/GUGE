<?php

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1,::1,localhost:3000,127.0.0.1:3000')),

    'expiration' => env('SANCTUM_EXPIRATION', 120),

    'middleware' => [
        'verify_csrf_token' => VerifyCsrfToken::class,
        'encrypt_cookies' => EncryptCookies::class,
        'add_cookies_to_response' => AddQueuedCookiesToResponse::class,
        'start_session' => StartSession::class,
        'authenticate_session' => AuthenticateSession::class,
        'ensure_frontend_requests_are_stateful' => EnsureFrontendRequestsAreStateful::class,
    ],

    'prefix' => env('SANCTUM_PREFIX', 'sanctum'),

    'guard' => ['web'],
];
