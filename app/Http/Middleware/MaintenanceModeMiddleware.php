<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If maintenance mode is NOT active, proceed normally
        if (!SystemSetting::get('maintenance_mode', false)) {
            return $next($request);
        }

        // 2. Always allow static assets & build files to render properly
        if ($request->is('build/*', 'storage/*', 'images/*', 'assets/*', 'favicon.ico')) {
            return $next($request);
        }

        // 3. Allow authenticated Admin roles to access the entire system.
        //    Gunakan peran AKTIF (active role) untuk akun multi-peran.
        if (Auth::check()) {
            $user = Auth::user();
            if (in_array($user->activeRole(), ['Admin Aplikasi', 'Admin RW'], true)) {
                return $next($request);
            }
        }

        // 4. Allow auth & role-selection endpoints so administrators can authenticate
        //    and pick their active role during maintenance.
        if ($request->is('login') || $request->is('logout') || $request->is('pilih-peran') || $request->is('ganti-peran')) {
            return $next($request);
        }

        // 5. Intercept all non-admin and public requests with HTTP 503 Maintenance Page
        return response()->view('errors.503', [], 503);
    }
}
