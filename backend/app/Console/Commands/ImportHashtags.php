<?php

namespace App\Console\Commands;

use App\Models\Hashtag;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportHashtags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:hashtags 
        {path : Absolute path к директории или файлу с текстовыми списками хэштегов}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импортирует хэштеги из текстовых шпаргалок (Local/Broad/Industry/Niche/Branded) в таблицу hashtags';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("Путь '{$path}' не найден.");
            return self::FAILURE;
        }

        $files = is_dir($path)
            ? $this->collectTxtFiles($path)
            : [$path];

        if (empty($files)) {
            $this->warn('Файлы *.txt не найдены.');
            return self::INVALID;
        }

        $this->info('Найдено файлов: '.count($files));

        $imported = 0;
        DB::transaction(function () use ($files, &$imported) {
            foreach ($files as $file) {
                $payload = $this->parseFile($file);

                if (!$payload) {
                    $this->warn("Пропущен файл: {$file}");
                    continue;
                }

                Hashtag::updateOrCreate(
                    [
                        'industry' => $payload['industry'],
                        'country' => $payload['country'],
                    ],
                    [
                        'title' => $payload['title'],
                        'tags' => $payload['tags'],
                        'hashtag_blocks' => $payload['blocks'],
                    ]
                );

                $this->line(sprintf(
                    'Импортировано: %s (%s/%s) — %d тегов, %d блоков',
                    basename($file),
                    $payload['industry'],
                    $payload['country'],
                    count($payload['tags']),
                    count($payload['blocks'])
                ));

                $imported++;
            }
        });

        $this->info("Импорт завершён. Всего обновлено: {$imported}");

        return self::SUCCESS;
    }

    /**
     * Собирает список файлов (*.txt) в указанной директории.
     */
    private function collectTxtFiles(string $directory): array
    {
        $items = scandir($directory);
        if ($items === false) {
            return [];
        }

        $files = [];
        foreach ($items as $item) {
            if (in_array($item, ['.', '..'], true)) {
                continue;
            }

            $fullPath = $directory.DIRECTORY_SEPARATOR.$item;
            if (is_dir($fullPath)) {
                $files = array_merge($files, $this->collectTxtFiles($fullPath));
            } elseif (is_file($fullPath) && Str::endsWith(strtolower($item), '.txt')) {
                $files[] = $fullPath;
            }
        }

        sort($files);

        return $files;
    }

    /**
     * Парсит текстовый файл и возвращает данные для сохранения.
     */
    private function parseFile(string $file): ?array
    {
        $content = file_get_contents($file);
        if ($content === false) {
            $this->warn("Не удалось прочитать файл: {$file}");
            return null;
        }

        $content = $this->normalizeContent($content);
        $meta = $this->extractMetaFromFilename($file);

        if (!$meta) {
            $this->warn("Не удалось определить индустрию/страну из имени файла: {$file}");
            return null;
        }

        $blocks = $this->extractBlocks($content);

        if (empty($blocks)) {
            $this->warn("В файле нет структурированных блоков (1 – Local и т.д.): {$file}");
            return null;
        }

        $allTags = collect($blocks)
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values()
            ->all();

        if (empty($allTags)) {
            $this->warn("В файле не обнаружены хэштеги: {$file}");
            return null;
        }

        return [
            'industry' => $meta['industry'],
            'country' => $meta['country'],
            'title' => $meta['title'],
            'tags' => $allTags,
            'blocks' => $blocks
        ];
    }

    /**
     * Приводит текст к UTF-8, чистит управляющие символы и унифицирует переносы строк.
     */
    private function normalizeContent(string $content): string
    {
        $content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1, Windows-1251', true) ?: 'UTF-8');
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $content = preg_replace('/[^\P{C}\n]+/u', '', $content); // убираем управляющие символы, кроме перевода строки

        return $content;
    }

    /**
     * Извлекает метаданные (индустрия, страна, title) из имени файла.
     */
    private function extractMetaFromFilename(string $file): ?array
    {
        $basename = basename($file, '.txt');

        if (!preg_match('/Hashtag\s+List\s*-\s*(.+?)\s*-\s*([A-Z]+)/i', $basename, $matches)) {
            return null;
        }

        $industryLabel = trim($matches[1]);
        $countryLabel = strtoupper(trim($matches[2]));

        $industryMap = [
            'beauty salon' => 'beauty',
            'beauty salons' => 'beauty',
            'physiotherapy' => 'physio',
            'physiotherapy clinic' => 'physio',
            'physiotherapy clinics' => 'physio',
            'coaching' => 'coaching',
            'business coaching' => 'coaching',
        ];

        $industryKey = Str::lower($industryLabel);
        $industry = $industryMap[$industryKey] ?? null;

        $countryMap = [
            'UK' => 'uk',
            'IRL' => 'ie',
            'IE' => 'ie',
            'DE' => 'de',
        ];

        $country = $countryMap[$countryLabel] ?? null;

        if (!$industry || !$country) {
            return null;
        }

        return [
            'industry' => $industry,
            'country' => $country,
            'title' => $industryLabel.' - '.$countryLabel,
        ];
    }

    /**
     * Извлекает блоки (Local, Broad, Industry и т.д.) из файла.
     */
    private function extractBlocks(string $content): array
    {
        $lines = array_map('trim', explode("\n", $content));
        $blocks = [];
        $current = null;
        $capturingTags = false;

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            if (preg_match('/^\d+\s*[–-]\s*(.+)$/u', $line, $match)) {
                if ($current) {
                    $blocks[] = $this->finalizeBlock($current);
                }

                $current = [
                    'title' => $match[0],
                    'description_lines' => [],
                    'tag_lines' => [],
                ];
                $capturingTags = false;
                continue;
            }

            if (!$current) {
                continue;
            }

            if (Str::contains($line, '#')) {
                $capturingTags = true;
            }

            if ($capturingTags) {
                $current['tag_lines'][] = $line;
            } else {
                $current['description_lines'][] = $line;
            }
        }

        if ($current) {
            $blocks[] = $this->finalizeBlock($current);
        }

        return array_values(array_filter($blocks, fn ($block) => !empty($block['tags'])));
    }

    /**
     * Преобразует промежуточную структуру блока в финальный формат.
     */
    private function finalizeBlock(array $block): array
    {
        $description = trim(preg_replace('/\s+/u', ' ', implode(' ', $block['description_lines'])));
        $tags = $this->extractTags($block['tag_lines']);

        return [
            'title' => $block['title'],
            'description' => $description,
            'tags' => $tags,
        ];
    }

    /**
     * Извлекает и нормализует хэштеги из строк.
     */
    private function extractTags(array $lines): array
    {
        $tags = [];

        foreach ($lines as $line) {
            $line = str_replace(['•', '�', '–', '—'], ' ', $line);
            $parts = preg_split('/\s+/u', $line, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($parts as $part) {
                if (Str::startsWith($part, '#')) {
                    $clean = preg_replace('/[^#\pL\pN_]/u', '', $part);
                    if ($clean !== '#' && $clean !== '') {
                        $tags[] = $clean;
                    }
                    continue;
                }

                if (!empty($tags)) {
                    $appendix = preg_replace('/[^A-Za-z0-9]/', '', $part);
                    if ($appendix !== '') {
                        $tags[array_key_last($tags)] .= $appendix;
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($tags)));
    }
}

