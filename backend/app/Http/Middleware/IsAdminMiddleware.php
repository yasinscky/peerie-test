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
        $path = $request->path();
        
        // Пропускаем страницу логина - если пользователь залогинен, но не админ,
        // разлогиним его, чтобы он мог залогиниться заново
        if (str_starts_with($path, 'admin/login') || 
            str_contains($path, 'admin/auth') ||
            $request->routeIs('filament.admin.auth.*')) {
            // Если пользователь залогинен, но не админ - разлогиниваем его
            if ($user && !$user->isAdmin()) {
                \Log::info('Logging out non-admin user from admin panel', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return $next($request);
        }
        
        // Логирование для отладки
        \Log::info('IsAdminMiddleware check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
            'isAdmin()' => $user->isAdmin(),
            'path' => $path,
        ]);

        // Проверяем, является ли он админом
        if (!$user->isAdmin()) {
            \Log::warning('Access denied - user is not admin', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'path' => $path,
            ]);
            abort(403, 'У вас нет прав доступа к админ-панели.');
        }

        return $next($request);
    }
}
