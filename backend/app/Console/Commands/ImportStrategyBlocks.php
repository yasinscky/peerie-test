<?php

namespace App\Console\Commands;

use App\Models\Task;
use DOMDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportStrategyBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:strategy-blocks 
        {path : Absolute path к HTML или CSV файлу с таблицей Strategy Blocks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импортирует маркетинговые задачи из таблицы Strategy Blocks в таблицу tasks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->argument('path');

        if (!is_file($path)) {
            $this->error("Файл '{$path}' не найден. Укажи абсолютный путь к HTML/CSV.");
            return self::FAILURE;
        }

        $rows = $this->loadRows($path);

        if (empty($rows)) {
            $this->warn('Данные не найдены в файле. Проверь структуру таблицы.');
            return self::INVALID;
        }

        $this->info('Импортируем задачи, это может занять несколько секунд...');

        $tasksPayload = $this->aggregateRows($rows);

        $this->info('Подготовлено задач: '.count($tasksPayload));

        DB::transaction(function () use ($tasksPayload) {
            foreach ($tasksPayload as $payload) {
                Task::updateOrCreate(
                    ['external_id' => $payload['external_id']],
                    [
                        'title' => $payload['title'],
                        'description' => $payload['description'],
                        'duration_hours' => $payload['duration_hours'],
                        'frequency' => $payload['frequency'],
                        'dependencies' => [],
                        'business_type' => $payload['business_type'],
                        'language' => $payload['language'],
                        'is_local' => $payload['is_local'],
                        'requires_website' => $payload['requires_website'],
                        'difficulty_level' => $payload['difficulty_level'],
                        'category' => $payload['category'],
                        'global_order' => $payload['global_order'],
                        'is_global' => $payload['is_global'],
                        'target_countries' => $payload['target_countries'],
                        'target_industries' => $payload['target_industries'],
                        'allowed_capacities' => $payload['allowed_capacities'],
                        'local_presence_options' => $payload['local_presence_options'],
                        'conditions' => $payload['conditions'],
                    ]
                );
            }
        });

        $this->info('Импорт успешно завершён.');
        return self::SUCCESS;
    }

    /**
     * Загружает строки из HTML или CSV файла
     */
    private function loadRows(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'html', 'htm' => $this->loadFromHtml($path),
            'csv' => $this->loadFromCsv($path),
            default => [],
        };
    }

    /**
     * Парсинг HTML таблицы
     */
    private function loadFromHtml(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false) {
            return [];
        }

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($content);
        libxml_clear_errors();

        $rows = [];
        $tableRows = $document->getElementsByTagName('tr');
        $headers = [];

        foreach ($tableRows as $index => $row) {
            if (!$row instanceof \DOMElement) {
                continue;
            }

            /** @var \DOMElement $row */
            $cells = $row->getElementsByTagName($index === 0 ? 'th' : 'td');
            $values = [];

            foreach ($cells as $cell) {
                $values[] = trim($cell->textContent);
            }

            if ($index === 0) {
                $headers = $this->normalizeHeaders($values);
                continue;
            }

            if (empty(array_filter($values))) {
                continue;
            }

            $rows[] = $this->combineRow($headers, $values);
        }

        return $rows;
    }

    /**
     * Парсинг CSV
     */
    private function loadFromCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $rows = [];
        $headers = [];

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if (empty($headers)) {
                $headers = $this->normalizeHeaders($data);
                continue;
            }

            if (empty(array_filter($data))) {
                continue;
            }

            $rows[] = $this->combineRow($headers, $data);
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Приводим заголовки к snake_case
     */
    private function normalizeHeaders(array $headers): array
    {
        return array_map(function (string $header) {
            $clean = str_replace("\xEF\xBB\xBF", '', $header);
            $clean = str_replace(chr(239).chr(187).chr(191), '', $clean);

            return Str::snake(Str::lower(trim($clean)));
        }, $headers);
    }

    /**
     * Комбинируем значения строки с заголовками
     */
    private function combineRow(array $headers, array $values): array
    {
        $combined = [];
        foreach ($headers as $index => $header) {
            $combined[$header] = $values[$index] ?? null;
        }

        return $combined;
    }

    /**
     * Агрегируем строки (ActionID + язык)
     */
    private function aggregateRows(array $rows): array
    {
        $grouped = [];
        $skipped = 0;
        $coverage = [];
        $normalizedRows = [];

        foreach ($rows as $row) {
            $normalized = $this->normalizeRow($row);

            if (empty($normalized['action_id'])) {
                $skipped++;
                continue;
            }

            $normalizedRows[] = $normalized;
            $coverage[$normalized['action_id']] = $this->mergeUnique(
                $coverage[$normalized['action_id']] ?? [],
                $normalized['countries']
            );
        }

        foreach ($normalizedRows as $normalized) {
            $language = $normalized['language'] ?? 'en';
            $key = $normalized['action_id'].'::'.$language;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'action_id' => $normalized['action_id'],
                    'external_id' => $normalized['action_id'].'_'.$language,
                    'language' => $language,
                    'title' => $normalized['action'],
                    'category' => $normalized['category'] ?: 'General',
                    'global_order' => $normalized['global_order'],
                    'instructions' => [],
                    'templates' => [],
                    'template_links' => [],
                    'prerequisites' => [],
                    'cadences' => [],
                    'efforts' => [],
                    'countries' => $coverage[$normalized['action_id']] ?? $normalized['countries'],
                    'industries' => [],
                    'capacities' => [],
                    'local_presence' => [],
                ];
            }

            $grouped[$key]['instructions'][] = $normalized['instruction'];
            if (!empty($normalized['template'])) {
                $grouped[$key]['templates'][] = $normalized['template'];
            }
            if (!empty($normalized['template_link'])) {
                $grouped[$key]['template_links'][] = $normalized['template_link'];
            }
            if (!empty($normalized['prerequisites'])) {
                $grouped[$key]['prerequisites'][] = $normalized['prerequisites'];
            }
            if (!empty($normalized['cadence'])) {
                $grouped[$key]['cadences'][] = $normalized['cadence'];
            }
            if (!empty($normalized['effort_min'])) {
                $grouped[$key]['efforts'][] = $normalized['effort_min'];
            }

            $grouped[$key]['industries'] = $this->mergeUnique(
                $grouped[$key]['industries'],
                $normalized['industries']
            );

            $grouped[$key]['capacities'] = $this->mergeUnique(
                $grouped[$key]['capacities'],
                $normalized['capacities']
            );

            $grouped[$key]['local_presence'] = $this->mergeUnique(
                $grouped[$key]['local_presence'],
                $normalized['local_presence']
            );
        }

        if ($skipped > 0) {
            $this->warn("Пропущено строк без ActionID: {$skipped}");
        }

        return array_map(fn ($item) => $this->buildPayload($item), array_values($grouped));
    }

    /**
     * Собираем описание задачи
     */
    private function buildDescription(array $instructions, array $templates, array $templateLinks, array $prerequisites): string
    {
        $parts = [];

        $uniqueInstructions = array_values(array_filter(array_unique($instructions)));
        if (!empty($uniqueInstructions)) {
            $parts[] = implode(PHP_EOL.PHP_EOL, $uniqueInstructions);
        }

        if (!empty(array_filter($templates))) {
            $parts[] = 'Template available';
        }

        $uniqueLinks = array_values(array_filter(array_unique($templateLinks)));
        if (!empty($uniqueLinks)) {
            $parts[] = 'Resources: '.implode(', ', $uniqueLinks);
        }

        $uniquePrerequisites = array_values(array_filter(array_unique($prerequisites)));
        if (!empty($uniquePrerequisites)) {
            $parts[] = 'Prerequisites: '.implode('; ', $uniquePrerequisites);
        }

        return implode(PHP_EOL.PHP_EOL, $parts);
    }

    /**
     * Переводим Effort в часы
     */
    private function resolveDurationHours(?string $effort): int
    {
        $minutes = (int) filter_var($effort ?? 0, FILTER_SANITIZE_NUMBER_INT);
        if ($minutes <= 0) {
            return 1;
        }

        return (int) max(1, ceil($minutes / 60));
    }

    /**
     * Конвертируем Cadence → frequency
     */
    private function resolveFrequency(?string $cadence): string
    {
        $value = Str::lower(trim((string) $cadence));

        return match (true) {
            str_contains($value, 'half-year') => 'quarterly',
            str_contains($value, 'bi-week') => 'weekly',
            str_contains($value, 'weekly') => 'weekly',
            str_contains($value, 'monthly') => 'monthly',
            str_contains($value, 'quarter') => 'quarterly',
            default => 'once',
        };
    }

    /**
     * Приводим язык
     */
    private function resolveLanguage(?string $language): string
    {
        $value = Str::lower(trim((string) $language));

        return match ($value) {
            'english', 'en' => 'en',
            'german', 'de' => 'de',
            default => 'en',
        };
    }

    /**
     * Строим payload для сохранения
     */
    private function buildPayload(array $data): array
    {
        $durationHours = $this->resolveAggregatedDurationHours($data['efforts']);
        $frequency = $this->resolveFrequency($data['cadences'][0] ?? null);

        $targetCountries = array_values(array_unique(array_filter($data['countries'])));
        $isGlobal = count($targetCountries) >= 3;

        $description = $this->buildDescription(
            $data['instructions'],
            $data['templates'],
            $data['template_links'],
            $data['prerequisites']
        );

        return [
            'external_id' => $data['external_id'],
            'title' => $data['title'] ?: 'Untitled task',
            'description' => $description,
            'duration_hours' => $durationHours,
            'frequency' => $frequency,
            'business_type' => 'service',
            'language' => $data['language'],
            'is_local' => $this->resolveIsLocalFromOptions($data['local_presence']),
            'requires_website' => $this->resolveRequiresWebsiteText($description),
            'difficulty_level' => $this->resolveDifficultyFromHours($durationHours),
            'category' => $data['category'],
            'global_order' => $data['global_order'],
            'is_global' => $isGlobal,
            'target_countries' => $targetCountries,
            'target_industries' => array_values(array_unique(array_filter($data['industries']))),
            'allowed_capacities' => $this->normalizeCapacities($data['capacities']),
            'local_presence_options' => array_values(array_unique(array_filter($data['local_presence']))),
            'conditions' => $this->collectConditions($data['prerequisites']),
        ];
    }

    /**
     * Нормализуем строку CSV/HTML
     */
    private function normalizeRow(array $row): array
    {
        $actionId = trim((string) ($row['action_id'] ?? $row['actionid'] ?? ''));

        return [
            'action_id' => $actionId,
            'global_order' => $this->extractIntValue($row, ['global_order', 'globalorder']),
            'category' => trim((string) $this->extractValue($row, ['category'])),
            'action' => trim((string) $this->extractValue($row, ['action'])),
            'prerequisites' => trim((string) $this->extractValue($row, ['prerequisites'])),
            'instruction' => trim((string) $this->extractValue($row, ['instruction'])),
            'cadence' => trim((string) $this->extractValue($row, ['cadence'])),
            'countries' => $this->splitValues($this->extractValue($row, ['country']), fn ($value) => $this->normalizeCountry($value)),
            'language' => $this->resolveLanguage($this->extractValue($row, ['language'])),
            'industries' => $this->splitValues($this->extractValue($row, ['industry']), fn ($value) => $this->normalizeIndustry($value)),
            'capacities' => $this->splitValues($this->extractValue($row, ['capacity_allowed', 'capacityallowed']), fn ($value) => $this->normalizeCapacity($value)),
            'effort_min' => trim((string) $this->extractValue($row, ['effort_min', 'effortmin'])),
            'local_presence' => $this->splitValues($this->extractValue($row, ['local_presence', 'localpresence']), fn ($value) => $this->normalizeLocalPresence($value)),
            'template' => trim((string) $this->extractValue($row, ['template'])),
            'template_link' => trim((string) $this->extractValue($row, ['template_link', 'templatelink'])),
        ];
    }

    private function extractValue(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                return $row[$key] ?? '';
            }
        }

        return '';
    }

    private function extractIntValue(array $row, array $keys): ?int
    {
        $value = $this->extractValue($row, $keys);

        return $value !== '' ? (int) $value : null;
    }

    private function mergeUnique(array $base, array $additional): array
    {
        return array_values(array_unique(array_merge($base, $additional)));
    }

    private function splitValues(string $value, callable $callback): array
    {
        $items = array_map('trim', explode(',', $value ?? ''));
        $items = array_filter($items, fn ($item) => $item !== '');

        return array_values(array_filter(array_map($callback, $items)));
    }

    private function normalizeCountry(string $value): ?string
    {
        return match (Str::lower($value)) {
            'de' => 'de',
            'uk' => 'uk',
            'ire', 'ie' => 'ie',
            default => null,
        };
    }

    private function normalizeIndustry(string $value): ?string
    {
        return match (Str::lower($value)) {
            'beauty' => 'beauty',
            'physio' => 'physio',
            'coach', 'coaching' => 'coaching',
            default => null,
        };
    }

    private function normalizeCapacity(string $value): ?int
    {
        $number = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);

        return $number > 0 ? $number : null;
    }

    private function normalizeLocalPresence(string $value): ?string
    {
        $lower = Str::lower($value);

        if ($lower === 'yes') {
            return 'yes';
        }

        if ($lower === 'no') {
            return 'no';
        }

        return null;
    }

    /**
     * Определяем локальность задачи на основе вариантов
     */
    private function resolveIsLocalFromOptions(array $options): bool
    {
        $options = array_values(array_unique($options));

        return $options === ['yes'];
    }

    /**
     * Определяем, требует ли задача сайт
     */
    private function resolveRequiresWebsiteText(string $text): bool
    {
        $haystack = Str::lower($text);

        $keywords = ['website', 'landing', 'page', 'site', 'web', 'seo audit'];

        foreach ($keywords as $keyword) {
            if (str_contains($haystack, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Определяем сложность
     */
    private function resolveDifficultyFromHours(int $hours): string
    {
        return match (true) {
            $hours <= 2 => 'beginner',
            $hours <= 4 => 'intermediate',
            default => 'advanced',
        };
    }

    /**
     * Рассчитываем длительность для агрегированного набора строк
     */
    private function resolveAggregatedDurationHours(array $efforts): int
    {
        if (empty($efforts)) {
            return 1;
        }

        $hours = array_map(fn ($value) => $this->resolveDurationHours($value), $efforts);

        return max($hours);
    }

    /**
     * Нормализуем доступные часы выполнения
     */
    private function normalizeCapacities(array $capacities): array
    {
        $values = array_values(array_unique(array_filter($capacities)));
        sort($values);

        return $values;
    }

    /**
     * Собираем условия показа задачи
     */
    private function collectConditions(array $prerequisites): array
    {
        $conditions = [];

        foreach ($prerequisites as $raw) {
            foreach ($this->parsePrerequisites($raw) as $condition) {
                $key = $condition['field'].'|'.$condition['operator'].'|'.($condition['value'] === true ? '1' : ($condition['value'] === false ? '0' : $condition['value']));
                $conditions[$key] = $condition;
            }
        }

        return array_values($conditions);
    }

    /**
     * Парсим prerequisites в условия
     */
    private function parsePrerequisites(string $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        $rawItems = preg_split('/[;,]+/', $raw);
        $result = [];

        foreach ($rawItems as $item) {
            $item = trim($item);
            if ($item === '') {
                continue;
            }

            if (!str_contains($item, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $item, 2));

            $result[] = match (true) {
                str_starts_with($key, 'AdsHistory[') => $this->buildAdsCondition($key, $value),
                str_contains($key, 'Has_') => $this->buildBooleanCondition($key, $value),
                default => null,
            };
        }

        return array_values(array_filter($result));
    }

    private function buildBooleanCondition(string $key, string $value): ?array
    {
        $mapping = [
            'Has_Website' => 'has_website',
            'Has_EmailTool' => 'email_marketing_tool',
            'Has_CRM' => 'crm_pipeline',
            'Has_SocialMedia' => 'has_primary_social_channel',
            'Has_SecondarySocial' => 'has_secondary_social_channel',
            'Has_BusinessGoals' => 'business_goals_defined',
            'Has_MarketingGoals' => 'marketing_goals_defined',
            'Has_GBP' => 'google_business_claimed',
            'Has_CoreDirectories' => 'core_directories_claimed',
            'Has_IndustryDirectories' => 'industry_directories_claimed',
            'Has_BusinessDirectories' => 'business_directories_claimed',
        ];

        if (!isset($mapping[$key])) {
            return null;
        }

        $normalizedValue = Str::lower($value) === 'yes';

        return [
            'field' => $mapping[$key],
            'operator' => '=',
            'value' => $normalizedValue,
        ];
    }

    private function buildAdsCondition(string $key, string $value): ?array
    {
        $type = Str::between($key, 'AdsHistory[', ']');

        $map = [
            'Retargeting' => 'retargeting',
            'PaidSearch' => 'paid_search',
            'ProspectingSocial' => 'prospecting_social',
        ];

        if (!isset($map[$type])) {
            return null;
        }

        $normalizedValue = Str::lower($value) === 'yes';

        return [
            'field' => 'running_ads',
            'operator' => $normalizedValue ? '=' : '!=',
            'value' => $map[$type],
        ];
    }
}

