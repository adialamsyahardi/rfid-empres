<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', '🔒 Silakan login terlebih dahulu!');
        }

        // Cek apakah user adalah admin
        if (auth()->user()->role !== 'admin') {
            // ✅ Redirect ke dashboard dengan pesan error
            return redirect()->route('dashboard')
                ->with('error', '⚠️ Akses ditolak! Halaman ini hanya untuk Administrator.');
        }

        return $next($request);
    }
}