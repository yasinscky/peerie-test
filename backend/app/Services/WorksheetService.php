<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\PlanTask;
use App\Models\Task;
use App\Models\User;
use App\Support\SimpleDocx;
use App\Support\SimplePdf;
use Illuminate\Support\Str;

class WorksheetService
{
    public function definition(string $kind): array
    {
        $fromTask = $this->definitionFromTasks($kind);
        if ($fromTask) {
            return $fromTask;
        }

        $definition = WorksheetRegistry::get($kind);
        if (!$definition) {
            throw new \InvalidArgumentException('Unknown worksheet');
        }

        return $definition;
    }

    public function hasKind(string $kind): bool
    {
        if (WorksheetRegistry::get($kind)) {
            return true;
        }

        return $this->definitionFromTasks($kind) !== null;
    }

    public function catalog(string $language): array
    {
        $language = $language === 'de' ? 'de' : 'en';
        $items = [];
        foreach ($this->allKinds() as $kind) {
            try {
                $definition = $this->definition($kind);
            } catch (\InvalidArgumentException) {
                continue;
            }
            $task = $this->taskForKind($kind, $language);
            $items[] = [
                'kind' => $kind,
                'slug' => WorksheetRegistry::kindToSlug($kind),
                'group' => $definition['group'] ?? $task?->document_group ?? 'planning',
                'title' => $task?->title
                    ?: ($definition['titles'][$language] ?? $definition['titles']['en'] ?? $kind),
                'shortLabel' => $task?->document_short_label
                    ?: ($definition['short_label'][$language] ?? $definition['short_label']['en'] ?? ''),
                'description' => $task?->document_description
                    ?: ($definition['description'][$language] ?? $definition['description']['en'] ?? ''),
            ];
        }

        return $items;
    }

    public function frontendSchema(string $kind, string $language): array
    {
        $definition = $this->definition($kind);
        $lang = $language === 'de' ? 'de' : 'en';
        $labels = $definition['labels'][$lang] ?? $definition['labels']['en'] ?? [];
        $fields = [];
        foreach ($definition['field_keys'] ?? [] as $key) {
            $step = 1;
            foreach ($definition['sections'] ?? [] as $stepNumber => $keys) {
                if (in_array($key, $keys, true)) {
                    $step = (int) $stepNumber;
                    break;
                }
            }
            $fields[] = [
                'key' => $key,
                'type' => $definition['field_types'][$key] ?? 'textarea',
                'step' => $step,
                'rows' => 4,
            ];
        }

        return [
            'kind' => $kind,
            'layout' => $definition['layout'] ?? 'fields',
            'fields' => $fields,
            'categoryKeys' => $definition['category_keys'] ?? [],
            'rowKeys' => $definition['row_keys'] ?? [],
            'stepTitles' => $definition['step_titles'][$lang] ?? $definition['step_titles']['en'] ?? [],
            'labels' => $labels,
            'placeholders' => [
                'idea' => $lang === 'de' ? 'Neue Idee…' : 'New idea...',
            ],
            'title' => $definition['titles'][$lang] ?? $definition['titles']['en'] ?? $kind,
            'intro' => $definition['description'][$lang] ?? $definition['description']['en'] ?? '',
        ];
    }

    public function findPlanTask(User $user, string $kind): ?PlanTask
    {
        $planIds = Plan::where('user_id', $user->id)->pluck('id');
        if ($planIds->isEmpty()) {
            return null;
        }

        $slug = WorksheetRegistry::kindToSlug($kind);
        $needles = WorksheetRegistry::get($kind)['title_needles'] ?? [];
        $planTasks = PlanTask::query()
            ->with('task')
            ->whereIn('plan_id', $planIds)
            ->whereHas('task', function ($query) use ($kind, $slug, $needles) {
                $query->where(function ($inner) use ($kind, $slug, $needles) {
                    $inner->where('document_key', $kind)->orWhere('document_key', $slug);
                    foreach ($needles as $needle) {
                        $inner->orWhereRaw(
                            "LOWER(REPLACE(REPLACE(REPLACE(title, '-', ' '), '–', ' '), '—', ' ')) LIKE ?",
                            ['%' . $needle . '%']
                        );
                    }
                });
            })
            ->orderBy('id')
            ->get();

        $withData = $planTasks->first(function (PlanTask $planTask) use ($kind) {
            return $this->isFilled($kind, $this->documentFromNotes($kind, $planTask->notes));
        });

        return $withData ?? $planTasks->first();
    }

    public function emptyDocument(string $kind): array
    {
        $definition = $this->definition($kind);
        $document = [
            'type' => $kind,
            'fields' => array_fill_keys($definition['field_keys'] ?? [], ''),
        ];

        if (($definition['layout'] ?? '') === 'rows') {
            $document['rows'] = [];
        }

        if (($definition['layout'] ?? '') === 'categories') {
            $document['categories'] = [];
            foreach ($definition['category_keys'] ?? [] as $key) {
                $document['categories'][$key] = [''];
            }
        }

        return $document;
    }

    public function documentFromNotes(string $kind, ?string $notes): array
    {
        $document = $this->emptyDocument($kind);
        if (!$notes) {
            return $document;
        }

        $parsed = json_decode($notes, true);
        if (!is_array($parsed)) {
            return $document;
        }

        return $this->normalizeDocument($kind, $parsed);
    }

    public function normalizeDocument(string $kind, array $incoming): array
    {
        $definition = $this->definition($kind);
        $document = $this->emptyDocument($kind);
        $fields = is_array($incoming['fields'] ?? null) ? $incoming['fields'] : $incoming;

        foreach ($definition['field_keys'] ?? [] as $key) {
            $value = $fields[$key] ?? '';
            $document['fields'][$key] = is_string($value) ? $value : '';
        }

        if (($definition['layout'] ?? '') === 'rows') {
            $rows = is_array($incoming['rows'] ?? null) ? $incoming['rows'] : [];
            $document['rows'] = [];
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $normalized = [];
                $hasValue = false;
                foreach ($definition['row_keys'] ?? [] as $key) {
                    $value = is_string($row[$key] ?? null) ? $row[$key] : '';
                    $normalized[$key] = $value;
                    if (trim($value) !== '') {
                        $hasValue = true;
                    }
                }
                if ($hasValue) {
                    $document['rows'][] = $normalized;
                }
            }
        }

        if (($definition['layout'] ?? '') === 'categories') {
            $incomingCategories = is_array($incoming['categories'] ?? null) ? $incoming['categories'] : [];
            foreach ($definition['category_keys'] ?? [] as $key) {
                $items = $incomingCategories[$key] ?? [''];
                if (!is_array($items)) {
                    $items = [''];
                }
                $clean = [];
                foreach ($items as $item) {
                    $clean[] = is_string($item) ? $item : '';
                }
                $document['categories'][$key] = $clean !== [] ? $clean : [''];
            }
        }

        return $document;
    }

    public function saveDocument(PlanTask $planTask, string $kind, array $incoming): array
    {
        $document = $this->normalizeDocument($kind, $incoming);
        $planTask->update([
            'notes' => json_encode($document, JSON_UNESCAPED_UNICODE),
        ]);

        return $document;
    }

    public function isFilled(string $kind, array $document): bool
    {
        foreach ($document['fields'] ?? [] as $value) {
            if (trim((string) $value) !== '') {
                return true;
            }
        }
        foreach ($document['rows'] ?? [] as $row) {
            foreach ($row as $value) {
                if (trim((string) $value) !== '') {
                    return true;
                }
            }
        }
        foreach ($document['categories'] ?? [] as $items) {
            foreach ($items as $value) {
                if (trim((string) $value) !== '') {
                    return true;
                }
            }
        }

        return false;
    }

    public function fillPercent(string $kind, array $document): int
    {
        $definition = $this->definition($kind);
        $filled = 0;
        $total = 0;

        foreach ($definition['field_keys'] ?? [] as $key) {
            $total++;
            if (trim((string) ($document['fields'][$key] ?? '')) !== '') {
                $filled++;
            }
        }

        if (($definition['layout'] ?? '') === 'rows') {
            $rowKeys = $definition['row_keys'] ?? [];
            $rows = $document['rows'] ?? [];
            if ($rows === []) {
                $total += count($rowKeys);
            } else {
                foreach ($rows as $row) {
                    foreach ($rowKeys as $key) {
                        $total++;
                        if (trim((string) ($row[$key] ?? '')) !== '') {
                            $filled++;
                        }
                    }
                }
            }
        }

        if (($definition['layout'] ?? '') === 'categories') {
            foreach ($definition['category_keys'] ?? [] as $key) {
                $total++;
                $hasValue = false;
                foreach ($document['categories'][$key] ?? [] as $item) {
                    if (trim((string) $item) !== '') {
                        $hasValue = true;
                        break;
                    }
                }
                if ($hasValue) {
                    $filled++;
                }
            }
        }

        if ($total === 0) {
            return 0;
        }

        return (int) round(($filled / $total) * 100);
    }

    public function statusForUser(User $user): array
    {
        $items = [];
        foreach ($this->allKinds() as $kind) {
            try {
                $planTask = $this->findPlanTask($user, $kind);
                $document = $planTask
                    ? $this->documentFromNotes($kind, $planTask->notes)
                    : $this->emptyDocument($kind);
            } catch (\InvalidArgumentException) {
                continue;
            }
            $items[] = [
                'kind' => $kind,
                'slug' => WorksheetRegistry::kindToSlug($kind),
                'filled' => $this->isFilled($kind, $document),
                'completed' => (bool) $planTask?->completed,
                'fill_percent' => $this->fillPercent($kind, $document),
                'updated_at' => optional($planTask?->updated_at)?->toIso8601String(),
                'plan_task_id' => $planTask?->id,
            ];
        }

        return $items;
    }

    public function export(string $kind, array $document, string $language, string $format): string
    {
        $blocks = $this->documentBlocks($kind, $document, $language);
        $definition = $this->definition($kind);
        $title = $definition['titles'][$language] ?? $definition['titles']['en'];

        if ($format === 'docx') {
            return SimpleDocx::fromBlocks($title, $blocks);
        }

        return SimplePdf::fromBlocks($title, $blocks);
    }

    public function filename(string $kind): string
    {
        return $this->definition($kind)['filename'];
    }

    public function copy(string $kind, string $language): array
    {
        $definition = $this->definition($kind);
        $lang = $language === 'de' ? 'de' : 'en';

        return [
            'title' => $definition['titles'][$lang],
            'filename' => $definition['filename'],
            'stepTitles' => $definition['step_titles'][$lang] ?? [],
            'labels' => $definition['labels'][$lang] ?? [],
        ];
    }

    private function documentBlocks(string $kind, array $document, string $language): array
    {
        $definition = $this->definition($kind);
        $copy = $this->copy($kind, $language);
        $blocks = [];

        if (($definition['layout'] ?? '') === 'categories') {
            foreach ($definition['category_keys'] ?? [] as $key) {
                $items = [];
                foreach ($document['categories'][$key] ?? [] as $index => $value) {
                    $items[] = [
                        'label' => ($copy['labels'][$key] ?? $key) . ' ' . ($index + 1),
                        'value' => $value,
                    ];
                }
                $blocks[] = [
                    'title' => $copy['labels'][$key] ?? $key,
                    'items' => $items ?: [['label' => $copy['labels'][$key] ?? $key, 'value' => '']],
                ];
            }

            return $blocks;
        }

        if (($definition['layout'] ?? '') === 'rows') {
            foreach ($document['rows'] ?? [] as $index => $row) {
                $items = [];
                foreach ($definition['row_keys'] ?? [] as $key) {
                    $items[] = [
                        'label' => $copy['labels'][$key] ?? $key,
                        'value' => $row[$key] ?? '',
                    ];
                }
                $blocks[] = [
                    'title' => '#' . ($index + 1),
                    'items' => $items,
                ];
            }
        }

        foreach ($definition['sections'] ?? [] as $step => $keys) {
            if ($keys === []) {
                continue;
            }
            $items = [];
            foreach ($keys as $key) {
                $items[] = [
                    'label' => $copy['labels'][$key] ?? $key,
                    'value' => $document['fields'][$key] ?? '',
                ];
            }
            $blocks[] = [
                'title' => $copy['stepTitles'][$step] ?? ('Step ' . $step),
                'items' => $items,
            ];
        }

        return $blocks;
    }

    private function allKinds(): array
    {
        $custom = Task::query()
            ->whereNotNull('document_key')
            ->where('document_key', '!=', '')
            ->pluck('document_key')
            ->map(fn ($key) => str_replace('-', '_', (string) $key))
            ->unique()
            ->filter(fn ($kind) => $this->hasKind($kind))
            ->values()
            ->all();

        return array_values(array_unique([...WorksheetRegistry::kinds(), ...$custom]));
    }

    private function taskForKind(string $kind, ?string $language = null): ?Task
    {
        $slug = WorksheetRegistry::kindToSlug($kind);
        $query = Task::query()
            ->where(function ($inner) use ($kind, $slug) {
                $inner->where('document_key', $kind)->orWhere('document_key', $slug);
            })
            ->orderBy('id');

        if ($language) {
            $query->orderByRaw('CASE WHEN language = ? THEN 0 ELSE 1 END', [$language]);
        }

        return $query->first();
    }

    private function definitionFromTasks(string $kind): ?array
    {
        $task = Task::query()
            ->where(function ($inner) use ($kind) {
                $slug = WorksheetRegistry::kindToSlug($kind);
                $inner->where('document_key', $kind)->orWhere('document_key', $slug);
            })
            ->whereNotNull('document_fields')
            ->orderBy('id')
            ->get()
            ->first(function (Task $task) {
                return is_array($task->document_fields) && $task->document_fields !== [];
            });

        if (!$task) {
            return null;
        }

        return $this->definitionFromTask($task, $kind);
    }

    private function definitionFromTask(Task $task, string $kind): array
    {
        $layout = in_array($task->document_layout, ['fields', 'rows', 'categories'], true)
            ? $task->document_layout
            : 'fields';
        $keys = [];
        $labels = ['en' => [], 'de' => []];
        $types = [];
        $sections = [];
        $lang = $task->language === 'de' ? 'de' : 'en';

        foreach ($task->document_fields ?? [] as $field) {
            if (!is_array($field)) {
                continue;
            }
            $key = str_replace('-', '_', Str::slug((string) ($field['key'] ?? ''), '_'));
            if ($key === '') {
                continue;
            }
            $keys[] = $key;
            $label = (string) ($field['label'] ?? $key);
            $labels['en'][$key] = $label;
            $labels['de'][$key] = $label;
            $types[$key] = ($field['type'] ?? 'text') === 'textarea' ? 'textarea' : 'text';
            if ($layout === 'fields') {
                $step = max(1, (int) ($field['step'] ?? 1));
                $sections[$step][] = $key;
            }
        }

        $other = Task::query()
            ->where(function ($inner) use ($task) {
                $inner->where('document_key', $task->document_key);
            })
            ->where('id', '!=', $task->id)
            ->whereNotNull('document_fields')
            ->get();

        foreach ($other as $related) {
            $relatedLang = $related->language === 'de' ? 'de' : 'en';
            foreach ($related->document_fields ?? [] as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $key = str_replace('-', '_', Str::slug((string) ($field['key'] ?? ''), '_'));
                if ($key === '' || !isset($labels[$relatedLang])) {
                    continue;
                }
                $labels[$relatedLang][$key] = (string) ($field['label'] ?? $key);
            }
        }

        $title = $task->title ?: $kind;
        $relatedTitle = $other->firstWhere('language', $lang === 'de' ? 'en' : 'de');

        return [
            'layout' => $layout,
            'filename' => Str::slug($task->document_key ?: $kind),
            'title_needles' => [],
            'titles' => [
                'en' => $lang === 'en' ? $title : ($relatedTitle?->title ?: $title),
                'de' => $lang === 'de' ? $title : ($relatedTitle?->title ?: $title),
            ],
            'field_keys' => $layout === 'fields' ? $keys : [],
            'category_keys' => $layout === 'categories' ? $keys : [],
            'row_keys' => $layout === 'rows' ? $keys : [],
            'field_types' => $types,
            'sections' => $sections,
            'step_titles' => ['en' => [], 'de' => []],
            'labels' => $labels,
            'group' => $task->document_group ?: 'planning',
            'short_label' => [
                'en' => $lang === 'en' ? (string) $task->document_short_label : '',
                'de' => $lang === 'de' ? (string) $task->document_short_label : '',
            ],
            'description' => [
                'en' => $lang === 'en' ? (string) $task->document_description : '',
                'de' => $lang === 'de' ? (string) $task->document_description : '',
            ],
        ];
    }
}
