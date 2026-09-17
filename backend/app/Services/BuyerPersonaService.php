<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\PlanTask;
use App\Models\User;
use App\Support\SimpleDocx;
use App\Support\SimplePdf;

class BuyerPersonaService
{
    public const FIELD_KEYS = [
        'name',
        'portrait',
        'age_range',
        'location_work_model',
        'role_industry',
        'seniority_team',
        'income_band',
        'life_situation',
        'pain_1',
        'pain_2',
        'pain_3',
        'pain_4',
        'pain_5',
        'short_term_goal',
        'deeper_motivation',
        'success_criteria',
        'platforms',
        'search_queries',
        'content_preferences',
        'budget',
        'value_perception',
        'decision_process',
        'objections',
        'persona_summary',
    ];

    public static function isBuyerPersonaTitle(?string $title): bool
    {
        $normalized = str_replace('-', ' ', mb_strtolower((string) $title));

        return str_contains($normalized, 'buyer persona');
    }

    public function findPlanTask(User $user): ?PlanTask
    {
        $planIds = Plan::where('user_id', $user->id)->pluck('id');

        if ($planIds->isEmpty()) {
            return null;
        }

        $planTasks = PlanTask::query()
            ->with('task')
            ->whereIn('plan_id', $planIds)
            ->whereHas('task', function ($query) {
                $query->whereRaw(
                    "LOWER(REPLACE(title, '-', ' ')) LIKE ?",
                    ['%buyer persona%']
                );
            })
            ->orderBy('id')
            ->get();

        $withData = $planTasks->first(function (PlanTask $planTask) {
            $fields = $this->fieldsFromNotes($planTask->notes);

            return collect($fields)->contains(fn ($value) => trim((string) $value) !== '');
        });

        return $withData ?? $planTasks->first();
    }

    public function emptyFields(): array
    {
        return array_fill_keys(self::FIELD_KEYS, '');
    }

    public function fieldsFromNotes(?string $notes): array
    {
        $fields = $this->emptyFields();

        if (!$notes) {
            return $fields;
        }

        $parsed = json_decode($notes, true);
        if (!is_array($parsed) || !isset($parsed['fields']) || !is_array($parsed['fields'])) {
            return $fields;
        }

        foreach ($parsed['fields'] as $key => $value) {
            if (in_array($key, self::FIELD_KEYS, true)) {
                $fields[$key] = is_string($value) ? $value : '';
            }
        }

        return $fields;
    }

    public function normalizeFields(array $incoming): array
    {
        $fields = $this->emptyFields();

        foreach (self::FIELD_KEYS as $key) {
            $value = $incoming[$key] ?? '';
            $fields[$key] = is_string($value) ? $value : '';
        }

        return $fields;
    }

    public function saveFields(PlanTask $planTask, array $fields): array
    {
        $normalized = $this->normalizeFields($fields);

        $planTask->update([
            'notes' => json_encode([
                'type' => 'buyer_persona',
                'fields' => $normalized,
            ], JSON_UNESCAPED_UNICODE),
        ]);

        return $normalized;
    }

    public function isFilled(array $fields): bool
    {
        return collect($fields)->contains(fn ($value) => trim((string) $value) !== '');
    }

    public function copy(string $language): array
    {
        if ($language === 'de') {
            return [
                'title' => 'Buyer Persona Vorlage',
                'filename' => 'Buyer-Persona',
                'stepTitles' => [
                    1 => 'Schritt 1: Demografie',
                    2 => 'Schritt 2: Schmerzpunkte',
                    3 => 'Schritt 3: Ziele & Motive',
                    4 => 'Schritt 4: Online-Verhalten & Kanäle',
                    5 => 'Schritt 5: Zahlungsbereitschaft & Entscheidungsweg',
                    6 => 'Schritt 6: Persona schreiben',
                ],
                'labels' => [
                    'name' => 'Name',
                    'portrait' => '2–3 Sätze zur idealen Person',
                    'age_range' => 'Altersspanne',
                    'location_work_model' => 'Ort & Arbeitsmodell',
                    'role_industry' => 'Rolle & Branche',
                    'seniority_team' => 'Seniorität & Teamgröße',
                    'income_band' => 'Einkommensband',
                    'life_situation' => 'Lebenssituation',
                    'pain_1' => 'Schmerzpunkt 1',
                    'pain_2' => 'Schmerzpunkt 2',
                    'pain_3' => 'Schmerzpunkt 3',
                    'pain_4' => 'Schmerzpunkt 4 (optional)',
                    'pain_5' => 'Schmerzpunkt 5 (optional)',
                    'short_term_goal' => 'Kurzfristiges Ziel (3–6 Monate)',
                    'deeper_motivation' => 'Tiefere Motivation',
                    'success_criteria' => 'Erfolgskriterien',
                    'platforms' => 'Plattformen',
                    'search_queries' => 'Suchanfragen',
                    'content_preferences' => 'Content-Vorlieben',
                    'budget' => 'Budget',
                    'value_perception' => 'Wertwahrnehmung',
                    'decision_process' => 'Entscheidung',
                    'objections' => 'Einwände',
                    'persona_summary' => 'Persona-Text',
                ],
            ];
        }

        return [
            'title' => 'Buyer Persona Template',
            'filename' => 'Buyer-Persona',
            'stepTitles' => [
                1 => 'Step 1: Demographics',
                2 => 'Step 2: Pain points',
                3 => 'Step 3: Goals & motives',
                4 => 'Step 4: Online behaviour & channels',
                5 => 'Step 5: Willingness to pay & decision path',
                6 => 'Step 6: Write the persona',
            ],
            'labels' => [
                'name' => 'Name',
                'portrait' => '2–3 sentences about your ideal person',
                'age_range' => 'Age range',
                'location_work_model' => 'Location & work model',
                'role_industry' => 'Role & industry',
                'seniority_team' => 'Seniority & team size',
                'income_band' => 'Income band',
                'life_situation' => 'Life situation',
                'pain_1' => 'Pain point 1',
                'pain_2' => 'Pain point 2',
                'pain_3' => 'Pain point 3',
                'pain_4' => 'Pain point 4 (optional)',
                'pain_5' => 'Pain point 5 (optional)',
                'short_term_goal' => 'Short-term goal (3–6 months)',
                'deeper_motivation' => 'Deeper motivation',
                'success_criteria' => 'Success criteria',
                'platforms' => 'Platforms',
                'search_queries' => 'Search queries',
                'content_preferences' => 'Content preferences',
                'budget' => 'Budget',
                'value_perception' => 'Value perception',
                'decision_process' => 'Decision process',
                'objections' => 'Objections',
                'persona_summary' => 'Persona write-up',
            ],
        ];
    }

    public function sections(): array
    {
        return [
            1 => ['name', 'portrait', 'age_range', 'location_work_model', 'role_industry', 'seniority_team', 'income_band', 'life_situation'],
            2 => ['pain_1', 'pain_2', 'pain_3', 'pain_4', 'pain_5'],
            3 => ['short_term_goal', 'deeper_motivation', 'success_criteria'],
            4 => ['platforms', 'search_queries', 'content_preferences'],
            5 => ['budget', 'value_perception', 'decision_process', 'objections'],
            6 => ['persona_summary'],
        ];
    }

    public function documentBlocks(array $fields, string $language): array
    {
        $copy = $this->copy($language);
        $blocks = [];

        foreach ($this->sections() as $step => $keys) {
            $items = [];
            foreach ($keys as $key) {
                $items[] = [
                    'label' => $copy['labels'][$key],
                    'value' => $fields[$key] ?? '',
                ];
            }
            $blocks[] = [
                'title' => $copy['stepTitles'][$step],
                'items' => $items,
            ];
        }

        return $blocks;
    }

    public function exportPdf(array $fields, string $language): string
    {
        $copy = $this->copy($language);
        $blocks = $this->documentBlocks($fields, $language);

        return SimplePdf::fromBlocks($copy['title'], $blocks);
    }

    public function exportDocx(array $fields, string $language): string
    {
        $copy = $this->copy($language);
        $blocks = $this->documentBlocks($fields, $language);

        return SimpleDocx::fromBlocks($copy['title'], $blocks);
    }
}
