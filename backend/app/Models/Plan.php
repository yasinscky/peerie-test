<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'country',
        'language',
        'is_local_business',
        'has_website',
        'marketing_time_per_week',
        'questionnaire_data',
        'industries',
        'business_goals_defined',
        'marketing_goals_defined',
        'google_business_claimed',
        'core_directories_claimed',
        'industry_directories_claimed',
        'business_directories_claimed',
        'email_marketing_tool',
        'crm_pipeline',
        'running_ads',
        'has_primary_social_channel',
        'primary_social_channel',
        'has_secondary_social_channel',
        'secondary_social_channel',
    ];

    protected $casts = [
        'is_local_business' => 'boolean',
        'has_website' => 'boolean',
        'questionnaire_data' => 'array',
        'industries' => 'array',
        'business_goals_defined' => 'boolean',
        'marketing_goals_defined' => 'boolean',
        'google_business_claimed' => 'boolean',
        'core_directories_claimed' => 'boolean',
        'industry_directories_claimed' => 'boolean',
        'business_directories_claimed' => 'boolean',
        'email_marketing_tool' => 'boolean',
        'crm_pipeline' => 'boolean',
        'running_ads' => 'array',
        'has_primary_social_channel' => 'boolean',
        'has_secondary_social_channel' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'plan_tasks')
                    ->withPivot(['id', 'week', 'completed', 'notes'])
                    ->withTimestamps();
    }

    public function getTotalTasksAttribute(): int
    {
        return $this->tasks()->count();
    }

    public function getCompletedTasksAttribute(): int
    {
        return $this->tasks()->wherePivot('completed', true)->count();
    }

    public function tasksForWeek(int $week)
    {
        return $this->tasks()->wherePivot('week', $week)->get();
    }

    public function getPlanByWeeks()
    {
        $weeks = [];
        
        for ($week = 1; $week <= 4; $week++) {
            $weeks[] = [
                'week' => $week,
                'tasks' => $this->tasksForWeek($week),
                'total_hours' => $this->tasksForWeek($week)->sum('duration_hours'),
            ];
        }

        return array_values($weeks);
    }

    public function getPlanByCategories(): array
    {
        $tasks = $this->tasks;

        if ($tasks->isEmpty()) {
            return [];
        }

        $categoryMapping = [
            'Goals' => 'Goals',
            'Digital Marketing Foundations' => 'Digital Marketing Foundations',
            'Local SEO' => 'Local SEO',
            'Content' => 'Content',
            'Social Media' => 'Social Media',
            'Website' => 'Website',
            'Email Marketing' => 'Email Marketing',
            'Paid Ads' => 'Paid Advertising',
            'Paid Advertising' => 'Paid Advertising',
            'CRM' => 'CRM',
        ];

        $desiredOrder = [
            'Goals',
            'Digital Marketing Foundations',
            'Local SEO',
            'Content',
            'Social Media',
            'Website',
            'Email Marketing',
            'Paid Advertising',
            'CRM',
        ];

        $categories = [];

        foreach ($tasks as $task) {
            $rawCategory = $task->category ?? 'General';
            $mappedCategory = array_key_exists($rawCategory, $categoryMapping)
                ? $categoryMapping[$rawCategory]
                : $rawCategory;

            if ($mappedCategory === null || $mappedCategory === 'Buffer') {
                continue;
            }

            if (!array_key_exists($mappedCategory, $categories)) {
                $categories[$mappedCategory] = [
                    'name' => $mappedCategory,
                    'tasks' => [],
                    'totalHours' => 0,
                    'completed' => 0,
                ];
            }

            $categories[$mappedCategory]['tasks'][] = $task;
            $categories[$mappedCategory]['totalHours'] += $task->duration_hours ?? 0;

            if ($task->pivot && $task->pivot->completed) {
                $categories[$mappedCategory]['completed']++;
            }
        }

        foreach ($categories as &$category) {
            $category['tasks'] = collect($category['tasks'])
                ->sortBy(function ($task) {
                    $actionId = $task->action_id ?? 0;
                    $globalOrder = $task->global_order ?? 0;

                    return [$actionId, $globalOrder];
                })
                ->values()
                ->all();
        }
        unset($category);

        $result = array_map(function (array $category) {
            $taskCount = count($category['tasks']);
            $category['progress'] = $taskCount > 0
                ? (int) round(($category['completed'] / $taskCount) * 100)
                : 0;

            return $category;
        }, array_values($categories));

        usort($result, function (array $a, array $b) use ($desiredOrder) {
            $indexA = array_search($a['name'], $desiredOrder, true);
            $indexB = array_search($b['name'], $desiredOrder, true);

            if ($indexA === false && $indexB === false) {
                return strcmp($a['name'], $b['name']);
            }

            if ($indexA === false) {
                return 1;
            }

            if ($indexB === false) {
                return -1;
            }

            return $indexA <=> $indexB;
        });

        return $result;
    }

    public function getQuestionnaireSummaryAttribute(): ?string
    {
        $data = $this->questionnaire_data;

        if (!is_array($data)) {
            return null;
        }

        $parts = [];

        $countryLabels = [
            'de' => 'Germany',
            'uk' => 'United Kingdom',
            'ie' => 'Ireland',
        ];

        $industryLabels = [
            'beauty' => 'Beauty',
            'physio' => 'Physio',
            'coaching' => 'Coaching',
        ];

        if (isset($data['country'])) {
            $parts[] = 'Country: ' . ($countryLabels[$data['country']] ?? strtoupper((string) $data['country']));
        }

        if (isset($data['industry'])) {
            $parts[] = 'Industry: ' . ($industryLabels[$data['industry']] ?? (string) $data['industry']);
        }

        if (isset($data['is_local_business'])) {
            $parts[] = 'Local business: ' . ($data['is_local_business'] ? 'Yes' : 'No');
        }

        if (isset($data['marketing_time_per_week'])) {
            $parts[] = 'Time per week: ' . $data['marketing_time_per_week'] . 'h';
        }

        if (isset($data['has_website'])) {
            $parts[] = 'Website: ' . ($data['has_website'] ? 'Yes' : 'No');
        }

        if (isset($data['business_goals_defined'])) {
            $parts[] = 'Business goals: ' . ($data['business_goals_defined'] ? 'Defined' : 'Not defined');
        }

        if (isset($data['marketing_goals_defined'])) {
            $parts[] = 'Marketing goals: ' . ($data['marketing_goals_defined'] ? 'Defined' : 'Not defined');
        }

        if (isset($data['running_ads']) && is_array($data['running_ads']) && count($data['running_ads']) > 0) {
            $parts[] = 'Ads: ' . implode(', ', $data['running_ads']);
        }

        if (isset($data['primary_social_channel'])) {
            $parts[] = 'Primary social: ' . $data['primary_social_channel'];
        }

        if (isset($data['secondary_social_channel'])) {
            $parts[] = 'Secondary social: ' . $data['secondary_social_channel'];
        }

        if (count($parts) === 0) {
            return null;
        }

        return implode(' • ', $parts);
    }
}
