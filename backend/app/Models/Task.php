<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'title',
        'description',
        'duration_hours',
        'frequency',
        'dependencies',
        'business_type',
        'language',
        'is_local',
        'requires_website',
        'difficulty_level',
        'category',
        'global_order',
        'is_global',
        'target_countries',
        'target_industries',
        'allowed_capacities',
        'local_presence_options',
        'conditions',
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
        'local_presence_options' => 'array',
        'conditions' => 'array',
    ];

    /**
     * Получить планы, связанные с этой задачей
     */
    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_tasks')
                    ->withPivot(['week', 'completed', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Получить задачи-зависимости
     */
    public function dependencyTasks()
    {
        if (empty($this->dependencies)) {
            return collect();
        }

        return Task::whereIn('id', $this->dependencies)->get();
    }

    /**
     * Проверить, можно ли выполнить задачу (все зависимости выполнены)
     */
    public function canBeExecuted(array $completedTaskIds = []): bool
    {
        if (empty($this->dependencies)) {
            return true;
        }

        return empty(array_diff($this->dependencies, $completedTaskIds));
    }
}
