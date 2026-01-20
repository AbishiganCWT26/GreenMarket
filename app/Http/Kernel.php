<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware – runs on every request.
     */
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePathEncoding::class,

        \App\Http\Middleware\AdminMiddleware::class,
        \App\Http\Middleware\BuyerMiddleware::class,
        \App\Http\Middleware\FacilitatorMiddleware::class,
        \App\Http\Middleware\FarmerMiddleware::class,
        \App\Http\Middleware\LeadFarmerMiddleware::class,
        // App\Http\Middleware\AdminMiddleware::class,
        // App\Http\Middleware\BuyerMiddleware::class,
        // App\Http\Middleware\FacilitatorMiddleware::class,
        // App\Http\Middleware\FarmerMiddleware::class,
        // App\Http\Middleware\LeadFarmerMiddleware::class,
    ];

    /**
     * Middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware aliases.
     *
     * These middleware may be assigned to groups or used individually.
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // ROLE MIDDLEWARE - KEEP THESE HERE
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'farmer' => \App\Http\Middleware\FarmerMiddleware::class,
        'lead_farmer' => \App\Http\Middleware\LeadFarmerMiddleware::class,
        'buyer' => \App\Http\Middleware\BuyerMiddleware::class,
        'facilitator' => \App\Http\Middleware\FacilitatorMiddleware::class,
    ];
}
