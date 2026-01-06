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

        $from = $this->resolveFrom();
        $html = $this->wrapHtml($subject, $html);

        Http::withToken($apiKey)
            ->post('https://api.resend.com/emails', [
                'from' => $from,
                'to' => $to,
                'subject' => $subject,
                'html' => $html,
            ]);
    }

    protected function resolveFrom(): string
    {
        $override = env('RESEND_FROM');
        if (is_string($override) && trim($override) !== '') {
            return trim($override);
        }

        $name = (string) config('mail.from.name', config('app.name', 'Peerie'));
        $address = (string) config('mail.from.address', 'support@peerie.io');

        $name = trim($name);
        $address = trim($address);

        if ($name === '') {
            return $address !== '' ? $address : 'support@peerie.io';
        }

        if ($address === '') {
            return $name;
        }

        return $name . ' <' . $address . '>';
    }

    protected function wrapHtml(string $subject, string $contentHtml): string
    {
        $brand = (string) config('mail.from.name', config('app.name', 'Peerie'));
        $brand = trim($brand) !== '' ? trim($brand) : 'Peerie';
        $logoUrl = $this->resolveLogoUrl();
        $preheader = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');

        return '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $preheader . '</title></head>'
            . '<body style="margin:0;padding:0;background:#f6f7f9;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#111827;">'
            . '<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">' . $preheader . '</div>'
            . '<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f6f7f9;padding:24px 12px;">'
            . '<tr><td align="center">'
            . '<table role="presentation" cellpadding="0" cellspacing="0" width="600" style="width:600px;max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 1px 2px rgba(16,24,40,0.06);">'
            . '<tr><td style="padding:20px 24px;border-bottom:1px solid #eef2f7;">'
            . '<table role="presentation" cellpadding="0" cellspacing="0" width="100%"><tr>'
            . '<td align="left" style="vertical-align:middle;">'
            . ($logoUrl ? '<img src="' . htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') . '" width="40" height="40" alt="' . htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') . '" style="display:block;border-radius:10px;">' : '')
            . '</td>'
            . '<td align="right" style="vertical-align:middle;font-size:16px;font-weight:600;color:#111827;">' . htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') . '</td>'
            . '</tr></table>'
            . '</td></tr>'
            . '<tr><td style="padding:24px;">' . $contentHtml . '</td></tr>'
            . '<tr><td style="padding:16px 24px;border-top:1px solid #eef2f7;font-size:12px;color:#6b7280;">'
            . htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') . '</td></tr>'
            . '</table>'
            . '</td></tr></table>'
            . '</body></html>';
    }

    protected function resolveLogoUrl(): ?string
    {
        $override = env('MAIL_BRAND_LOGO_URL');
        if (is_string($override) && trim($override) !== '') {
            return trim($override);
        }

        $appUrl = (string) config('app.url', '');
        $appUrl = trim($appUrl);
        if ($appUrl === '') {
            return null;
        }

        return rtrim($appUrl, '/') . '/dist/favicon-300x300.png';
    }
}


