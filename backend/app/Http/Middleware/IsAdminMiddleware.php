<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $user = auth()->user();
    
        if (!$user || $request->routeIs('filament.admin.auth.*')) {
            return $next($request);
        }
    
        $user->refresh();
    
        \Log::info('IsAdminMiddleware check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
            'isAdmin()' => $user->isAdmin(),
            'path' => $request->path(),
        ]);
    
        if (!$user->isAdmin()) {
            \Log::warning('Access denied - user is not admin', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'path' => $request->path(),
            ]);
            abort(403, 'У вас нет прав доступа к админ-панели.');
        }
    
        return $next($request);
    }    
}
