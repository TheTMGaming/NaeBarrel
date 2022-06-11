<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        "api/cases/buy",
        "api/auth/login",
        "api/auth/logout",
        "api/auth/deleteprofile",
        "api/auth/register",
        "api/market/createlot",
        "api/market/buy",
        "api/items/sell"
    ];
}
