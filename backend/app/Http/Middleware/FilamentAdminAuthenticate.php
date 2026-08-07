<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

class FilamentAdminAuthenticate extends FilamentAuthenticate
{
    protected function redirectTo($request): ?string
    {
        return $request->expectsJson() ? null : route('admin.login');
    }
}

