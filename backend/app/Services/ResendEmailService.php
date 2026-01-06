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

        $primary = '#F34767';
        $secondary = '#3F4369';
        $background = '#f6f7f9';
        $surface = '#ffffff';
        $muted = '#6b7280';
        $text = '#111827';
        $border = '#e5e7eb';

        $code = null;
        $textOnly = trim(preg_replace('/\s+/', ' ', strip_tags($contentHtml)));
        if (preg_match('/\b(\d{6})\b/', $textOnly, $m)) {
            $code = $m[1];
        }

        $safeBrand = htmlspecialchars($brand, ENT_QUOTES, 'UTF-8');
        $safeLogoUrl = $logoUrl ? htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') : null;
        $safeSubject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');

        $bodyHtml = $contentHtml;
        if (is_string($code) && $code !== '') {
            $escapedCode = preg_quote($code, '/');

            $bodyHtml = preg_replace('/<p\b[^>]*>[\s\S]*?' . $escapedCode . '[\s\S]*?<\/p>/i', '', $bodyHtml);
            $bodyHtml = preg_replace('/<div\b[^>]*>[\s\S]*?' . $escapedCode . '[\s\S]*?<\/div>/i', '', $bodyHtml);
            $bodyHtml = preg_replace('/<p\b[^>]*>[\s\S]*?\bcode\s+is\b[\s\S]*?<\/p>/i', '', $bodyHtml);
            $bodyHtml = preg_replace('/<div\b[^>]*>[\s\S]*?\bcode\s+is\b[\s\S]*?<\/div>/i', '', $bodyHtml);

            $bodyHtml = preg_replace('/<strong>\s*' . $escapedCode . '\s*<\/strong>/i', '', $bodyHtml);
            $bodyHtml = trim($bodyHtml);
        }

        $codeBlockHtml = '';
        if (is_string($code) && $code !== '') {
            $codeBlockHtml =
                '<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:0 0 20px;">'
                . '<tr><td style="padding:0;">'
                . '<div style="font-size:14px;line-height:20px;color:' . $muted . ';margin:0 0 10px;">Your confirmation code</div>'
                . '<div style="border:1px solid ' . $border . ';border-radius:16px;background:#f9fafb;padding:18px 16px;text-align:center;">'
                . '<div style="font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:34px;line-height:40px;font-weight:700;letter-spacing:8px;color:' . $secondary . ';margin:0;user-select:all;-webkit-user-select:all;">' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</div>'
                . '</div>'
                . '<div style="font-size:13px;line-height:18px;color:' . $muted . ';margin:10px 0 0;">Tip: tap/click the code to select it, then copy.</div>'
                . '</td></tr></table>';
        }

        $headerHtml = '<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="padding:28px 24px 0;">'
            . '<tr><td align="center" style="padding:0 0 18px;">'
            . ($safeLogoUrl ? '<img src="' . $safeLogoUrl . '" width="56" height="56" alt="' . $safeBrand . '" style="display:block;border-radius:14px;">' : '<div style="width:56px;height:56px;border-radius:14px;background:' . $primary . ';display:inline-block;"></div>')
            . '</td></tr>'
            . '<tr><td align="center" style="font-size:18px;line-height:22px;font-weight:700;color:' . $text . ';padding:0 0 6px;">' . $safeBrand . '</td></tr>'
            . '</table>';

        $footerHtml = '<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="padding:0 24px 26px;">'
            . '<tr><td style="border-top:1px solid #eef2f7;padding-top:16px;font-size:12px;line-height:18px;color:' . $muted . ';">'
            . 'If you didn’t request this email, you can safely ignore it.'
            . '</td></tr></table>';

        return '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $preheader . '</title></head>'
            . '<body style="margin:0;padding:0;background:' . $background . ';font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:' . $text . ';">'
            . '<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">' . $preheader . '</div>'
            . '<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:' . $background . ';padding:24px 12px;">'
            . '<tr><td align="center">'
            . '<table role="presentation" cellpadding="0" cellspacing="0" width="600" style="width:600px;max-width:600px;background:' . $surface . ';border-radius:22px;overflow:hidden;box-shadow:0 8px 24px rgba(16,24,40,0.08);border:1px solid rgba(17,24,39,0.06);">'
            . '<tr><td style="background:' . $surface . ';">' . $headerHtml . '</td></tr>'
            . '<tr><td style="padding:18px 24px 8px;">'
            . '<div style="font-size:28px;line-height:34px;font-weight:800;color:' . $text . ';margin:0 0 14px;">' . $safeSubject . '</div>'
            . '</td></tr>'
            . '<tr><td style="padding:0 24px 6px;">' . $codeBlockHtml . '</td></tr>'
            . '<tr><td style="padding:0 24px 18px;">'
            . '<div style="font-size:16px;line-height:24px;color:#374151;">' . $bodyHtml . '</div>'
            . '</td></tr>'
            . '<tr><td style="padding:0 24px 28px;">'
            . '<div style="height:10px;border-radius:999px;background:linear-gradient(90deg,' . $primary . ', ' . $secondary . ');opacity:0.12;"></div>'
            . '</td></tr>'
            . '<tr><td>' . $footerHtml . '</td></tr>'
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


