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
    private array $acceptsInputCache = [];

    public function definition(string $kind): array
    {
        $fromTask = $this->definitionFromTasks($kind);
        if ($fromTask) {
            return $fromTask;
        }

        $definition = WorksheetRegistry::get($kind);
        if (! $definition) {
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
                'group' => $definition['group'] ?? $task?->document_group ?? Task::documentGroupForCategory($task?->category) ?? 'marketing-foundations',
                'title' => $task?->title
                    ?: ($definition['titles'][$language] ?? $definition['titles']['en'] ?? $kind),
                'shortLabel' => $task?->document_short_label
                    ?: ($definition['short_label'][$language] ?? $definition['short_label']['en'] ?? ''),
                'description' => $task?->document_description
                    ?: ($definition['description'][$language] ?? $definition['description']['en'] ?? ''),
                'image' => $this->documentCoverUrl($kind, $task),
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
        $planTasks = $this->planTasksForKind($user, $kind);
        $withData = $planTasks->first(function (PlanTask $planTask) use ($kind) {
            return $this->isFilled($kind, $this->documentForPlanTask($kind, $planTask));
        });

        return $withData ?? $planTasks->first();
    }

    public function combinedDocument(User $user, string $kind): array
    {
        $planTasks = $this->planTasksForKind($user, $kind);
        if ($planTasks->isEmpty()) {
            return $this->emptyDocument($kind);
        }

        $definition = $this->definition($kind);
        $rowKeys = $definition['row_keys'] ?? [];
        $labelKey = $rowKeys[0] ?? null;
        $answerKey = $rowKeys[1] ?? null;
        if (! $labelKey || ! $answerKey || ! $this->isAnswerLabel($definition, $answerKey)) {
            $planTask = $this->findPlanTask($user, $kind);

            return $planTask
                ? $this->documentForPlanTask($kind, $planTask)
                : $this->emptyDocument($kind);
        }

        $document = $this->emptyDocument($kind);
        $titleCounts = $planTasks->countBy(fn (PlanTask $planTask) => (string) ($planTask->task?->title ?? ''));
        $sections = [];
        $rows = [];
        foreach ($planTasks->sortBy(fn (PlanTask $planTask) => sprintf('%04d%02d%08d', (int) $planTask->year, (int) $planTask->month, $planTask->id)) as $planTask) {
            $part = $this->documentForPlanTask($kind, $planTask);
            foreach ($document['fields'] ?? [] as $key => $value) {
                $incoming = $part['fields'][$key] ?? '';
                if (trim((string) $value) === '' && trim((string) $incoming) !== '') {
                    $document['fields'][$key] = $incoming;
                }
            }
            $sheet = $this->pairSheetFromTask($planTask->task);
            if ($sheet['labels'] === []) {
                continue;
            }
            $saved = [];
            foreach ($part['rows'] ?? [] as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $label = mb_strtolower(trim((string) ($row[$labelKey] ?? '')));
                if ($label !== '') {
                    $saved[$label] = $row;
                }
            }
            $sectionRows = [];
            $used = [];
            foreach ($sheet['labels'] as $label) {
                $key = mb_strtolower($label);
                $used[$key] = true;
                $sectionRows[] = [
                    $labelKey => $label,
                    $answerKey => (string) ($saved[$key][$answerKey] ?? ''),
                ];
            }
            foreach ($saved as $key => $row) {
                if (isset($used[$key])) {
                    continue;
                }
                $sectionRows[] = [
                    $labelKey => (string) ($row[$labelKey] ?? ''),
                    $answerKey => (string) ($row[$answerKey] ?? ''),
                ];
            }
            $title = (string) ($planTask->task?->title ?? '');
            $sections[] = [
                'plan_task_id' => $planTask->id,
                'title' => $this->sectionTitle($planTask, ($titleCounts[$title] ?? 0) > 1),
                'headers' => $sheet['headers'],
                'rows' => $sectionRows,
            ];
            foreach ($sectionRows as $row) {
                $rows[] = $row;
            }
        }
        $document['sections'] = $sections;
        $document['rows'] = $rows;

        return $document;
    }

    public function saveSections(User $user, string $kind, array $sections): array
    {
        $allowed = $this->planTasksForKind($user, $kind)->keyBy('id');
        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }
            $planTask = $allowed->get((int) ($section['plan_task_id'] ?? 0));
            if (! $planTask) {
                continue;
            }
            $existing = json_decode((string) $planTask->notes, true);
            if (! is_array($existing)) {
                $existing = [];
            }
            $document = $this->normalizeDocument($kind, [
                'fields' => is_array($existing['fields'] ?? null) ? $existing['fields'] : [],
                'rows' => is_array($section['rows'] ?? null) ? $section['rows'] : [],
            ]);
            if (is_array($existing['tables'] ?? null)) {
                $document['tables'] = $existing['tables'];
            }
            $planTask->update([
                'notes' => json_encode($document, JSON_UNESCAPED_UNICODE),
            ]);
        }

        return $this->combinedDocument($user, $kind);
    }

    private function isAnswerLabel(array $definition, string $key): bool
    {
        foreach (['en', 'de'] as $language) {
            $label = mb_strtolower(trim((string) ($definition['labels'][$language][$key] ?? '')));
            if ($label !== '' && preg_match('/^(your |dein(?:e)? )/', $label) === 1) {
                return true;
            }
        }

        return false;
    }

    private function sectionTitle(PlanTask $planTask, bool $withMonth): string
    {
        $title = (string) ($planTask->task?->title ?? '');
        if (! $withMonth || ! $planTask->month) {
            return $title;
        }
        $months = $planTask->task?->language === 'de'
            ? [1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April', 5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember']
            : [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
        $month = $months[(int) $planTask->month] ?? '';
        if ($month === '') {
            return $title;
        }

        return trim($title.' · '.$month.' '.$planTask->year);
    }

    private function pairSheetFromTask(?Task $task): array
    {
        $empty = ['headers' => ['', ''], 'labels' => []];
        $html = (string) ($task?->description ?? '');
        if (! str_contains(strtolower($html), '<table')) {
            return $empty;
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><div id="root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $headers = ['', ''];
        $labels = [];
        foreach ($document->getElementsByTagName('table') as $table) {
            if (! $table instanceof \DOMElement || $this->isExampleTable($table)) {
                continue;
            }
            $tableRows = $this->instructionTableRows($table);
            if (count($tableRows) < 2 || count($tableRows[0]) !== 2 || ! $this->isPairTable($tableRows)) {
                continue;
            }
            if ($headers[0] === '') {
                $headers = [$tableRows[0][0], $tableRows[0][1]];
            }
            foreach (array_slice($tableRows, 1) as $row) {
                $label = trim($row[0] ?? '');
                if ($label !== '' && ! $this->isBlankInstructionCell($label)) {
                    $labels[] = $label;
                }
            }
        }

        return ['headers' => $headers, 'labels' => $labels];
    }

    private function isPairTable(array $tableRows): bool
    {
        $header = mb_strtolower(trim($tableRows[0][1] ?? ''));
        $answer = '/^(your (?:answer|answers|input|plan|notes|hashtags|campaign)|dein(?:e)? (?:antwort|antworten|angaben|notizen|hashtags|kampagne)|dein plan|antwort)$/u';
        if (preg_match($answer, $header) === 1) {
            return true;
        }
        $body = array_slice($tableRows, 1);
        if ($body === []) {
            return false;
        }
        $secondBlank = true;
        $firstLabeled = false;
        foreach ($body as $row) {
            if (! $this->isBlankInstructionCell($row[1] ?? '')) {
                $secondBlank = false;
            }
            if (! $this->isBlankInstructionCell($row[0] ?? '')) {
                $firstLabeled = true;
            }
        }

        return $secondBlank && $firstLabeled;
    }

    private function isExampleTable(\DOMElement $table): bool
    {
        $text = '';
        $sibling = $table->previousSibling;
        while ($sibling) {
            if ($sibling instanceof \DOMElement) {
                $text = trim(preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $sibling->textContent ?? '')) ?? '');
                if ($text !== '') {
                    break;
                }
            }
            $sibling = $sibling->previousSibling;
        }
        if (preg_match('/^(for example|zum beispiel|common examples|häufige beispiele)\b/iu', $text) === 1) {
            return true;
        }

        return $text !== '' && mb_strlen($text) <= 180 && preg_match('/\b(for example|zum beispiel)\b/iu', $text) === 1;
    }

    private function instructionTableRows(\DOMElement $table): array
    {
        $rows = [];
        foreach ($table->getElementsByTagName('tr') as $row) {
            $cells = [];
            foreach ($row->childNodes as $cell) {
                if ($cell instanceof \DOMElement && in_array($cell->nodeName, ['td', 'th'], true)) {
                    $cells[] = $this->instructionCellText($cell);
                }
            }
            if ($cells !== []) {
                $rows[] = $cells;
            }
        }

        return $rows;
    }

    private function planTasksForKind(User $user, string $kind)
    {
        $planIds = Plan::where('user_id', $user->id)->pluck('id');
        if ($planIds->isEmpty()) {
            return collect();
        }

        $slug = WorksheetRegistry::kindToSlug($kind);
        $needles = WorksheetRegistry::get($kind)['title_needles'] ?? [];

        return PlanTask::query()
            ->with('task')
            ->whereIn('plan_id', $planIds)
            ->whereHas('task', function ($query) use ($kind, $slug, $needles) {
                $query->where(function ($inner) use ($kind, $slug, $needles) {
                    $inner->where('document_key', $kind)->orWhere('document_key', $slug);
                    foreach ($needles as $needle) {
                        $inner->orWhereRaw(
                            "LOWER(REPLACE(REPLACE(REPLACE(title, '-', ' '), '–', ' '), '—', ' ')) LIKE ?",
                            ['%'.$needle.'%']
                        );
                    }
                });
            })
            ->orderBy('id')
            ->get();
    }

    public function emptyDocument(string $kind): array
    {
        $definition = $this->definition($kind);
        $document = [
            'type' => $kind,
            'fields' => array_fill_keys($definition['field_keys'] ?? [], ''),
        ];

        if (($definition['row_keys'] ?? []) !== []) {
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
        if (! $notes) {
            return $document;
        }

        $parsed = json_decode($notes, true);
        if (! is_array($parsed)) {
            return $document;
        }

        return $this->normalizeDocument($kind, $parsed);
    }

    public function documentForPlanTask(string $kind, PlanTask $planTask): array
    {
        $document = $this->documentFromNotes($kind, $planTask->notes);

        return $this->mergeInstructionRows($kind, $document, $planTask->task?->description, $planTask->notes);
    }

    private function mergeInstructionRows(string $kind, array $document, ?string $html, ?string $notes): array
    {
        $definition = $this->definition($kind);
        if (($definition['layout'] ?? '') !== 'rows' || $this->rowsHaveAnswers($document) || ! is_string($html) || ! str_contains(strtolower($html), '<table')) {
            return $document;
        }

        $parsed = json_decode((string) $notes, true);
        $savedTables = is_array($parsed['tables'] ?? null) ? $parsed['tables'] : [];
        $rows = $this->rowsFromInstruction($definition, $html, $savedTables);
        if ($rows !== []) {
            $document['rows'] = $rows;
        }

        return $document;
    }

    private function rowsHaveAnswers(array $document): bool
    {
        foreach ($document['rows'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            foreach ($row as $value) {
                if (trim((string) $value) !== '') {
                    return true;
                }
            }
        }

        return false;
    }

    private function rowsFromInstruction(array $definition, string $html, array $savedTables): array
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><div id="root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $rows = [];
        $index = 0;
        foreach ($document->getElementsByTagName('table') as $table) {
            if (! $table instanceof \DOMElement) {
                continue;
            }
            $tableRows = [];
            foreach ($table->getElementsByTagName('tr') as $tableRow) {
                $cells = [];
                foreach ($tableRow->childNodes as $cell) {
                    if ($cell instanceof \DOMElement && in_array($cell->nodeName, ['td', 'th'], true)) {
                        $cells[] = $this->instructionCellText($cell);
                    }
                }
                if ($cells !== []) {
                    $tableRows[] = $cells;
                }
            }
            if (count($tableRows) < 2) {
                continue;
            }
            $keys = [];
            foreach ($tableRows[0] as $header) {
                $keys[] = $this->keyForHeader($header, $definition);
            }
            if (count(array_filter($keys)) < 2 || in_array(null, $keys, true)) {
                continue;
            }
            $heading = '';
            $node = $table->previousSibling;
            while ($node) {
                if ($node instanceof \DOMElement && preg_match('/^h[1-3]$/i', $node->nodeName)) {
                    $heading = trim($node->textContent ?? '');
                    break;
                }
                $node = $node->previousSibling;
            }
            $tableKey = ($heading !== '' ? $heading : 'table').'#'.$index;
            $savedEntry = $savedTables[$tableKey] ?? null;
            if (! is_array($savedEntry) && count($savedTables) === 1) {
                $only = reset($savedTables);
                $savedEntry = is_array($only) ? $only : null;
            }
            $saved = is_array($savedEntry['cells'] ?? null) ? $savedEntry['cells'] : [];
            $index++;
            $needsInput = false;
            foreach (array_slice($tableRows, 1) as $cells) {
                foreach ($keys as $column => $key) {
                    if ($this->isBlankInstructionCell((string) ($cells[$column] ?? ''))) {
                        $needsInput = true;
                        break 2;
                    }
                }
            }
            if (! $needsInput && $saved === []) {
                continue;
            }
            foreach (array_slice($tableRows, 1) as $rowIndex => $cells) {
                $item = [];
                $filled = false;
                foreach ($keys as $column => $key) {
                    $value = $saved[$rowIndex.':'.$column] ?? ($cells[$column] ?? '');
                    if ($this->isInstructionPlaceholder($value)) {
                        $value = '';
                    }
                    $item[$key] = $value;
                    if (trim($value) !== '') {
                        $filled = true;
                    }
                }
                if ($filled) {
                    $rows[] = $item;
                }
            }
        }

        return $rows;
    }

    private function keyForHeader(string $header, array $definition): ?string
    {
        $needle = mb_strtolower(trim($header));
        if ($needle === '') {
            return null;
        }
        $allowed = array_flip($definition['row_keys'] ?? []);
        foreach (['en', 'de'] as $language) {
            foreach ($definition['labels'][$language] ?? [] as $key => $label) {
                if (! isset($allowed[$key])) {
                    continue;
                }
                if (mb_strtolower(trim((string) $label)) === $needle) {
                    return $key;
                }
            }
        }

        return null;
    }

    private function instructionCellText(\DOMElement $cell): string
    {
        $text = html_entity_decode($cell->textContent ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{00A0}", ' ', $text);

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    private function isInstructionPlaceholder(string $value): bool
    {
        return (bool) preg_match('/^(your (?:answer|answers|input|plan|notes|hashtags|campaign)|deine (?:antwort|antworten|angaben|notizen|hashtags|kampagne)|dein plan|antwort)\s*:?$/iu', trim($value));
    }

    private function isBlankInstructionCell(string $value): bool
    {
        $value = trim($value);

        return $value === '' || $this->isInstructionPlaceholder($value) || preg_match('/^(\[your answer\]|\[deine antwort\]|…|\.{3}|-|—)$/iu', $value) === 1;
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

        if (($definition['row_keys'] ?? []) !== []) {
            $rows = is_array($incoming['rows'] ?? null) ? $incoming['rows'] : [];
            $document['rows'] = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
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
                if (! is_array($items)) {
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
        $existing = json_decode((string) $planTask->notes, true);
        if (is_array($existing['tables'] ?? null)) {
            $document['tables'] = $existing['tables'];
        }
        if (is_array($incoming['tables'] ?? null)) {
            $document['tables'] = $incoming['tables'];
        }
        $planTask->update([
            'notes' => json_encode($document, JSON_UNESCAPED_UNICODE),
        ]);

        return $document;
    }

    private function answerRowKeys(array $definition): array
    {
        $keys = array_values(array_filter(
            $definition['row_keys'] ?? [],
            fn ($key) => $key !== 'week'
        ));
        if (count($keys) !== 2) {
            return $keys;
        }
        $label = '';
        foreach (['en', 'de'] as $language) {
            $label = mb_strtolower(trim((string) ($definition['labels'][$language][$keys[1]] ?? '')));
            if ($label !== '') {
                break;
            }
        }
        if (preg_match('/^(your |deine |dein )/', $label) === 1) {
            return [$keys[1]];
        }

        return $keys;
    }

    public function isFilled(string $kind, array $document): bool
    {
        foreach ($document['fields'] ?? [] as $value) {
            if (trim((string) $value) !== '') {
                return true;
            }
        }
        $answerKeys = $this->answerRowKeys($this->definition($kind));
        foreach ($document['rows'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $values = $answerKeys === [] ? $row : array_intersect_key($row, array_flip($answerKeys));
            foreach ($values as $value) {
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

        if (($definition['row_keys'] ?? []) !== []) {
            $rowKeys = $this->answerRowKeys($definition);
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
                $document = $this->combinedDocument($user, $kind);
            } catch (\InvalidArgumentException) {
                continue;
            }
            $schedule = $this->planTasksForKind($user, $kind)
                ->map(fn (PlanTask $item) => [
                    'year' => (int) $item->year,
                    'month' => (int) $item->month,
                    'completed' => (bool) $item->completed,
                ])
                ->values()
                ->all();
            $items[] = [
                'kind' => $kind,
                'slug' => WorksheetRegistry::kindToSlug($kind),
                'filled' => $this->isFilled($kind, $document),
                'completed' => (bool) $planTask?->completed,
                'fill_percent' => $this->fillPercent($kind, $document),
                'updated_at' => optional($planTask?->updated_at)?->toIso8601String(),
                'plan_task_id' => $planTask?->id,
                'schedule' => $schedule,
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
                        'label' => ($copy['labels'][$key] ?? $key).' '.($index + 1),
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

        if (is_array($document['sections'] ?? null) && $document['sections'] !== []) {
            $labelKey = $definition['row_keys'][0] ?? null;
            $answerKey = $definition['row_keys'][1] ?? null;
            foreach ($document['sections'] as $section) {
                if (! is_array($section)) {
                    continue;
                }
                $items = [];
                foreach ($section['rows'] ?? [] as $row) {
                    if (! is_array($row) || ! $labelKey) {
                        continue;
                    }
                    $items[] = [
                        'label' => (string) ($row[$labelKey] ?? ''),
                        'value' => $answerKey ? (string) ($row[$answerKey] ?? '') : '',
                    ];
                }
                $blocks[] = [
                    'title' => (string) ($section['title'] ?? ''),
                    'items' => $items ?: [['label' => '', 'value' => '']],
                ];
            }

            return $blocks;
        }

        if (($definition['row_keys'] ?? []) !== []) {
            foreach ($document['rows'] ?? [] as $index => $row) {
                $items = [];
                foreach ($definition['row_keys'] ?? [] as $key) {
                    $items[] = [
                        'label' => $copy['labels'][$key] ?? $key,
                        'value' => $row[$key] ?? '',
                    ];
                }
                $blocks[] = [
                    'title' => '#'.($index + 1),
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
                'title' => $copy['stepTitles'][$step] ?? ('Step '.$step),
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

        return array_values(array_filter(
            array_unique([...WorksheetRegistry::kinds(), ...$custom]),
            fn (string $kind) => $this->acceptsInput($kind)
        ));
    }

    public function acceptsInput(string $kind): bool
    {
        if (array_key_exists($kind, $this->acceptsInputCache)) {
            return $this->acceptsInputCache[$kind];
        }

        if (WorksheetRegistry::get($kind)) {
            return $this->acceptsInputCache[$kind] = true;
        }

        try {
            $definition = $this->definition($kind);
        } catch (\InvalidArgumentException) {
            return $this->acceptsInputCache[$kind] = false;
        }

        $layout = $definition['layout'] ?? 'fields';
        if ($layout === 'fields') {
            return $this->acceptsInputCache[$kind] = ($definition['field_keys'] ?? []) !== [];
        }
        if ($layout === 'categories') {
            return $this->acceptsInputCache[$kind] = ($definition['category_keys'] ?? []) !== [];
        }
        if (($definition['row_keys'] ?? []) === []) {
            return $this->acceptsInputCache[$kind] = false;
        }

        $slug = WorksheetRegistry::kindToSlug($kind);
        $tasks = Task::query()
            ->where(function ($inner) use ($kind, $slug) {
                $inner->where('document_key', $kind)->orWhere('document_key', $slug);
            })
            ->get();

        if ($tasks->isEmpty()) {
            return $this->acceptsInputCache[$kind] = true;
        }

        foreach ($tasks as $task) {
            $state = $this->instructionInputState($definition, (string) $task->description);
            if ($state !== false) {
                return $this->acceptsInputCache[$kind] = true;
            }
        }

        return $this->acceptsInputCache[$kind] = false;
    }

    public function documentPresentation(Task $task): array
    {
        $kind = str_replace('-', '_', trim((string) $task->document_key));
        $visible = $kind !== '' && $this->hasKind($kind) && $this->acceptsInput($kind);

        return [
            'document_key' => $visible ? $task->document_key : null,
            'document_group' => $visible ? $task->document_group : null,
            'document_layout' => $visible ? $task->document_layout : null,
            'document_short_label' => $visible ? $task->document_short_label : null,
            'document_description' => $visible ? $task->document_description : null,
            'document_fields' => $visible ? $task->document_fields : null,
        ];
    }

    private function instructionInputState(array $definition, string $html): ?bool
    {
        if (! str_contains(strtolower($html), '<table')) {
            return null;
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><div id="root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $matched = false;
        foreach ($document->getElementsByTagName('table') as $table) {
            if (! $table instanceof \DOMElement || $this->isExampleTable($table)) {
                continue;
            }
            $tableRows = $this->instructionTableRows($table);
            if (count($tableRows) < 2) {
                continue;
            }
            $keys = [];
            foreach ($tableRows[0] as $header) {
                $keys[] = $this->keyForHeader($header, $definition);
            }
            if (count(array_filter($keys)) < 2 || in_array(null, $keys, true)) {
                continue;
            }
            $matched = true;
            foreach (array_slice($tableRows, 1) as $cells) {
                foreach ($keys as $column => $key) {
                    if ($this->isBlankInstructionCell((string) ($cells[$column] ?? ''))) {
                        return true;
                    }
                }
            }
        }

        return $matched ? false : null;
    }

    private function documentCoverUrl(string $kind, ?Task $preferred): ?string
    {
        $path = $preferred?->document_image;
        if (! is_string($path) || $path === '') {
            $slug = WorksheetRegistry::kindToSlug($kind);
            $path = Task::query()
                ->where(function ($inner) use ($kind, $slug) {
                    $inner->where('document_key', $kind)->orWhere('document_key', $slug);
                })
                ->whereNotNull('document_image')
                ->where('document_image', '!=', '')
                ->value('document_image');
        }

        return Task::documentCoverUrl(is_string($path) ? $path : null);
    }

    private function taskForKind(string $kind, ?string $language = null): ?Task
    {
        $slug = WorksheetRegistry::kindToSlug($kind);
        $query = Task::query()
            ->where(function ($inner) use ($kind, $slug) {
                $inner->where('document_key', $kind)->orWhere('document_key', $slug);
            });

        if ($language) {
            $query->orderByRaw('CASE WHEN language = ? THEN 0 ELSE 1 END', [$language]);
        }

        $query->orderBy('id');

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

        if (! $task) {
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
            if (! is_array($field)) {
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
                if (! is_array($field)) {
                    continue;
                }
                $key = str_replace('-', '_', Str::slug((string) ($field['key'] ?? ''), '_'));
                if ($key === '' || ! isset($labels[$relatedLang])) {
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
            'group' => $task->document_group ?: Task::documentGroupForCategory($task->category) ?: 'marketing-foundations',
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
