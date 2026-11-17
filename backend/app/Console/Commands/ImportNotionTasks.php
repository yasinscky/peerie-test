<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\PlanTask;
use App\Models\Task;
use App\Services\NotionInstructionHtml;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportNotionTasks extends Command
{
    protected $signature = 'tasks:import-notion {path : Path to the Notion CSV or zip export}';

    protected $description = 'Import Notion strategy blocks into tasks without changing plan generation';

    private array $markdownHtml = [];

    public function handle(): int
    {
        $path = $this->argument('path');
        if (! is_readable($path)) {
            $this->error("Export not readable: {$path}");

            return self::FAILURE;
        }

        $directory = $this->resolveDirectory($path);
        $groups = $directory ? $this->markdownPages($directory) : [];
        if ($groups === []) {
            $csvPath = $this->prepareExport($path);
            $groups = $this->groups($this->rows($csvPath));
        }
        $tasks = Task::query()->orderBy('id')->get();
        $claimed = [];
        $updated = 0;
        $created = 0;
        $createdCodes = [];
        $descriptions = 0;
        $moved = 0;
        $deleted = 0;
        $orphaned = 0;

        DB::transaction(function () use ($groups, $tasks, &$claimed, &$updated, &$created, &$createdCodes, &$descriptions, &$moved, &$deleted, &$orphaned) {
            $applied = [];
            foreach ($groups as $index => $group) {
                $match = $this->exactTask($group, $tasks, $claimed);
                if (! $match) {
                    continue;
                }
                $this->fillExisting($match, $group);
                if (! empty($group['html'])) {
                    $descriptions++;
                }
                $match->save();
                $claimed[$match->id] = $group['code'];
                $applied[$index] = true;
                $updated++;
            }

            $pending = [];
            foreach ($groups as $index => $group) {
                if (! isset($applied[$index])) {
                    $pending[] = [$index, $group];
                }
            }
            usort($pending, function (array $left, array $right) {
                $leftSize = count($left[1]['countries']) + count($left[1]['industries']);
                $rightSize = count($right[1]['countries']) + count($right[1]['industries']);

                return $leftSize <=> $rightSize;
            });

            foreach ($pending as [$index, $group]) {
                $match = $this->overlapTask($group, $tasks, $claimed);
                if ($match) {
                    $this->fillExisting($match, $group);
                    if (! empty($group['html'])) {
                        $descriptions++;
                    }
                    $match->save();
                    $claimed[$match->id] = $group['code'];
                    $updated++;

                    continue;
                }

                $createdTask = Task::query()->create($this->newAttributes($group));
                $claimed[$createdTask->id] = $group['code'];
                $created++;
                if (! empty($group['html'])) {
                    $descriptions++;
                }
                $createdCodes[$group['code'].'|'.$group['language'].'|'.$group['frequency']] = true;
            }

            $hasHtml = false;
            foreach ($groups as $group) {
                if (! empty($group['html'])) {
                    $hasHtml = true;
                    break;
                }
            }
            if (! $hasHtml) {
                $this->copyFullDescriptions();
            }

            $this->copyMissingDocumentFields();
            $moved = $this->reassignPivots($claimed);
            $orphaned = PlanTask::query()->whereNotIn('task_id', array_keys($claimed))->count();
            $deleted = Task::query()->whereNotIn('id', array_keys($claimed))->delete();
        });

        $this->info('Pages: '.count($groups));
        $this->info("Updated rows: {$updated}");
        $this->info("Created rows: {$created}");
        $this->info("Full instructions written: {$descriptions}");
        $this->info("Plan tasks moved: {$moved}");
        $this->info("Plan tasks removed with extra cards: {$orphaned}");
        $this->info("Removed extra tasks: {$deleted}");
        $this->info('New ActionIDs: '.implode(', ', array_keys($createdCodes)));

        return self::SUCCESS;
    }

    private function prepareExport(string $path): string
    {
        if (! str_ends_with(strtolower($path), '.zip')) {
            return $path;
        }

        $directory = storage_path('app/notion-import');
        if (is_dir($directory)) {
            $this->deleteDirectory($directory);
        }
        mkdir($directory, 0777, true);

        $zip = new \ZipArchive;
        if ($zip->open($path) !== true) {
            throw new \RuntimeException("Cannot open export zip: {$path}");
        }
        $zip->extractTo($directory);
        $zip->close();

        $this->indexMarkdown($directory);

        $csvPath = null;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '_all.csv')) {
                $csvPath = $file->getPathname();
                break;
            }
        }

        if ($csvPath === null) {
            throw new \RuntimeException('Notion export has no CSV.');
        }

        return $csvPath;
    }

    private function indexMarkdown(string $directory): void
    {
        $converter = new NotionInstructionHtml;
        $entries = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.md')) {
                continue;
            }

            $raw = file_get_contents($file->getPathname());
            if (! is_string($raw) || ! preg_match('/^#\s+([A-Z]+-\d+)/m', $raw, $idMatch)) {
                continue;
            }

            $splitAt = strpos($raw, "\n# ", 1);
            $header = $splitAt === false ? $raw : substr($raw, 0, $splitAt);
            $body = $converter->body($raw);
            if (trim($body) === '') {
                continue;
            }

            $language = str_contains($header, 'Language: German') ? 'de' : 'en';
            preg_match('/^Industry:\s*(.+)$/m', $header, $industryMatch);
            $industries = $this->industries($industryMatch[1] ?? '');
            $entries[] = [
                'code' => strtoupper($idMatch[1]),
                'language' => $language,
                'industries' => $industries,
                'html' => $converter->convert($body),
            ];
        }

        usort($entries, fn (array $left, array $right) => count($right['industries']) <=> count($left['industries']));
        foreach ($entries as $entry) {
            foreach ($entry['industries'] as $industry) {
                $this->markdownHtml[$entry['code'].'|'.$entry['language'].'|'.$industry] = $entry['html'];
            }
        }
    }

    private function resolveDirectory(string $path): ?string
    {
        if (is_dir($path)) {
            return $path;
        }
        if (str_ends_with(strtolower($path), '.zip')) {
            $this->prepareExport($path);

            return storage_path('app/notion-import');
        }
        $directory = dirname($path);
        if ($this->containsMarkdown($directory)) {
            return $directory;
        }
        $parent = dirname($directory);
        if ($this->containsMarkdown($parent)) {
            return $parent;
        }

        return null;
    }

    private function containsMarkdown(string $directory): bool
    {
        if (! is_dir($directory)) {
            return false;
        }
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.md')) {
                return true;
            }
        }

        return false;
    }

    private function markdownPages(string $directory): array
    {
        $converter = new NotionInstructionHtml;
        $parsed = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.md')) {
                continue;
            }
            $raw = file_get_contents($file->getPathname());
            if (! is_string($raw) || ! preg_match('/^#\s+([A-Z]+-\d+)/m', $raw, $idMatch)) {
                continue;
            }
            $second = strpos($raw, "\n# ", 1);
            $header = $second === false ? $raw : substr($raw, 0, $second);
            $props = [];
            foreach (preg_split("/\r\n|\n|\r/", $header) ?: [] as $line) {
                if (preg_match('/^(Action|Cadence|CapacityAllowed|Category|Country|Documents|Document name|EffortMin|GlobalOrder|Industry|Language|LocalPresence|Prerequisites|Preferred week of the month):\s*(.*)$/', $line, $property)) {
                    $props[$property[1]] = trim($property[2]);
                }
            }
            $code = $this->normalizeCode($idMatch[1]);
            $language = strcasecmp($props['Language'] ?? '', 'German') === 0 ? 'de' : 'en';
            $body = $converter->body($raw);
            $title = trim($props['Action'] ?? '');
            $parsed[] = [
                'code' => $code,
                'language' => $language,
                'props' => $props,
                'title' => $title,
                'html' => trim($body) === '' ? '<p>'.e($title).'</p>' : $converter->convert($body),
            ];
        }

        $englishDocuments = [];
        foreach ($parsed as $page) {
            $name = trim($page['props']['Document name'] ?? '');
            if ($page['language'] === 'en' && $name !== '' && ! isset($englishDocuments[$page['code']])) {
                $englishDocuments[$page['code']] = $name;
            }
        }

        $pages = [];
        foreach ($parsed as $page) {
            $props = $page['props'];
            [$prerequisites, $eligibility] = $this->rules($props['Prerequisites'] ?? '');
            $documentName = trim($props['Document name'] ?? '');
            $documents = strcasecmp(trim($props['Documents'] ?? ''), 'Yes') === 0;
            $prefix = explode('-', $page['code'])[0];
            $category = trim($props['Category'] ?? '');
            if ($category === '') {
                $category = (string) (Task::categoryFromActionIdPrefix($prefix) ?? '');
            }
            preg_match('/(\d+)$/', $page['code'], $number);
            $industries = $this->industries($props['Industry'] ?? '');
            $pages[] = [
                'code' => $page['code'],
                'action_id' => (int) ($number[1] ?? 0),
                'language' => $page['language'],
                'industry' => $industries[0] ?? '',
                'industries' => $industries,
                'title' => $page['title'],
                'category' => $category,
                'instruction' => '',
                'frequency' => $this->frequency($props['Cadence'] ?? ''),
                'capacities' => $this->capacities($props['CapacityAllowed'] ?? ''),
                'countries' => $this->countries($props['Country'] ?? ''),
                'effort' => (int) trim($props['EffortMin'] ?? '') ?: null,
                'global_order' => (int) trim($props['GlobalOrder'] ?? '') ?: null,
                'local_presence' => strcasecmp(trim($props['LocalPresence'] ?? ''), 'Yes') === 0 ? 'yes' : 'any',
                'prerequisites' => $prerequisites,
                'eligibility' => $eligibility,
                'week' => $this->week($props['Preferred week of the month'] ?? ''),
                'documents' => $documents,
                'document_name' => $documentName,
                'document_key' => Str::slug($englishDocuments[$page['code']] ?? $documentName),
                'html' => $page['html'],
            ];
        }

        return $pages;
    }

    private function deleteDirectory(string $directory): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($directory);
    }

    private function rows(string $path): array
    {
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);

            return [];
        }
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        $rows = [];
        while (($line = fgetcsv($handle)) !== false) {
            $row = [];
            foreach ($header as $index => $column) {
                $row[$column] = $line[$index] ?? '';
            }
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    private function groups(array $rows): array
    {
        $englishDocuments = [];
        foreach ($rows as $row) {
            $code = trim($row['ActionID'] ?? '');
            $name = trim($row['Document name'] ?? '');
            if ($code !== '' && $name !== '' && strcasecmp(trim($row['Language'] ?? ''), 'English') === 0) {
                $englishDocuments[$code] = $name;
            }
        }

        $groups = [];
        foreach ($rows as $row) {
            $code = trim($row['ActionID'] ?? '');
            if (! preg_match('/^([A-Z]+)-(\d+)$/i', $code, $matches)) {
                continue;
            }
            $language = strcasecmp(trim($row['Language'] ?? ''), 'German') === 0 ? 'de' : 'en';
            $industries = $this->industries($row['Industry'] ?? '');
            $countries = $this->countries($row['Country'] ?? '');
            [$prerequisites, $eligibility] = $this->rules($row['Prerequisites'] ?? '');
            $week = $this->week($row['Preferred week of the month'] ?? '');
            $documentName = trim($row['Document name'] ?? '');
            $documents = strcasecmp(trim($row['Documents'] ?? ''), 'Yes') === 0;
            $frequency = $this->frequency($row['Cadence'] ?? '');
            $capacities = $this->capacities($row['CapacityAllowed'] ?? '');

            foreach ($industries as $industry) {
                $key = $code.'|'.$language.'|'.$industry.'|'.implode(',', $countries).'|'.$frequency.'|'.implode(',', $capacities);
                $candidate = [
                    'code' => $this->normalizeCode($code),
                    'action_id' => (int) $matches[2],
                    'language' => $language,
                    'industry' => $industry,
                    'industries' => [$industry],
                    'title' => trim($row['Action'] ?? ''),
                    'category' => trim($row['Category'] ?? ''),
                    'instruction' => trim($row['Instruction'] ?? ''),
                    'frequency' => $this->frequency($row['Cadence'] ?? ''),
                    'capacities' => $this->capacities($row['CapacityAllowed'] ?? ''),
                    'countries' => $countries,
                    'effort' => (int) trim($row['EffortMin'] ?? '') ?: null,
                    'global_order' => (int) trim($row['GlobalOrder'] ?? '') ?: null,
                    'local_presence' => strcasecmp(trim($row['LocalPresence'] ?? ''), 'Yes') === 0 ? 'yes' : 'any',
                    'prerequisites' => $prerequisites,
                    'eligibility' => $eligibility,
                    'week' => $week,
                    'documents' => $documents,
                    'document_name' => $documentName,
                    'document_key' => Str::slug($englishDocuments[$code] ?? $documentName),
                    'html' => $this->markdownHtml[$code.'|'.$language.'|'.$industry] ?? null,
                ];
                if (! isset($groups[$key]) || mb_strlen($candidate['instruction']) > mb_strlen($groups[$key]['instruction'])) {
                    $groups[$key] = $candidate;
                } else {
                    $groups[$key]['countries'] = array_values(array_unique([...$groups[$key]['countries'], ...$countries]));
                    $groups[$key]['week'] = $groups[$key]['week'] ?? $week;
                    $groups[$key]['eligibility'] = $groups[$key]['eligibility'] ?? $eligibility;
                    if (empty($groups[$key]['html']) && ! empty($candidate['html'])) {
                        $groups[$key]['html'] = $candidate['html'];
                    }
                }
            }
        }

        uasort($groups, fn (array $a, array $b) => [$a['code'], $a['language'], $a['industry']] <=> [$b['code'], $b['language'], $b['industry']]);

        return array_values($groups);
    }

    private function copyFullDescriptions(): void
    {
        $donors = [];
        Task::query()
            ->where('description', 'like', '%<%')
            ->orderByDesc('id')
            ->get(['action_id', 'category', 'language', 'title', 'description'])
            ->each(function (Task $task) use (&$donors) {
                if (mb_strlen($task->description) < 1000) {
                    return;
                }
                $key = $task->action_id.'|'.$task->category.'|'.$task->language.'|'.$task->title;
                if (! isset($donors[$key]) || mb_strlen($task->description) > mb_strlen($donors[$key])) {
                    $donors[$key] = $task->description;
                }
            });

        Task::query()->orderBy('id')->get()->each(function (Task $task) use ($donors) {
            $key = $task->action_id.'|'.$task->category.'|'.$task->language.'|'.$task->title;
            if (! isset($donors[$key]) || mb_strlen($task->description) >= 1000) {
                return;
            }
            $task->description = $donors[$key];
            $task->save();
        });
    }

    private function matches($group, $tasks, array $claimed): array
    {
        $needles = $this->aliases()[$group['code'].'|'.$group['language']] ?? null;
        $found = [];
        foreach ($tasks as $task) {
            if (isset($claimed[$task->id]) && $claimed[$task->id] !== $group['code']) {
                continue;
            }
            if (! $this->sameCategory($task->category, $group['category'])) {
                continue;
            }
            if (! $this->industryOverlaps($task, $group['industry'])) {
                continue;
            }

            $exact = $this->normalize($task->title) === $this->normalize($group['title']);
            if (! $exact && $task->language !== $group['language']) {
                continue;
            }

            $score = 0.0;
            similar_text($this->normalize($group['title']), $this->normalize($task->title), $score);
            if ($exact) {
                $score = 100;
            }

            if ($needles !== null) {
                $normalized = $this->normalize($task->title);
                $hit = $exact;
                if (! $hit) {
                    foreach ($needles as $needle) {
                        if (str_contains($normalized, $needle)) {
                            $hit = true;
                            break;
                        }
                    }
                }
                if (! $hit) {
                    continue;
                }
            } elseif (! $exact && ($score < 93 || $this->reviewMismatch($group['title'], $task->title))) {
                continue;
            }

            $found[] = ['task' => $task, 'score' => $score];
        }

        if ($found === []) {
            return [];
        }

        $best = max(array_column($found, 'score'));
        $floor = $best - 8;

        return array_values(array_map(
            fn (array $item) => $item['task'],
            array_filter($found, fn (array $item) => $item['score'] >= $floor)
        ));
    }

    private function fillExisting(Task $task, array $group): void
    {
        $task->action_id = $group['action_id'];
        $task->title = $group['title'];
        $task->category = $group['category'];
        $task->language = $group['language'];
        $task->frequency = $group['frequency'];
        $task->global_order = $group['global_order'];
        $task->duration_minutes = $group['effort'];
        $task->allowed_capacities = $group['capacities'];
        $task->local_presence_options = $group['local_presence'];
        $task->target_countries = $group['countries'];
        $task->target_industries = $group['industries'] ?? [$group['industry']];
        $task->prerequisites = $group['prerequisites'];
        $task->preferred_week = $group['week'];
        $task->eligibility_delay_months = $group['eligibility']['months'] ?? null;
        $task->eligibility_after_action_ids = $group['eligibility']['after'] ?? null;
        $task->template = $group['documents'] ? 'yes' : 'no';
        if ($group['documents']) {
            $task->document_group = Task::documentGroupForCategory($group['category']);
        }
        if ($group['document_name'] !== '') {
            $task->document_short_label = $group['document_name'];
        }
        if ($group['documents'] && $group['document_key'] !== '' && blank($task->document_key)) {
            $task->document_key = $group['document_key'];
        }
        if (! empty($group['html'])) {
            $task->description = $group['html'];
        }
    }

    private function newAttributes(array $group): array
    {
        $description = '';
        foreach (preg_split("/\n+/", $group['instruction']) ?: [] as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') {
                continue;
            }
            $description .= '<p>'.e($paragraph).'</p>';
        }

        return [
            'action_id' => $group['action_id'],
            'title' => $group['title'],
            'category' => $group['category'],
            'language' => $group['language'],
            'frequency' => $group['frequency'],
            'global_order' => $group['global_order'],
            'duration_minutes' => $group['effort'],
            'allowed_capacities' => $group['capacities'],
            'target_countries' => $group['countries'],
            'target_industries' => $group['industries'] ?? [$group['industry']],
            'local_presence_options' => $group['local_presence'],
            'prerequisites' => $group['prerequisites'],
            'preferred_week' => $group['week'],
            'eligibility_delay_months' => $group['eligibility']['months'] ?? null,
            'eligibility_after_action_ids' => $group['eligibility']['after'] ?? null,
            'description' => $group['html'] ?? ($description !== '' ? $description : '<p>'.e($group['title']).'</p>'),
            'template' => $group['documents'] ? 'yes' : 'no',
            'document_group' => $group['documents'] ? Task::documentGroupForCategory($group['category']) : null,
            'document_short_label' => $group['document_name'] !== '' ? $group['document_name'] : null,
            'document_key' => $group['documents'] && $group['document_key'] !== '' ? $group['document_key'] : null,
            'is_local' => false,
            'requires_website' => false,
            'is_global' => false,
        ];
    }

    private function aliases(): array
    {
        return [
            'CON-020|de' => ['content fur den kommenden monat'],
            'CRM-10|de' => ['minimales crm'],
            'CRM-30|de' => ['empfehlungssystem'],
            'CRM-40|de' => ['treueprogramm'],
            'EM-010|de' => ['e mail marketing tool einrichten'],
            'EM-020|de' => ['willkommensserie'],
            'EM-030|de' => ['monatlichen newsletter vorbereiten'],
            'F-030|de' => ['wettbewerbsanalyse durch'],
            'F-040|en' => ['personal company brand'],
            'F-060|de' => ['marketing taktiken'],
            'F-060|en' => ['digital marketing tactics'],
            'F-081|de' => ['monatliche kennzahlen'],
            'SEO-030|de' => ['apple', 'bing'],
            'SEO-040|de' => ['apple', 'bing'],
            'SEO-050|de' => ['beauty branchen', 'coaching spezifische', 'physiotherapie verzeichnisse'],
            'SEO-050|en' => ['industry directory', 'industry specific', 'medical directory'],
            'SEO-070|en' => ['ask for reviews'],
            'SEO-070|de' => ['kundenbewertungen'],
            'SEO-090|en' => ['messaging'],
            'SEO-090|de' => ['nachrichtenfunktion', 'buchungsoptionen'],
            'SEO-100|de' => ['updates im google'],
            'SEO-110|de' => ['deutsche branchenverzeichnisse'],
            'SEO-130|en' => ['nap audit'],
            'SEO-130|de' => ['nap audit'],
            'SM-050|de' => ['hashtag liste'],
            'SM-050|en' => ['hashtag list'],
            'SM-060|en' => ['customer testimonials'],
            'SM-060|de' => ['kundenstimmen anfragen'],
            'SM-070|en' => ['secondary channel'],
            'SM-070|de' => ['secondary channel', 'zweiten social'],
            'WEB-010|de' => ['website landingpage'],
            'WEB-020|de' => ['unternehmenswebsite'],
            'WEB-030|en' => ['privacy'],
            'WEB-030|de' => ['datenschutz'],
            'WEB-100|en' => ['booking'],
            'WEB-100|de' => ['buchung'],
            'WEB-110|de' => ['local seo'],
            'WEB-131|en' => ['update keywords'],
            'WEB-131|de' => ['aktualisiere deine keywords'],
            'WEB-140|de' => ['erfolgsgeschichte'],
            'WEB-150|en' => ['publish new blog'],
            'WEB-170|en' => ['whatsapp', 'messaging on your website'],
        ];
    }

    private function sameCategory(?string $dbCategory, string $notionCategory): bool
    {
        $canon = [
            'Digital Marketing Foundations' => 'foundations',
            'Marketing Foundations' => 'foundations',
            'CRM' => 'leads',
            'Leads & Clients' => 'leads',
        ];

        return ($canon[$dbCategory] ?? $dbCategory) === ($canon[$notionCategory] ?? $notionCategory);
    }

    private function industryOverlaps(Task $task, string $industry): bool
    {
        $industries = array_map(
            fn ($value) => strtolower(trim((string) $value)),
            is_array($task->target_industries) ? $task->target_industries : []
        );
        if (in_array('all', $industries, true) || in_array('all industries', $industries, true)) {
            return true;
        }

        return in_array($industry, $industries, true);
    }

    private function reviewMismatch(string $notionTitle, string $dbTitle): bool
    {
        $notion = $this->normalize($notionTitle);
        $db = $this->normalize($dbTitle);
        $notionReview = str_contains($notion, 'review')
            || str_contains($notion, 'uberprufe')
            || str_contains($notion, 'optimise')
            || str_contains($notion, 'optimize')
            || str_contains($notion, 'optimiere');
        if (! $notionReview) {
            return false;
        }

        return ! str_contains($db, 'review')
            && ! str_contains($db, 'uberprufe')
            && ! str_contains($db, 'prufen')
            && ! str_contains($db, 'update')
            && ! str_contains($db, 'aktualisiere');
    }

    private function rules(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [[], null];
        }

        $eligibility = null;
        if (preg_match('/^(.*?)\s+or\s+(FirstEligibleAfter:.*)$/i', $raw, $matches)) {
            $raw = trim($matches[1]);
            $eligibility = $this->eligibility($matches[2]);
        } elseif (stripos($raw, 'FirstEligibleAfter:') === 0) {
            return [[], $this->eligibility($raw)];
        }

        if (! preg_match('/^Has_([A-Za-z0-9_]+)=(Yes|No)$/', $raw, $matches)) {
            return [[], $eligibility];
        }

        return [[
            [
                'condition' => $this->condition($matches[1]),
                'value' => strtolower($matches[2]),
            ],
        ], $eligibility];
    }

    private function eligibility(string $raw): ?array
    {
        if (! preg_match('/FirstEligibleAfter:\s*(\d+)\s+months?\s+after\s+(.+?)\s+completion/i', $raw, $matches)) {
            return null;
        }
        preg_match_all('/[A-Z]{1,3}-?\d+/i', $matches[2], $ids);
        $after = array_values(array_unique(array_map(
            fn ($id) => $this->normalizeCode((string) $id),
            $ids[0] ?? []
        )));
        sort($after);

        return [
            'months' => (int) $matches[1],
            'after' => $after,
        ];
    }

    private function condition(string $name): string
    {
        return match ($name) {
            'BusinessGoals' => 'business_goals_defined',
            'MarketingGoals' => 'marketing_goals_defined',
            'GBP' => 'google_business_claimed',
            'IndustryDirectories' => 'industry_directories_claimed',
            'BusinessDirectories' => 'business_directories_claimed',
            'BusinessProfiles' => 'core_directories_claimed',
            'Website' => 'has_website',
            'EmailTool' => 'email_marketing_tool',
            'CRM' => 'crm_pipeline',
            'SocialMedia' => 'has_primary_social_channel',
            'SecondarySocial' => 'has_secondary_social_channel',
            'PaidAds' => 'has_paid_ads',
            'PaidSearch' => 'has_paid_search',
            'PaidSocial_Awareness' => 'has_paid_social_awareness',
            'PaidSocial_Leads' => 'has_paid_social_leads',
            'PaidSocial_Retargeting' => 'has_paid_social_retargeting',
            default => Str::snake($name),
        };
    }

    private function industries(string $raw): array
    {
        $map = [
            'beauty' => 'beauty',
            'physio' => 'physio',
            'coach' => 'coaching',
            'coaching' => 'coaching',
        ];
        $industries = [];
        foreach (explode(',', $raw) as $part) {
            $key = strtolower(trim($part));
            if ($key === '') {
                continue;
            }
            $industries[] = $map[$key] ?? $key;
        }

        $industries = array_values(array_unique($industries));
        $order = ['beauty' => 0, 'coaching' => 1, 'physio' => 2];
        usort($industries, fn (string $left, string $right) => ($order[$left] ?? 9) <=> ($order[$right] ?? 9) ?: strcmp($left, $right));

        return $industries;
    }

    private function countries(string $raw): array
    {
        $map = [
            'uk' => 'UK',
            'ire' => 'IRE',
            'ie' => 'IRE',
            'de' => 'DE',
        ];
        $countries = [];
        foreach (explode(',', $raw) as $part) {
            $key = strtolower(trim($part));
            if ($key === '') {
                continue;
            }
            $countries[] = $map[$key] ?? strtoupper($key);
        }

        $countries = array_values(array_unique($countries));
        $order = ['UK' => 0, 'IRE' => 1, 'DE' => 2];
        usort($countries, fn (string $left, string $right) => ($order[$left] ?? 9) <=> ($order[$right] ?? 9) ?: strcmp($left, $right));

        return $countries;
    }

    private function frequency(string $raw): string
    {
        return match (strtolower(trim($raw))) {
            'weekly' => 'weekly',
            'bi-weekly', 'biweekly' => 'bi_weekly',
            'monthly' => 'monthly',
            'quarterly' => 'quarterly',
            'half-yearly', 'half yearly', 'half_yearly' => 'half_yearly',
            'yearly' => 'yearly',
            'bi_weekly' => 'bi_weekly',
            'once' => 'once',
            default => 'once',
        };
    }

    private function capacities(string $raw): array
    {
        preg_match_all('/(\d+)\s*h/i', $raw, $matches);
        $capacities = array_values(array_unique(array_map('intval', $matches[1] ?? [])));
        sort($capacities);

        return $capacities;
    }

    private function week(string $raw): ?int
    {
        if (preg_match('/week\s*([1-4])/i', $raw, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower($value);
        $value = strtr($value, [
            'ä' => 'a',
            'ö' => 'o',
            'ü' => 'u',
            'ß' => 'ss',
            '&' => ' and ',
        ]);
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }

    private function normalizeCode(string $code): string
    {
        $code = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $code) ?? '');
        if (! preg_match('/^([A-Z]+)(\d+)$/', $code, $matches)) {
            return strtoupper(trim($code));
        }
        $prefix = $matches[1] === 'PA' ? 'AD' : $matches[1];

        return $prefix.'-'.str_pad($matches[2], 3, '0', STR_PAD_LEFT);
    }

    private function industrySet(array $values): array
    {
        return $this->industries(implode(',', $values));
    }

    private function sameIdentity(Task $task, array $group): bool
    {
        if ($this->normalizeCode($task->formattedActionId()) !== $group['code']) {
            return false;
        }
        if ($task->language !== $group['language']) {
            return false;
        }
        if ($this->frequency((string) $task->frequency) !== $group['frequency']) {
            return false;
        }
        $capacities = array_map('intval', is_array($task->allowed_capacities) ? $task->allowed_capacities : []);
        sort($capacities);

        return $capacities === $group['capacities'];
    }

    private function exactTask(array $group, $tasks, array $claimed): ?Task
    {
        foreach ($tasks as $task) {
            if (isset($claimed[$task->id]) || ! $this->sameIdentity($task, $group)) {
                continue;
            }
            if ($this->industrySet($task->target_industries ?? []) !== $group['industries']) {
                continue;
            }
            if ($this->countries(implode(',', $task->target_countries ?? [])) !== $group['countries']) {
                continue;
            }

            return $task;
        }

        return null;
    }

    private function overlapTask(array $group, $tasks, array $claimed): ?Task
    {
        $best = null;
        $bestScore = null;
        foreach ($tasks as $task) {
            if (isset($claimed[$task->id]) || ! $this->sameIdentity($task, $group)) {
                continue;
            }
            $industries = $this->industrySet($task->target_industries ?? []);
            $countries = $this->countries(implode(',', $task->target_countries ?? []));
            if (! $this->overlaps($industries, $group['industries']) || ! $this->overlaps($countries, $group['countries'])) {
                continue;
            }
            $score = 0;
            if ($industries === $group['industries']) {
                $score += 4;
            }
            if ($countries === $group['countries']) {
                $score += 4;
            }
            if ($this->sameHtml($task, $group)) {
                $score += 8;
            }
            $score -= count(array_diff($countries, $group['countries']));
            $score -= count(array_diff($industries, $group['industries']));
            if ($bestScore === null || $score > $bestScore) {
                $best = $task;
                $bestScore = $score;
            }
        }

        return $best;
    }

    private function overlaps(array $left, array $right): bool
    {
        return $left !== [] && $right !== [] && array_intersect($left, $right) !== [];
    }

    private function sameHtml(Task $task, array $group): bool
    {
        $left = preg_replace('/\s+/', '', str_replace(' border="1"', '', (string) $task->description)) ?? '';
        $right = preg_replace('/\s+/', '', str_replace(' border="1"', '', (string) ($group['html'] ?? ''))) ?? '';

        return $left === $right;
    }

    private function copyMissingDocumentFields(): void
    {
        $donors = [];
        Task::query()->whereNotNull('document_key')->orderBy('id')->each(function (Task $task) use (&$donors) {
            $fields = $task->document_fields;
            if (! is_array($fields) || $fields === [] || $task->document_key === null || $task->document_key === '') {
                return;
            }
            $donors[$task->document_key.'|'.$task->language] ??= [
                'document_fields' => $fields,
                'document_layout' => $task->document_layout,
                'document_group' => $task->document_group,
            ];
        });

        Task::query()->whereNotNull('document_key')->orderBy('id')->each(function (Task $task) use ($donors) {
            $fields = $task->document_fields;
            if (is_array($fields) && $fields !== []) {
                return;
            }
            $donor = $donors[$task->document_key.'|'.$task->language] ?? null;
            if ($donor === null) {
                return;
            }
            $task->document_fields = $donor['document_fields'];
            $task->document_layout = $task->document_layout ?: $donor['document_layout'];
            $task->document_group = $task->document_group ?: $donor['document_group'];
            $task->save();
        });
    }

    private function reassignPivots(array $keepIds): int
    {
        $moved = 0;
        $plans = Plan::query()->get()->keyBy('id');
        $tasks = Task::query()->whereIn('id', array_keys($keepIds))->get();
        PlanTask::query()->orderBy('id')->get()->each(function (PlanTask $pivot) use ($plans, $tasks, $keepIds, &$moved) {
            $current = Task::query()->find($pivot->task_id);
            $plan = $plans->get($pivot->plan_id);
            if (! $plan || ! $current) {
                return;
            }
            $kept = isset($keepIds[$pivot->task_id]);
            if ($kept && $this->taskFitsPlan($current, $plan)) {
                return;
            }
            $replacement = null;
            $bestFit = null;
            foreach ($tasks as $candidate) {
                if ($candidate->id === $current->id || ! $this->sameAction($candidate, $current) || ! $this->taskFitsPlan($candidate, $plan)) {
                    continue;
                }
                $fit = count($candidate->target_industries ?? []) + count($candidate->target_countries ?? []);
                if ($bestFit === null || $fit < $bestFit) {
                    $replacement = $candidate;
                    $bestFit = $fit;
                }
            }
            if (! $replacement) {
                return;
            }
            $conflict = PlanTask::query()
                ->where('plan_id', $pivot->plan_id)
                ->where('task_id', $replacement->id)
                ->where('year', $pivot->year)
                ->where('month', $pivot->month)
                ->first();
            if ($conflict) {
                if ($pivot->completed && ! $conflict->completed) {
                    $conflict->completed = true;
                    $conflict->last_completed_at = $pivot->last_completed_at;
                    $conflict->save();
                }
                $pivot->delete();
                $moved++;

                return;
            }
            $pivot->task_id = $replacement->id;
            $pivot->save();
            $moved++;
        });

        return $moved;
    }

    private function sameAction(Task $candidate, Task $current): bool
    {
        return $this->normalizeCode($candidate->formattedActionId()) === $this->normalizeCode($current->formattedActionId())
            && $candidate->language === $current->language
            && $this->frequency((string) $candidate->frequency) === $this->frequency((string) $current->frequency);
    }

    private function taskFitsPlan(Task $task, Plan $plan): bool
    {
        if ($task->language !== $plan->language) {
            return false;
        }
        $taskCountries = $this->countries(implode(',', $task->target_countries ?? []));
        if ($taskCountries !== [] && ! in_array($this->planCountry($plan), $taskCountries, true)) {
            return false;
        }
        $planIndustries = array_map(static fn ($value) => strtolower(trim((string) $value)), is_array($plan->industries) ? $plan->industries : []);
        $taskIndustries = $this->industrySet($task->target_industries ?? []);
        if ($planIndustries !== [] && $taskIndustries !== [] && array_intersect($planIndustries, $taskIndustries) === []) {
            return false;
        }
        $capacities = array_map('intval', is_array($task->allowed_capacities) ? $task->allowed_capacities : []);
        if ($capacities !== [] && ! in_array((int) $plan->marketing_time_per_week, $capacities, true)) {
            return false;
        }
        if ($task->local_presence_options === 'yes' && ! $plan->is_local_business) {
            return false;
        }

        return true;
    }

    private function planCountry(Plan $plan): string
    {
        return match (strtolower(trim((string) $plan->country))) {
            'uk', 'gb', 'united kingdom' => 'UK',
            'ie', 'ire', 'ireland' => 'IRE',
            'de', 'ger', 'germany' => 'DE',
            default => strtoupper(trim((string) $plan->country)),
        };
    }
}
