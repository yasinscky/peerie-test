<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// CSRF cookie endpoint для SPA
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Простая страница логина для админки
Route::get('/admin-login', function () {
    return view('admin-login');
})->name('admin.login');

Route::post('/admin-login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect('/admin');
    }

    return back()->withErrors([
        'email' => 'Неверные учетные данные.',
    ]);
})->name('admin.login.post');

// Маршрут для SPA (исключаем админку)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|sanctum|admin-login).*');