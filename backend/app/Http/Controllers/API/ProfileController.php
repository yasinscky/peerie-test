<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VerificationCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'language' => $user->language,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'language' => 'nullable|string|in:en,de',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'language' => $request->language ?? $user->language,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'confirmed', Password::min(8)],
            'code' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        try {
            $service = app(VerificationCodeService::class);
            $verified = $service->verifyCode($user, 'change_password', $request->input('code'));

            if (!$verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired verification code',
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function requestEmailChange(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated',
            ], 401);
        }

        $request->validate([
            'new_email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $service = app(VerificationCodeService::class);
        $codeData = $service->createCode($user, 'change_email', [
            'new_email' => $request->input('new_email'),
        ]);

        $newEmail = $request->input('new_email');

        Mail::raw(
            'Your email change code is: ' . $codeData['plain'],
            function ($message) use ($newEmail) {
                $message->to($newEmail)->subject('Confirm your new email address');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Email change code sent',
        ]);
    }

    public function confirmEmailChange(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated',
            ], 401);
        }

        $request->validate([
            'code' => 'required|string',
        ]);

        $service = app(VerificationCodeService::class);
        $verification = $service->verifyCode($user, 'change_email', $request->input('code'));

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code',
            ], 422);
        }

        $payload = $verification->payload ?? [];
        $newEmail = $payload['new_email'] ?? null;

        if (!$newEmail) {
            return response()->json([
                'success' => false,
                'message' => 'New email is missing',
            ], 422);
        }

        $exists = User::where('email', $newEmail)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already taken',
            ], 422);
        }

        $user->email = $newEmail;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email updated successfully',
            'user' => $user->fresh(),
        ]);
    }

    public function requestPasswordChange(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated',
            ], 401);
        }

        $request->validate([
            'current_password' => 'required|string',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $service = app(VerificationCodeService::class);
        $codeData = $service->createCode($user, 'change_password');

        Mail::raw(
            'Your password change code is: ' . $codeData['plain'],
            function ($message) use ($user) {
                $message->to($user->email)->subject('Confirm your password change');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Password change code sent',
        ]);
    }
}
