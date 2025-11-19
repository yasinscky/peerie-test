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
        $user = auth()->user();
        
        // Логирование для отладки
        \Log::info('IsAdminMiddleware check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
            'isAdmin()' => $user->isAdmin(),
            'path' => $request->path(),
        ]);

        // Проверяем, является ли он админом
        if (!$user->isAdmin()) {
            \Log::warning('Access denied - user is not admin', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
            ]);
            abort(403, 'У вас нет прав доступа к админ-панели.');
        }

        return $next($request);
    }
}
