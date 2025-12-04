<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Services\VerificationCodeService;
use App\Services\ResendEmailService;
use App\Http\Controllers\API\QuestionnaireController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\HashtagController;
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

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::get('/admin/login', function () {
    return view('admin-login');
})->name('admin.login');

Route::post('/admin/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect('/admin');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials.',
    ]);
})->name('admin.login.post');

Route::prefix('api')->group(function () {
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

        $service = app(VerificationCodeService::class);
        $codeData = $service->createCode($user, 'registration');

        app(ResendEmailService::class)->send(
            $user->email,
            'Verify your email',
            '<p>Your verification code is: <strong>' . $codeData['plain'] . '</strong></p>'
        );

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'requires_verification' => true,
            'message' => 'Registration successful. Please verify your email.',
        ], 201);
    });

    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = Auth::user();

        if (!$user->email_verified_at) {
            $service = app(VerificationCodeService::class);
            $codeData = $service->createCode($user, 'registration');

            app(ResendEmailService::class)->send(
                $user->email,
                'Verify your email',
                '<p>Your verification code is: <strong>' . $codeData['plain'] . '</strong></p>'
            );

            Auth::logout();

            return response()->json([
                'success' => false,
                'requires_verification' => true,
                'user_id' => $user->id,
                'message' => 'Email not verified. Verification code sent.',
            ], 403);
        }

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'Login successful!',
        ]);
    });

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful!',
        ]);
    });

    Route::post('/email/verify-registration', function (Request $request) {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'code' => 'required|string',
        ]);

        $user = User::findOrFail($request->input('user_id'));

        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Email already verified.',
            ]);
        }

        $service = app(VerificationCodeService::class);
        $verified = $service->verifyCode($user, 'registration', $request->input('code'));

        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code.',
            ], 422);
        }

        $user->email_verified_at = now();
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'Email verified successfully.',
        ]);
    });

    Route::post('/email/resend-registration-code', function (Request $request) {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($request->input('user_id'));

        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Email already verified.',
            ]);
        }

        $service = app(VerificationCodeService::class);
        $codeData = $service->createCode($user, 'registration');

        app(ResendEmailService::class)->send(
            $user->email,
            'Verify your email',
            '<p>Your verification code is: <strong>' . $codeData['plain'] . '</strong></p>'
        );

        return response()->json([
            'success' => true,
            'message' => 'Verification code resent.',
        ]);
    });

    Route::middleware('auth')->group(function () {
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

        Route::get('/user', [App\Http\Controllers\API\ProfileController::class, 'show']);

        Route::prefix('profile')->group(function () {
            Route::put('/', [App\Http\Controllers\API\ProfileController::class, 'update']);
            Route::put('/password', [App\Http\Controllers\API\ProfileController::class, 'updatePassword']);
            Route::post('/email/request-change', [App\Http\Controllers\API\ProfileController::class, 'requestEmailChange']);
            Route::post('/email/confirm-change', [App\Http\Controllers\API\ProfileController::class, 'confirmEmailChange']);
            Route::post('/password/request-change', [App\Http\Controllers\API\ProfileController::class, 'requestPasswordChange']);
        });

        Route::get('/hashtags', [HashtagController::class, 'index']);
    });
});

Route::fallback(function () {
    return view('app');
});