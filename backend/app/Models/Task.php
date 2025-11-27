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
        'action_id',
        'title',
        'description',
        'duration_hours',
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
    ];

    protected function getLocalPresenceOptionsAttribute($value)
    {
        if (is_array($value)) {
            return !empty($value) ? $value[0] : 'no';
        }
        return $value ?? 'no';
    }

    protected function getTemplateAttribute($value)
    {
        if (is_array($value)) {
            return !empty($value) ? $value[0] : 'no';
        }
        return $value ?? 'no';
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_tasks')
                    ->withPivot(['week', 'completed', 'notes'])
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
