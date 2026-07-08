<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     * 
     * Hanya user dengan role 'admin' yang bisa akses
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah user adalah admin
        $user = Auth::guard('admin')->user();
        
        if ($user->role !== 'admin') {
            // Logout user yang bukan admin
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('error', 'Akses ditolak! Hanya admin yang dapat mengakses sistem ini.');
        }

        return $next($request);
    }
}