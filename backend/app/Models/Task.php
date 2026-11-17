<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'action_id',
        'title',
        'short_description',
        'description',
        'duration_minutes',
        'frequency',
        'preferred_week',
        'eligibility_delay_months',
        'eligibility_after_action_ids',
        'dependencies',
        'language',
        'is_local',
        'requires_website',
        'category',
        'global_order',
        'is_global',
        'target_countries',
        'target_industries',
        'allowed_capacities',
        'local_presence_options',
        'conditions',
        'prerequisites',
        'template',
        'document_key',
        'document_group',
        'document_layout',
        'document_short_label',
        'document_description',
        'document_image',
        'document_fields',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dependencies' => 'array',
        'eligibility_after_action_ids' => 'array',
        'is_local' => 'boolean',
        'requires_website' => 'boolean',
        'is_global' => 'boolean',
        'target_countries' => 'array',
        'target_industries' => 'array',
        'allowed_capacities' => 'array',
        'conditions' => 'array',
        'prerequisites' => 'array',
        'document_fields' => 'array',
    ];

    public static function normalizeDocumentPayload(array $data): array
    {
        $fields = is_array($data['document_fields'] ?? null) ? $data['document_fields'] : [];
        $normalized = [];
        foreach ($fields as $field) {
            if (! is_array($field)) {
                continue;
            }
            $key = trim((string) ($field['key'] ?? ''));
            $label = trim((string) ($field['label'] ?? ''));
            if ($key === '' && $label !== '') {
                $key = str_replace('-', '_', Str::slug($label, '_'));
            }
            $key = str_replace('-', '_', Str::slug($key, '_'));
            if ($key === '') {
                continue;
            }
            $type = ($field['type'] ?? 'text') === 'textarea' ? 'textarea' : 'text';
            $normalized[] = [
                'key' => $key,
                'type' => $type,
                'step' => max(1, (int) ($field['step'] ?? 1)),
                'label' => $label !== '' ? $label : $key,
            ];
        }
        $data['document_fields'] = $normalized;

        $documentKey = trim((string) ($data['document_key'] ?? ''));
        if ($documentKey === '' && $normalized !== []) {
            $documentKey = (string) ($data['title'] ?? 'document');
        }
        $data['document_key'] = $documentKey !== ''
            ? Str::slug($documentKey)
            : null;

        $layout = $data['document_layout'] ?? null;
        $data['document_layout'] = in_array($layout, ['fields', 'rows', 'categories'], true) ? $layout : ($normalized !== [] ? 'fields' : null);

        $group = trim((string) ($data['document_group'] ?? ''));
        if (! array_key_exists($group, self::documentGroups())) {
            $group = self::documentGroupForCategory($data['category'] ?? null) ?? '';
        }
        $data['document_group'] = $group !== '' ? $group : null;

        $image = $data['document_image'] ?? null;
        if (is_array($image)) {
            $image = $image[0] ?? null;
        }
        $data['document_image'] = is_string($image) && trim($image) !== '' ? trim($image) : null;

        if ($normalized === [] && empty($data['document_key'])) {
            $data['document_layout'] = null;
            $data['document_group'] = null;
            $data['document_short_label'] = null;
            $data['document_description'] = null;
            $data['document_image'] = null;
        }

        return $data;
    }

    public static function documentCoverUrl(?string $path): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return '/document-covers/'.ltrim($path, '/');
    }

    public function syncDocumentCover(): void
    {
        $key = trim((string) $this->document_key);
        if ($key === '') {
            return;
        }

        $keys = array_values(array_unique([
            $key,
            str_replace('_', '-', $key),
            str_replace('-', '_', $key),
        ]));

        static::query()
            ->where('id', '!=', $this->id)
            ->whereIn('document_key', $keys)
            ->update(['document_image' => $this->document_image]);
    }

    protected function getLocalPresenceOptionsAttribute($value)
    {
        if (is_array($value)) {
            return ! empty($value) ? $value[0] : 'any';
        }

        return $value ?? 'any';
    }

    protected function getTemplateAttribute($value)
    {
        if (is_array($value)) {
            return ! empty($value) ? $value[0] : 'no';
        }

        return $value ?? 'no';
    }

    public static function prerequisiteConditions(): array
    {
        return [
            'business_goals_defined' => 'Business goals defined',
            'marketing_goals_defined' => 'Marketing goals defined',
            'google_business_claimed' => 'Google Business Profile claimed & filled out',
            'core_directories_claimed' => 'Apple Business and Bing Places',
            'industry_directories_claimed' => 'Industry directories claimed',
            'business_directories_claimed' => 'Business directories claimed',
            'has_website' => 'Website in place',
            'email_marketing_tool' => 'Email marketing tool in place',
            'crm_pipeline' => 'CRM or simple pipeline to track leads',
            'has_primary_social_channel' => 'Primary social media channel',
            'has_secondary_social_channel' => 'Secondary social media channel',
            'has_paid_ads' => 'Paid ads in place',
            'has_paid_search' => 'Paid search ads in place',
            'has_paid_social_awareness' => 'Paid social awareness ads in place',
            'has_paid_social_leads' => 'Paid social lead ads in place',
            'has_paid_social_retargeting' => 'Paid social retargeting ads in place',
        ];
    }

    public static function normalizeSchedulingPayload(array $data): array
    {
        $week = $data['preferred_week'] ?? null;
        $data['preferred_week'] = in_array((int) $week, [1, 2, 3, 4], true) && $week !== null && $week !== ''
            ? (int) $week
            : null;

        $months = $data['eligibility_delay_months'] ?? null;
        $data['eligibility_delay_months'] = $months === null || $months === ''
            ? null
            : max(1, (int) $months);

        $actionIds = $data['eligibility_after_action_ids'] ?? null;
        if (! is_array($actionIds)) {
            $actionIds = [];
        }
        $actionIds = array_values(array_filter(array_map(
            fn ($id) => strtoupper(trim((string) $id)),
            $actionIds
        )));
        $data['eligibility_after_action_ids'] = $actionIds === [] ? null : $actionIds;

        return $data;
    }

    public static function documentGroups(): array
    {
        return [
            'goals' => 'Goals',
            'marketing-foundations' => 'Marketing Foundations',
            'digital-marketing-foundations' => 'Digital Marketing Foundations',
            'local-seo' => 'Local SEO',
            'content' => 'Content',
            'social-media' => 'Social Media',
            'website' => 'Website',
            'email-marketing' => 'Email Marketing',
            'paid-advertising' => 'Paid Advertising',
            'leads-clients' => 'Leads & Clients',
            'crm' => 'CRM',
        ];
    }

    public static function documentGroupForCategory(?string $category): ?string
    {
        $group = array_search($category, self::documentGroups(), true);

        return $group === false ? null : $group;
    }

    public static function actionIdPrefix(?string $category): string
    {
        return match ($category) {
            'Goals' => 'G',
            'Marketing Foundations', 'Digital Marketing Foundations' => 'F',
            'Local SEO' => 'SEO',
            'Content' => 'CON',
            'Social Media' => 'SM',
            'Website' => 'WEB',
            'Email Marketing' => 'EM',
            'Paid Advertising' => 'AD',
            'Leads & Clients', 'CRM' => 'CRM',
            default => '',
        };
    }

    public static function categoryFromActionIdPrefix(string $prefix): ?string
    {
        return self::categoriesForActionPrefix($prefix)[0] ?? null;
    }

    public static function categoriesForActionPrefix(string $prefix): array
    {
        return match (strtoupper($prefix)) {
            'G' => ['Goals'],
            'F' => ['Marketing Foundations', 'Digital Marketing Foundations'],
            'SEO' => ['Local SEO'],
            'CON' => ['Content'],
            'SM' => ['Social Media'],
            'WEB' => ['Website'],
            'EM' => ['Email Marketing'],
            'PA', 'AD' => ['Paid Advertising'],
            'CRM' => ['Leads & Clients', 'CRM'],
            default => [],
        };
    }

    public function formattedActionId(): string
    {
        $number = is_numeric($this->action_id) ? (int) $this->action_id : null;

        if ($number === null) {
            return '—';
        }

        $prefix = self::actionIdPrefix($this->category);
        $formattedNumber = str_pad((string) $number, 3, '0', STR_PAD_LEFT);

        return $prefix !== ''
            ? $prefix.'-'.$formattedNumber
            : $formattedNumber;
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_tasks')
            ->withPivot(['id', 'week', 'year', 'month', 'completed', 'notes'])
            ->withTimestamps();
    }

    /**
     * Get dependency tasks
     */
    public function dependencyTasks()
    {
        if (empty($this->dependencies)) {
            return collect();
        }

        return Task::whereIn('id', $this->dependencies)->get();
    }

    /**
     * Check if task can be executed (all dependencies are completed)
     */
    public function canBeExecuted(array $completedTaskIds = []): bool
    {
        if (empty($this->dependencies)) {
            return true;
        }

        return empty(array_diff($this->dependencies, $completedTaskIds));
    }
}
