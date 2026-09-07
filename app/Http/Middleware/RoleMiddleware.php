<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user's role is in allowed roles for this route
        if (!empty($roles) && !in_array($user->role, $roles)) {
            $redirectUrl = $this->getRedirectUrlForRole($user->role);
            
            return redirect($redirectUrl)
                ->with('error', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        }

        return $next($request);
    }

    /**
     * Get authorized home URL based on user role
     */
    protected function getRedirectUrlForRole(?string $role): string
    {
        $sidebarMenu = config('sidebarMenu', []);

        foreach ($sidebarMenu as $item) {
            if (!empty($item['roles']) && in_array($role, $item['roles'])) {
                $url = isset($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']) 
                    ? route($item['route']) 
                    : ($item['url'] ?? null);

                if ($url && $url !== '#') {
                    return $url;
                }
            }
        }

        return '/warga';
    }
}
