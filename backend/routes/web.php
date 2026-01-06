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
    $email = is_string($request->email) ? mb_strtolower(trim($request->email)) : $request->email;
    $credentials = [
        'email' => $email,
        'password' => $request->password,
    ];

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
        $request->merge([
            'email' => is_string($request->email) ? mb_strtolower(trim($request->email)) : $request->email,
        ]);

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
            'Verification code for Peerie',
            '<p>Your verification code is: <strong>' . $codeData['plain'] . '</strong></p>'
        );

        $response = [
            'success' => true,
            'user_id' => $user->id,
            'requires_verification' => true,
            'message' => 'Registration successful. Please verify your email.',
        ];

        if (app()->environment('local')) {
            $response['debug_verification_code'] = $codeData['plain'];
        }

        return response()->json($response, 201);
    });

    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $email = is_string($request->email) ? mb_strtolower(trim($request->email)) : $request->email;

        if (!Auth::attempt(['email' => $email, 'password' => $request->password], $request->filled('remember'))) {
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
                'Verification code for Peerie',
                '<p>Your verification code is: <strong>' . $codeData['plain'] . '</strong></p>'
            );

            Auth::logout();

            $response = [
                'success' => false,
                'requires_verification' => true,
                'user_id' => $user->id,
                'message' => 'Email not verified. Verification code sent.',
            ];

            if (app()->environment('local')) {
                $response['debug_verification_code'] = $codeData['plain'];
            }

            return response()->json($response, 403);
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
            'Verification code for Peerie',
            '<p>Your verification code is: <strong>' . $codeData['plain'] . '</strong></p>'
        );

        $response = [
            'success' => true,
            'message' => 'Verification code resent.',
        ];

        if (app()->environment('local')) {
            $response['debug_verification_code'] = $codeData['plain'];
        }

        return response()->json($response);
    });

    Route::post('/password/forgot', function (Request $request) {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $email = is_string($request->email) ? mb_strtolower(trim($request->email)) : $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If the email exists, a password reset code has been sent.',
            ]);
        }

        $service = app(VerificationCodeService::class);
        $codeData = $service->createCode($user, 'password_reset');

        app(ResendEmailService::class)->send(
            $user->email,
            'Reset your password',
            '<p>Your password reset code is: <strong>' . $codeData['plain'] . '</strong></p>'
        );

        $response = [
            'success' => true,
            'user_id' => $user->id,
            'message' => 'If the email exists, a password reset code has been sent.',
        ];

        if (app()->environment('local')) {
            $response['debug_verification_code'] = $codeData['plain'];
        }

        return response()->json($response);
    })->middleware('throttle:5,15');

    Route::post('/password/reset', function (Request $request) {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($request->input('user_id'));

        $service = app(VerificationCodeService::class);
        $verified = $service->verifyCode($user, 'password_reset', $request->input('code'));

        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code.',
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
        ]);
    })->middleware('throttle:10,15');

    Route::middleware('auth')->group(function () {
        Route::get('/user', [App\Http\Controllers\API\ProfileController::class, 'show']);
        Route::post('/questionnaire', [QuestionnaireController::class, 'submit']);

        Route::prefix('profile')->group(function () {
            Route::put('/', [App\Http\Controllers\API\ProfileController::class, 'update']);
            Route::put('/password', [App\Http\Controllers\API\ProfileController::class, 'updatePassword']);
            Route::post('/email/request-change', [App\Http\Controllers\API\ProfileController::class, 'requestEmailChange']);
            Route::post('/email/confirm-change', [App\Http\Controllers\API\ProfileController::class, 'confirmEmailChange']);
            Route::post('/password/request-change', [App\Http\Controllers\API\ProfileController::class, 'requestPasswordChange']);
        });

        Route::middleware('questionnaire.completed')->group(function () {
            Route::get('/plans', [PlanController::class, 'index']);
            Route::get('/plan/{id}', [PlanController::class, 'show']);
            Route::get('/plans/available-months', [PlanController::class, 'getAvailableMonths']);
            Route::post('/plans/generate-month', [PlanController::class, 'generateMonth']);
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

            Route::get('/discord/invite', function () {
                return response()->json([
                    'url' => env('DISCORD_INVITE_URL', 'https://discord.gg/waJUuHvq'),
                ]);
            });

            Route::get('/hashtags', [HashtagController::class, 'index']);

            Route::prefix('content-ideas')->group(function () {
                Route::get('/available-months', [App\Http\Controllers\API\ContentIdeasController::class, 'getAvailableMonths']);
                Route::get('/by-date', [App\Http\Controllers\API\ContentIdeasController::class, 'getByDate']);
            });

            Route::prefix('resources')->group(function () {
                Route::get('/', [App\Http\Controllers\API\ResourceController::class, 'index']);
                Route::get('/download', [App\Http\Controllers\API\ResourceController::class, 'download']);
            });
        });
    });
});

Route::fallback(function () {
    return view('app');
});
