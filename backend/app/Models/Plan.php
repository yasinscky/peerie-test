<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'country',
        'business_niche',
        'business_type',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
        'has_primary_social_channel' => 'boolean',
        'has_secondary_social_channel' => 'boolean',
    ];

    /**
     * Получить пользователя, которому принадлежит план
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить задачи плана
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'plan_tasks')
                    ->withPivot(['id', 'week', 'completed', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Получить задачи для конкретной недели
     */
    public function tasksForWeek(int $week)
    {
        return $this->tasks()->wherePivot('week', $week)->get();
    }

    /**
     * Получить план по неделям
     */
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
}
