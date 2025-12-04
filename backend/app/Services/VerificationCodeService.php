<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VerificationCodeService
{
    public function createCode(User $user, string $action, array $payload = [], ?int $ttlMinutes = 15): array
    {
        $plainCode = $this->generateNumericCode();
        $now = Carbon::now();

        VerificationCode::where('user_id', $user->id)
            ->where('action', $action)
            ->whereNull('used_at')
            ->delete();

        $code = VerificationCode::create([
            'user_id' => $user->id,
            'action' => $action,
            'code' => Hash::make($plainCode),
            'payload' => $payload,
            'expires_at' => $now->copy()->addMinutes($ttlMinutes ?? 15),
        ]);

        return [
            'model' => $code,
            'plain' => $plainCode,
        ];
    }

    public function verifyCode(User $user, string $action, string $plainCode): ?VerificationCode
    {
        $now = Carbon::now();

        $code = VerificationCode::where('user_id', $user->id)
            ->where('action', $action)
            ->whereNull('used_at')
            ->where('expires_at', '>', $now)
            ->latest()
            ->first();

        if (!$code) {
            return null;
        }

        if (!Hash::check($plainCode, $code->code)) {
            return null;
        }

        $code->used_at = $now;
        $code->save();

        return $code;
    }

    protected function generateNumericCode(int $length = 6): string
    {
        $digits = '';

        for ($i = 0; $i < $length; $i++) {
            $digits .= (string) random_int(0, 9);
        }

        return $digits;
    }
}


