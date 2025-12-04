<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ResendEmailService
{
    public function send(string $to, string $subject, string $html): void
    {
        $apiKey = env('RESEND_API_KEY');

        if (!$apiKey) {
            return;
        }

        $from = env('RESEND_FROM', 'onboarding@resend.dev');

        Http::withToken($apiKey)
            ->post('https://api.resend.com/emails', [
                'from' => $from,
                'to' => $to,
                'subject' => $subject,
                'html' => $html,
            ]);
    }
}


