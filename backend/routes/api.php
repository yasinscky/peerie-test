<?php

use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\QuestionnaireController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Controllers\API\HashtagController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);

    return response()->json([
        'success' => true,
        'user' => $user,
        'message' => 'Регистрация успешна!'
    ], 201);
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
        throw ValidationException::withMessages([
            'email' => ['Неверные учетные данные.'],
        ]);
    }

    $user = Auth::user();

    return response()->json([
        'success' => true,
        'user' => $user,
        'message' => 'Вход выполнен успешно!'
    ]);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'success' => true,
        'message' => 'Выход выполнен успешно!'
    ]);
});

Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/questionnaire', [QuestionnaireController::class, 'submit']);
    
    Route::get('/plans', [PlanController::class, 'index']);
    Route::get('/plan/{id}', [PlanController::class, 'show']);
    Route::put('/plan/{planId}/plan-task/{planTaskId}', [PlanController::class, 'updateTaskStatus']);
    
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    
    Route::prefix('images')->group(function () {
        Route::post('/search', [App\Http\Controllers\API\ImageController::class, 'search']);
        Route::post('/search/category', [App\Http\Controllers\API\ImageController::class, 'searchByCategory']);
        Route::get('/popular', [App\Http\Controllers\API\ImageController::class, 'popular']);
        Route::get('/{id}', [App\Http\Controllers\API\ImageController::class, 'getImage']);
        Route::get('/{id}/download', [App\Http\Controllers\API\ImageController::class, 'download']);
        Route::post('/download-proxy', [App\Http\Controllers\API\ImageController::class, 'downloadProxy']);
    });

    Route::prefix('profile')->group(function () {
        Route::put('/', [App\Http\Controllers\API\ProfileController::class, 'update']);
        Route::put('/password', [App\Http\Controllers\API\ProfileController::class, 'updatePassword']);
    });

    Route::get('/hashtags', [HashtagController::class, 'index']);
});
