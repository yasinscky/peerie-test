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
        // Этот middleware применяется только после Authenticate middleware
        // Если мы здесь, значит пользователь уже залогинен
        // Проверяем, является ли он админом
        if (!auth()->user()->isAdmin()) {
            abort(403, 'У вас нет прав доступа к админ-панели.');
        }

        return $next($request);
    }
}
