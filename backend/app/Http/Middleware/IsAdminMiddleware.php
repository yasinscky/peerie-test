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
    public function handle(Request $request, Closure $next): Response
    {
        // Пропускаем все маршруты аутентификации Filament
        $path = $request->path();
        if (str_starts_with($path, 'admin/login') || 
            str_contains($path, 'admin/auth') ||
            $request->routeIs('filament.admin.auth.*') ||
            $path === 'admin') {
            // Для страницы /admin и страниц логина пропускаем проверку админа
            // Filament Authenticate middleware обработает редирект на логин
            return $next($request);
        }

        // Если пользователь не залогинен, пропускаем (Filament Authenticate middleware обработает редирект)
        if (!auth()->check()) {
            return $next($request);
        }

        // Если залогинен, но не админ - запрещаем доступ
        if (!auth()->user()->isAdmin()) {
            abort(403, 'У вас нет прав доступа к админ-панели.');
        }

        return $next($request);
    }
}
