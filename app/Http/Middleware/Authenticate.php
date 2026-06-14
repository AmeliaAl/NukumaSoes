<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jika request bukan JSON (API), redirect ke halaman login
        // Route name 'login' harus ada di routes/web.php
        return $request->expectsJson() ? null : route('login');
    }
}