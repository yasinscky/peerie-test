<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Task;
use App\Models\PlanTask;
use Illuminate\Support\Collection;

class PlanGeneratorService
{
    /**
     * Генерирует план с задачами на основе ответов анкеты
     */
    public function generatePlan(Plan $plan): array
    {
        $hoursPerWeek = max(1, (int) ($plan->marketing_time_per_week ?? 4));

        $filteredTasks = $this->filterTasks($plan);
        $prioritizedTasks = $this->prioritizeTasks($filteredTasks, $plan);
        $capacityAlignedTasks = $this->limitTasksByCapacity($prioritizedTasks, $hoursPerWeek);
        $sortedTasks = $this->sortTasksByDependencies($capacityAlignedTasks);
        $weeklyPlan = $this->distributeTasksByWeeks($sortedTasks, $hoursPerWeek);
        $this->savePlanTasks($plan, $weeklyPlan);
        
        return [
            'plan_id' => $plan->id,
            'title' => $plan->title,
            'weeks' => $weeklyPlan,
            'total_tasks' => $filteredTasks->count(),
            'total_hours' => $filteredTasks->sum('duration_hours'),
        ];
    }

    /**
     * Фильтрует задачи по критериям анкеты
     */
    private function filterTasks(Plan $plan): Collection
    {
        $query = Task::query();

        $query->where(function ($q) use ($plan) {
            $q->where('language', $plan->language);

            if ($plan->language !== 'en') {
                $q->orWhere(function ($nested) use ($plan) {
                    $nested->where('language', 'en')
                           ->where('is_global', true);
                });
            }
        });

        $tasks = $query->get();

        return $tasks->filter(function (Task $task) use ($plan) {
            return $this->matchesPlanBasics($task, $plan)
                && $this->passesQuestionnaireRules($task, $plan);
        })->values();
    }

    /**
     * Приоритизирует задачи в зависимости от типа бизнеса
     */
    private function prioritizeTasks(Collection $tasks, Plan $plan): Collection
    {
        return $tasks->sortBy(function ($task) use ($plan) {
            return $this->calculatePriority($task, $plan);
        })->values();
    }

    /**
     * Сортирует задачи по зависимостям (топологическая сортировка)
     */
    private function sortTasksByDependencies(Collection $tasks): Collection
    {
        $sorted = collect();
        $visited = [];
        $visiting = [];

        foreach ($tasks as $task) {
            if (!isset($visited[$task->id])) {
                $this->visitTask($task, $tasks, $visited, $visiting, $sorted);
            }
        }

        return $sorted;
    }

    /**
     * Рекурсивный обход задач для топологической сортировки
     */
    private function visitTask(Task $task, Collection $allTasks, array &$visited, array &$visiting, Collection &$sorted): void
    {
        if (isset($visiting[$task->id])) {
            return;
        }

        if (isset($visited[$task->id])) {
            return;
        }

        $visiting[$task->id] = true;

        if (!empty($task->dependencies)) {
            foreach ($task->dependencies as $dependencyId) {
                $dependency = $allTasks->firstWhere('id', $dependencyId);
                if ($dependency && !isset($visited[$dependencyId])) {
                    $this->visitTask($dependency, $allTasks, $visited, $visiting, $sorted);
                }
            }
        }

        unset($visiting[$task->id]);
        $visited[$task->id] = true;
        $sorted->push($task);
    }

    /**
     * Распределяет задачи по неделям с учетом лимита времени
     */
    private function distributeTasksByWeeks(Collection $tasks, int $hoursPerWeek): array
    {
        $weeks = [
            1 => ['tasks' => [], 'hours' => 0],
            2 => ['tasks' => [], 'hours' => 0],
            3 => ['tasks' => [], 'hours' => 0],
            4 => ['tasks' => [], 'hours' => 0],
        ];

        $completedTaskIds = [];
        
        $oneTimeTasks = $tasks->where('frequency', 'once');
        $weeklyTasks = $tasks->where('frequency', 'weekly');
        $monthlyTasks = $tasks->where('frequency', 'monthly');
        $quarterlyTasks = $tasks->where('frequency', 'quarterly');

        $this->distributeOneTimeTasks($oneTimeTasks, $weeks, $completedTaskIds, $hoursPerWeek);
        $this->distributeRecurringTasks($weeklyTasks, $weeks, $completedTaskIds, $hoursPerWeek, 'weekly');
        $this->distributeRecurringTasks($monthlyTasks, $weeks, $completedTaskIds, $hoursPerWeek, 'monthly');
        $this->distributeRecurringTasks($quarterlyTasks, $weeks, $completedTaskIds, $hoursPerWeek, 'quarterly');

        return $weeks;
    }

    /**
     * Распределяет одноразовые задачи
     */
    private function distributeOneTimeTasks(Collection $tasks, array &$weeks, array &$completedTaskIds, int $hoursPerWeek): void
    {
        $sortedTasks = $tasks->sortBy(function ($task) {
            $priority = 0;
            if ($task->difficulty_level === 'beginner') $priority = 1;
            elseif ($task->difficulty_level === 'intermediate') $priority = 2;
            else $priority = 3;
            
            return [$priority, $task->duration_hours];
        });

        $currentWeek = 1;
        foreach ($sortedTasks as $task) {
            $assigned = false;
            $startWeek = $currentWeek;

            do {
                if ($weeks[$currentWeek]['hours'] + $task->duration_hours <= $hoursPerWeek) {
                    $weeks[$currentWeek]['tasks'][] = $task;
                    $weeks[$currentWeek]['hours'] += $task->duration_hours;
                    $completedTaskIds[] = $task->id;
                    $assigned = true;
                    $currentWeek = ($currentWeek % 4) + 1;
                    break;
                }

                $currentWeek = ($currentWeek % 4) + 1;
            } while ($currentWeek !== $startWeek);

            if (!$assigned) {
                for ($week = 1; $week <= 4; $week++) {
                    if ($weeks[$week]['hours'] + $task->duration_hours <= $hoursPerWeek * 1.3) {
                        $weeks[$week]['tasks'][] = $task;
                        $weeks[$week]['hours'] += $task->duration_hours;
                        $completedTaskIds[] = $task->id;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Распределяет повторяющиеся задачи
     */
    private function distributeRecurringTasks(Collection $tasks, array &$weeks, array &$completedTaskIds, int $hoursPerWeek, string $frequency): void
    {
        foreach ($tasks as $task) {
            switch ($frequency) {
                case 'weekly':
                    for ($week = 1; $week <= 4; $week++) {
                        if ($weeks[$week]['hours'] + $task->duration_hours <= $hoursPerWeek * 1.2) {
                            $weeks[$week]['tasks'][] = $task;
                            $weeks[$week]['hours'] += $task->duration_hours;
                            if (!in_array($task->id, $completedTaskIds)) {
                                $completedTaskIds[] = $task->id;
                            }
                        }
                    }
                    break;
                    
                case 'monthly':
                    $assignedWeeks = [];
                    for ($week = 1; $week <= 4; $week++) {
                        if ($weeks[$week]['hours'] + $task->duration_hours <= $hoursPerWeek * 1.2) {
                            $weeks[$week]['tasks'][] = $task;
                            $weeks[$week]['hours'] += $task->duration_hours;
                            $assignedWeeks[] = $week;
                            if (!in_array($task->id, $completedTaskIds)) {
                                $completedTaskIds[] = $task->id;
                            }
                            if (count($assignedWeeks) >= 2) break;
                        }
                    }
                    break;
                    
                case 'quarterly':
                    for ($week = 1; $week <= 4; $week++) {
                        if ($weeks[$week]['hours'] + $task->duration_hours <= $hoursPerWeek * 1.2) {
                            $weeks[$week]['tasks'][] = $task;
                            $weeks[$week]['hours'] += $task->duration_hours;
                            if (!in_array($task->id, $completedTaskIds)) {
                                $completedTaskIds[] = $task->id;
                            }
                            break;
                        }
                    }
                    break;
            }
        }
    }

    /**
     * Сохраняет план задач в базу данных
     */
    private function savePlanTasks(Plan $plan, array $weeklyPlan): void
    {
        $plan->tasks()->detach();

        foreach ($weeklyPlan as $week => $weekData) {
            foreach ($weekData['tasks'] as $task) {
                PlanTask::create([
                    'plan_id' => $plan->id,
                    'task_id' => $task->id,
                    'week' => $week,
                    'completed' => false,
                ]);
            }
        }
    }

    /**
     * Оцениваем приоритет задачи
     */
    private function calculatePriority(Task $task, Plan $plan): int
    {
        $baseOrder = $task->global_order ?? 500;

        if ($plan->is_local_business && $task->is_local) {
            $baseOrder -= 50;
        }

        if (!$plan->business_goals_defined && $this->taskContains($task, ['goal', 'kpi'])) {
            $baseOrder -= 25;
        }

        if (!$plan->marketing_goals_defined && $this->taskContains($task, ['strategy', 'plan', 'roadmap'])) {
            $baseOrder -= 20;
        }

        if ($task->difficulty_level === 'beginner') {
            $baseOrder -= 10;
        } elseif ($task->difficulty_level === 'advanced') {
            $baseOrder += 5;
        }

        return max(0, $baseOrder);
    }

    /**
     * Ограничиваем список задач доступным временем
     */
    private function limitTasksByCapacity(Collection $tasks, int $hoursPerWeek): Collection
    {
        $availableHours = max(1, $hoursPerWeek) * 4;
        $selected = collect();
        $usedHours = 0;

        foreach ($tasks as $task) {
            if (($usedHours + $task->duration_hours) > $availableHours) {
                continue;
            }

            $selected->push($task);
            $usedHours += $task->duration_hours;
        }

        return $selected->isNotEmpty() ? $selected : $tasks->take(10);
    }

    /**
     * Дополнительные правила фильтрации по анкете
     */
    private function passesQuestionnaireRules(Task $task, Plan $plan): bool
    {
        if (!empty($task->conditions)) {
            foreach ($task->conditions as $condition) {
                $field = $condition['field'] ?? null;
                $operator = $condition['operator'] ?? '=';
                $expected = $condition['value'] ?? null;

                if (!$field) {
                    continue;
                }

                $actual = data_get($plan, $field);

                if ($field === 'running_ads') {
                    $actual = $actual ?? 'none';
                }

                if ($operator === '=' && $actual !== $expected) {
                    return false;
                }

                if ($operator === '!=' && $actual === $expected) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Проверяем, относится ли задача к соцсетям
     */
    private function matchesPlanBasics(Task $task, Plan $plan): bool
    {
        $planCountry = $plan->country;

        if (!$task->is_global && !empty($task->target_countries) && !in_array($planCountry, $task->target_countries, true)) {
            return false;
        }

        $planIndustries = is_array($plan->industries) ? $plan->industries : [$plan->business_niche];
        $planIndustries = array_filter(array_map('strtolower', $planIndustries));

        if (!empty($task->target_industries)) {
            $intersection = array_intersect($task->target_industries, $planIndustries);
            if (empty($intersection)) {
                return false;
            }
        }

        if (!empty($task->allowed_capacities)) {
            $capacity = (int) $plan->marketing_time_per_week;
            if (!in_array($capacity, $task->allowed_capacities, true)) {
                return false;
            }
        }

        if (!empty($task->local_presence_options)) {
            $options = $task->local_presence_options;
            $isLocal = (bool) $plan->is_local_business;

            if ($isLocal && !in_array('yes', $options, true)) {
                return false;
            }

            if (!$isLocal && !in_array('no', $options, true)) {
                return false;
            }
        }

        return true;
    }

    private function isSocialTask(Task $task): bool
    {
        return $task->category === 'Social Media';
    }

    private function taskContains(Task $task, array $keywords): bool
    {
        $haystack = mb_strtolower(implode(' ', [
            $task->category,
            $task->title,
            $task->description,
        ]));

        foreach ($keywords as $keyword) {
            if (str_contains($haystack, mb_strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }
}
