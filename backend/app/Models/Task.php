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
        'document_fields',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dependencies' => 'array',
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

        $group = $data['document_group'] ?? null;
        $data['document_group'] = in_array($group, ['brand', 'planning'], true) ? $group : ($normalized !== [] ? 'planning' : null);

        if ($normalized === [] && empty($data['document_key'])) {
            $data['document_layout'] = null;
            $data['document_group'] = null;
            $data['document_short_label'] = null;
            $data['document_description'] = null;
        }

        return $data;
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

    public static function actionIdPrefix(?string $category): string
    {
        return match ($category) {
            'Goals' => 'G',
            'Digital Marketing Foundations' => 'F',
            'Local SEO' => 'SEO',
            'Content' => 'CON',
            'Social Media' => 'SM',
            'Website' => 'WEB',
            'Email Marketing' => 'EM',
            'Paid Advertising' => 'PA',
            'CRM' => 'CRM',
            default => '',
        };
    }

    public static function categoryFromActionIdPrefix(string $prefix): ?string
    {
        return match (strtoupper($prefix)) {
            'G' => 'Goals',
            'F' => 'Digital Marketing Foundations',
            'SEO' => 'Local SEO',
            'CON' => 'Content',
            'SM' => 'Social Media',
            'WEB' => 'Website',
            'EM' => 'Email Marketing',
            'PA' => 'Paid Advertising',
            'CRM' => 'CRM',
            default => null,
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
