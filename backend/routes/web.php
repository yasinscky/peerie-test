<?php

use App\Http\Controllers\API\BuyerPersonaController;
use App\Http\Controllers\API\ContentIdeasController;
use App\Http\Controllers\API\HashtagController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\QuestionnaireController;
use App\Http\Controllers\API\ResourceController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\WorksheetController;
use App\Models\User;
use App\Services\ResendEmailService;
use App\Services\VerificationCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

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

const VERIFICATION_COOLDOWN_SECONDS = 30;
const VERIFICATION_COUNTER_TTL_MINUTES = 360;
const VERIFICATION_COOLDOWN_TTL_MINUTES = 360;

function getRegistrationVerificationLastSentKey(int $userId): string
{
    return 'registration_verification_last_sent:'.$userId;
}

function getRegistrationVerificationResendCountKey(int $userId): string
{
    return 'registration_verification_resend_count:'.$userId;
}

function getVerificationRetryAfter(?int $lastSentAt, int $cooldownSeconds): int
{
    if (! $lastSentAt) {
        return 0;
    }

    $elapsed = time() - $lastSentAt;
    $remaining = $cooldownSeconds - $elapsed;

    return $remaining > 0 ? $remaining : 0;
}

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
            '<p>Your verification code is: <strong>'.$codeData['plain'].'</strong></p>'
        );

        Cache::put(getRegistrationVerificationLastSentKey($user->id), time(), now()->addMinutes(VERIFICATION_COOLDOWN_TTL_MINUTES));
        Cache::put(getRegistrationVerificationResendCountKey($user->id), 0, now()->addMinutes(VERIFICATION_COUNTER_TTL_MINUTES));

        $response = [
            'success' => true,
            'user_id' => $user->id,
            'requires_verification' => true,
            'cooldown_seconds' => VERIFICATION_COOLDOWN_SECONDS,
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

        if (! Auth::attempt(['email' => $email, 'password' => $request->password], $request->filled('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = Auth::user();

        if (! $user->email_verified_at) {
            $service = app(VerificationCodeService::class);
            $codeData = $service->createCode($user, 'registration');

            app(ResendEmailService::class)->send(
                $user->email,
                'Verification code for Peerie',
                '<p>Your verification code is: <strong>'.$codeData['plain'].'</strong></p>'
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

        if (! $verified) {
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

        $lastSentAt = Cache::get(getRegistrationVerificationLastSentKey($user->id));
        $retryAfter = getVerificationRetryAfter(is_numeric($lastSentAt) ? (int) $lastSentAt : null, VERIFICATION_COOLDOWN_SECONDS);
        if ($retryAfter > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting a new verification code.',
                'cooldown_seconds' => VERIFICATION_COOLDOWN_SECONDS,
                'retry_after' => $retryAfter,
            ], 429);
        }

        if (! Cache::has(getRegistrationVerificationResendCountKey($user->id))) {
            Cache::put(getRegistrationVerificationResendCountKey($user->id), 0, now()->addMinutes(VERIFICATION_COUNTER_TTL_MINUTES));
        }

        $service = app(VerificationCodeService::class);
        $codeData = $service->createCode($user, 'registration');

        app(ResendEmailService::class)->send(
            $user->email,
            'Verification code for Peerie',
            '<p>Your verification code is: <strong>'.$codeData['plain'].'</strong></p>'
        );

        Cache::put(getRegistrationVerificationLastSentKey($user->id), time(), now()->addMinutes(VERIFICATION_COOLDOWN_TTL_MINUTES));
        $resendCount = (int) Cache::increment(getRegistrationVerificationResendCountKey($user->id));

        $response = [
            'success' => true,
            'message' => 'Verification code resent.',
            'cooldown_seconds' => VERIFICATION_COOLDOWN_SECONDS,
            'resend_count' => $resendCount,
            'show_spam_tip' => $resendCount >= 1,
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

        if (! $user) {
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
            '<p>Your password reset code is: <strong>'.$codeData['plain'].'</strong></p>'
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

        if (! $verified) {
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
        Route::get('/user', [ProfileController::class, 'show']);
        Route::post('/questionnaire', [QuestionnaireController::class, 'submit']);

        Route::prefix('profile')->group(function () {
            Route::put('/', [ProfileController::class, 'update']);
            Route::put('/password', [ProfileController::class, 'updatePassword']);
            Route::post('/email/request-change', [ProfileController::class, 'requestEmailChange']);
            Route::post('/email/confirm-change', [ProfileController::class, 'confirmEmailChange']);
            Route::post('/password/request-change', [ProfileController::class, 'requestPasswordChange']);
        });

        Route::middleware('questionnaire.completed')->group(function () {
            Route::get('/plans', [PlanController::class, 'index']);
            Route::get('/plan/{id}', [PlanController::class, 'show']);
            Route::get('/plans/available-months', [PlanController::class, 'getAvailableMonths']);
            Route::post('/plans/generate-month', [PlanController::class, 'generateMonth']);
            Route::get('/plan-tasks/{planTaskId}', [PlanController::class, 'showPlanTask']);
            Route::put('/plan/{planId}/plan-task/{planTaskId}', [PlanController::class, 'updateTaskStatus']);

            Route::get('/tasks', [TaskController::class, 'index']);
            Route::get('/tasks/{id}', [TaskController::class, 'show']);

            Route::prefix('images')->group(function () {
                Route::post('/search', [ImageController::class, 'search']);
                Route::post('/search/category', [ImageController::class, 'searchByCategory']);
                Route::get('/popular', [ImageController::class, 'popular']);
                Route::get('/{id}', [ImageController::class, 'getImage']);
                Route::get('/{id}/download', [ImageController::class, 'download']);
                Route::post('/download-proxy', [ImageController::class, 'downloadProxy']);
            });

            Route::get('/discord/invite', function () {
                return response()->json([
                    'url' => env('DISCORD_INVITE_URL', 'https://discord.gg/waJUuHvq'),
                ]);
            });

            Route::get('/hashtags', [HashtagController::class, 'index']);
            Route::get('/notifications', [NotificationController::class, 'index']);
            Route::post('/notifications/read', [NotificationController::class, 'markAllRead']);

            Route::prefix('content-ideas')->group(function () {
                Route::get('/available-months', [ContentIdeasController::class, 'getAvailableMonths']);
                Route::get('/by-month', [ContentIdeasController::class, 'getByMonth']);
                Route::get('/by-date', [ContentIdeasController::class, 'getByDate']);
            });

            Route::prefix('resources')->group(function () {
                Route::get('/', [ResourceController::class, 'index']);
                Route::get('/download', [ResourceController::class, 'download']);
                Route::get('/download/{id}', [ResourceController::class, 'downloadById']);
            });

            Route::prefix('buyer-persona')->group(function () {
                Route::get('/', [BuyerPersonaController::class, 'show']);
                Route::put('/', [BuyerPersonaController::class, 'update']);
                Route::get('/export', [BuyerPersonaController::class, 'export']);
            });

            Route::prefix('worksheets')->group(function () {
                Route::get('/', [WorksheetController::class, 'index']);
                Route::get('/{kind}', [WorksheetController::class, 'show']);
                Route::put('/{kind}', [WorksheetController::class, 'update']);
                Route::get('/{kind}/export', [WorksheetController::class, 'export']);
            });
        });
    });
});

Route::fallback(function () {
    return view('app');
});
