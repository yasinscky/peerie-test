<?php

namespace App\Support;

class SimplePdf
{
    public static function fromBlocks(string $title, array $blocks): string
    {
        $lines = [];
        $lines[] = ['style' => 'h1', 'text' => $title];

        foreach ($blocks as $block) {
            $lines[] = ['style' => 'h2', 'text' => $block['title']];
            foreach ($block['items'] as $item) {
                $lines[] = ['style' => 'label', 'text' => $item['label']];
                $value = trim((string) ($item['value'] ?? ''));
                $lines[] = ['style' => 'value', 'text' => $value !== '' ? $value : '—'];
                $lines[] = ['style' => 'gap', 'text' => ''];
            }
        }

        $pages = self::paginate($lines);
        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';

        $kids = [];
        $contentIds = [];
        $fontId = 3 + (count($pages) * 2);
        $nextId = 3;

        foreach ($pages as $pageIndex => $content) {
            $pageId = $nextId++;
            $contentId = $nextId++;
            $kids[] = $pageId . ' 0 R';
            $contentIds[$pageIndex] = [$pageId, $contentId, $content];
        }

        $objects[] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($pages) . ' >>';

        foreach ($contentIds as [$pageId, $contentId, $content]) {
            $objects[$pageId - 1] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 ' . $fontId . ' 0 R >> >> /Contents ' . $contentId . ' 0 R >>';
            $stream = self::stream($content);
            $objects[$contentId - 1] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
        }

        $objects[$fontId - 1] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';

        return self::assemble($objects);
    }

    private static function paginate(array $lines): array
    {
        $pages = [];
        $current = [];
        $y = 800;

        foreach ($lines as $line) {
            $size = match ($line['style']) {
                'h1' => 18,
                'h2' => 13,
                'label' => 10,
                default => 11,
            };
            $wrapped = $line['style'] === 'gap' ? [''] : self::wrap($line['text'], $line['style'] === 'h1' ? 40 : 92);
            $needed = max(1, count($wrapped)) * ($size + 4) + ($line['style'] === 'gap' ? 8 : 0);

            if ($y - $needed < 50 && $current) {
                $pages[] = $current;
                $current = [];
                $y = 800;
            }

            foreach ($wrapped as $text) {
                $current[] = [
                    'style' => $line['style'],
                    'text' => $text,
                    'y' => $y,
                    'size' => $size,
                ];
                $y -= $size + 4;
            }

            if ($line['style'] === 'gap') {
                $y -= 6;
            }
        }

        if ($current) {
            $pages[] = $current;
        }

        return $pages ?: [[]];
    }

    private static function wrap(string $text, int $width): array
    {
        $normalized = preg_replace("/\r\n|\r/", "\n", $text) ?? $text;
        $result = [];

        foreach (explode("\n", $normalized) as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                $result[] = '';
                continue;
            }
            $words = preg_split('/\s+/', $paragraph) ?: [];
            $row = '';
            foreach ($words as $word) {
                $next = $row === '' ? $word : $row . ' ' . $word;
                if (mb_strlen($next) > $width && $row !== '') {
                    $result[] = $row;
                    $row = $word;
                } else {
                    $row = $next;
                }
            }
            if ($row !== '') {
                $result[] = $row;
            }
        }

        return $result ?: [''];
    }

    private static function stream(array $items): string
    {
        $chunks = ['BT'];
        foreach ($items as $item) {
            if ($item['style'] === 'gap') {
                continue;
            }
            $rgb = $item['style'] === 'h2' ? '0.95 0.28 0.40 rg' : '0.11 0.10 0.11 rg';
            $escaped = self::escape(self::winAnsi($item['text']));
            $chunks[] = sprintf(
                '/F1 %d Tf %s 1 0 0 1 50 %.2F Tm (%s) Tj',
                $item['size'],
                $rgb,
                $item['y'],
                $escaped
            );
        }
        $chunks[] = 'ET';

        return implode("\n", $chunks);
    }

    private static function winAnsi(string $text): string
    {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted === false) {
            $converted = @mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        }

        return $converted === false ? $text : $converted;
    }

    private static function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private static function assemble(array $objects): string
    {
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i < $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer << /Size {$count} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }
}
