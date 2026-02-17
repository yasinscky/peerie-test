<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Task;
use App\Models\PlanTask;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PlanGeneratorService
{
    public function generatePlan(Plan $plan): array
    {
        $plan->refresh();
        
        $planCreatedAt = $plan->created_at instanceof Carbon
            ? $plan->created_at
            : Carbon::parse($plan->created_at ?? Carbon::now());
        
        $year = $planCreatedAt->year;
        $month = $planCreatedAt->month;
        
        return $this->generatePlanForMonth($plan, $year, $month);
    }

    private function filterTasks(Plan $plan, int $year, int $month): Collection
    {
        $tasks = Task::query()
            ->whereIn('language', [$plan->language, 'en'])
            ->get();

        $filtered = $tasks->filter(function (Task $task) use ($plan) {
            return $this->matchesPlanBasics($task, $plan)
                && $this->passesQuestionnaireRules($task, $plan);
        })->values();

        $grouped = $filtered
            ->groupBy(function (Task $task) {
                if (!empty($task->category)) {
                    return $task->category . '|' . ($task->action_id ?? $task->id);
                }

                return $task->action_id ?? $task->id;
            })
            ->map(function (Collection $group) use ($plan) {
                $preferred = $group->firstWhere('language', $plan->language);

                if ($preferred) {
                    return $preferred;
                }

                $fallback = $group->firstWhere('language', 'en');

                if ($fallback) {
                    return $fallback;
                }

                return $group->first();
            })
            ->values();

        $previousMonthsTasks = $this->getPreviousMonthsTaskIds($plan, $year, $month);

        return $grouped->filter(function (Task $task) use ($plan, $year, $month, $previousMonthsTasks) {
            $frequency = $this->normalizeFrequency($task->frequency);

            if (in_array($frequency, ['weekly', 'bi_weekly', 'monthly'], true)) {
                return true;
            }

            if ($frequency === 'once') {
                $wasInPreviousMonths = in_array($task->id, $previousMonthsTasks, true);
                return !$wasInPreviousMonths;
            }

            return $this->shouldIncludeTaskThisMonth($task, $plan, $year, $month);
        });
    }

    private function getPreviousMonthsTaskIds(Plan $plan, int $year, int $month): array
    {
        $planCreatedAt = $plan->created_at instanceof Carbon
            ? $plan->created_at
            : Carbon::parse($plan->created_at ?? Carbon::now());

        $currentDate = Carbon::create($year, $month, 1)->startOfMonth();
        $planStartDate = Carbon::create($planCreatedAt->year, $planCreatedAt->month, 1)->startOfMonth();

        if ($currentDate->lte($planStartDate)) {
            return [];
        }

        $previousTasks = PlanTask::where('plan_id', $plan->id)
            ->where(function ($query) use ($year, $month) {
                $query->where('year', '<', $year)
                      ->orWhere(function ($q) use ($year, $month) {
                          $q->where('year', $year)
                            ->where('month', '<', $month);
                      });
            })
            ->distinct()
            ->pluck('task_id')
            ->toArray();

        return array_values(array_unique($previousTasks));
    }

    private function prioritizeTasks(Collection $tasks, Plan $plan, int $year, int $month): Collection
    {
        $previousMonthsTaskIds = $this->getPreviousMonthsTaskIds($plan, $year, $month);

        return $tasks->sortBy(function ($task) use ($plan, $previousMonthsTaskIds) {
            $globalOrder = (int) ($task->global_order ?? 500);
            
            $frequency = $this->normalizeFrequency($task->frequency);
            
            if ($frequency === 'once' && in_array($task->id, $previousMonthsTaskIds, true)) {
                return 999999;
            }
            
            if (in_array($task->id, $previousMonthsTaskIds, true)) {
                return 100000 + $globalOrder;
            }

            return $globalOrder;
        })->values();
    }

    private function getPreviousMonthsCompletedTaskIds(Plan $plan, int $year, int $month): array
    {
        $currentDate = Carbon::create($year, $month, 1)->startOfMonth();

        $completedTasks = PlanTask::where('plan_id', $plan->id)
            ->where('completed', true)
            ->where(function ($query) use ($year, $month) {
                $query->where('year', '<', $year)
                      ->orWhere(function ($q) use ($year, $month) {
                          $q->where('year', $year)
                           ->where('month', '<', $month);
                      });
            })
            ->pluck('task_id')
            ->unique()
            ->toArray();

        return $completedTasks;
    }

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

    private function getTaskDurationMinutes(Task $task): int
    {
        if (!empty($task->duration_minutes)) {
            return (int) $task->duration_minutes;
        }

        if (!empty($task->duration_hours)) {
            return (int) $task->duration_hours * 60;
        }

        return 60;
    }

    private function getTaskEffectiveDurationMinutes(Task $task): int
    {
        $taskMinutes = $this->getTaskDurationMinutes($task);
        $frequency = $this->normalizeFrequency($task->frequency);

        if ($frequency === 'weekly') {
            return $taskMinutes * 4;
        }

        if ($frequency === 'bi_weekly') {
            return $taskMinutes * 2;
        }

        return $taskMinutes;
    }

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

    private function savePlanTasks(Plan $plan, Collection $tasks, ?int $year = null, ?int $month = null): void
    {
        $now = Carbon::now();
        $year = $year ?? $now->year;
        $month = $month ?? $now->month;

        PlanTask::where('plan_id', $plan->id)
            ->where('year', $year)
            ->where('month', $month)
            ->delete();

        foreach ($tasks as $task) {
            PlanTask::create([
                'plan_id' => $plan->id,
                'task_id' => $task->id,
                'week' => 1,
                'year' => $year,
                'month' => $month,
                'completed' => false,
            ]);
        }
    }

    public function syncNewTasksForMonth(Plan $plan, int $year, int $month): void
    {
        $existingPlanTasks = PlanTask::where('plan_id', $plan->id)
            ->where('year', $year)
            ->where('month', $month)
            ->pluck('task_id')
            ->toArray();

        $filteredTasks = $this->filterTasks($plan, $year, $month);

        $newTasks = $filteredTasks->filter(function ($task) use ($existingPlanTasks) {
            return !in_array($task->id, $existingPlanTasks, true);
        });

        if ($newTasks->isEmpty()) {
            return;
        }

        $hoursPerWeek = max(1, (int) ($plan->marketing_time_per_week ?? 4));
        $minutesPerWeek = $hoursPerWeek * 60;
        $monthlyCapacity = $minutesPerWeek * 4;

        $existingTotalMinutes = PlanTask::where('plan_id', $plan->id)
            ->where('year', $year)
            ->where('month', $month)
            ->with('task')
            ->get()
            ->sum(function ($planTask) {
                if (!$planTask->task) {
                    return 0;
                }
                return $this->getTaskEffectiveDurationMinutes($planTask->task);
            });

        $availableMinutes = max(0, $monthlyCapacity - $existingTotalMinutes);

        if ($availableMinutes <= 0) {
            return;
        }

        $prioritizedNewTasks = $this->prioritizeTasks($newTasks, $plan, $year, $month);
        $orderedNewTasks = $this->applyPrerequisites($prioritizedNewTasks);
        
        $selectedTasks = collect();
        $usedMinutes = 0;
        
        foreach ($orderedNewTasks as $task) {
            $taskMinutes = $this->getTaskEffectiveDurationMinutes($task);
            
            if (($usedMinutes + $taskMinutes) <= $availableMinutes) {
                $selectedTasks->push($task);
                $usedMinutes += $taskMinutes;
            }
        }

        foreach ($selectedTasks as $task) {
            PlanTask::create([
                'plan_id' => $plan->id,
                'task_id' => $task->id,
                'week' => 1,
                'year' => $year,
                'month' => $month,
                'completed' => false,
            ]);
        }
    }

    public function generatePlanForMonth(Plan $plan, int $year, int $month): array
    {
        $hoursPerWeek = max(1, (int) ($plan->marketing_time_per_week ?? 4));
        $minutesPerWeek = $hoursPerWeek * 60;

        $filteredTasks = $this->filterTasks($plan, $year, $month);
        $prioritizedTasks = $this->prioritizeTasks($filteredTasks, $plan, $year, $month);
        $orderedTasks = $this->applyPrerequisites($prioritizedTasks);
        $limitedTasks = $this->limitTasksByCapacity($orderedTasks, $minutesPerWeek);
        $this->savePlanTasks($plan, $limitedTasks, $year, $month);
        
        return [
            'plan_id' => $plan->id,
            'title' => $plan->title,
            'year' => $year,
            'month' => $month,
            'tasks' => $limitedTasks->values(),
            'total_tasks' => $limitedTasks->count(),
            'total_minutes' => $limitedTasks->sum(function (Task $task) {
                return $this->getTaskEffectiveDurationMinutes($task);
            }),
        ];
    }

    private function applyPrerequisites(Collection $tasks): Collection
    {
        $items = $tasks->values()->all();
        $count = count($items);

        if ($count < 2) {
            return $tasks->values();
        }

        $maxPasses = $count * 2;

        for ($pass = 0; $pass < $maxPasses; $pass++) {
            $indexById = [];
            foreach ($items as $idx => $item) {
                $indexById[$item->id] = $idx;
            }

            $moved = false;

            foreach ($items as $idx => $task) {
                $dependencies = is_array($task->dependencies ?? null) ? $task->dependencies : [];

                if ($dependencies === []) {
                    continue;
                }

                $maxDependencyIndex = -1;
                foreach ($dependencies as $dependencyId) {
                    if (!is_int($dependencyId) && !ctype_digit((string) $dependencyId)) {
                        continue;
                    }

                    $dependencyId = (int) $dependencyId;

                    if ($dependencyId === (int) $task->id) {
                        continue;
                    }

                    if (!array_key_exists($dependencyId, $indexById)) {
                        continue;
                    }

                    $maxDependencyIndex = max($maxDependencyIndex, $indexById[$dependencyId]);
                }

                if ($maxDependencyIndex <= $idx) {
                    continue;
                }

                $taskItem = $items[$idx];
                array_splice($items, $idx, 1);

                $insertIndex = $maxDependencyIndex;
                if ($maxDependencyIndex > $idx) {
                    $insertIndex = $maxDependencyIndex;
                }

                array_splice($items, $insertIndex, 0, [$taskItem]);
                $moved = true;
                break;
            }

            if (!$moved) {
                break;
            }
        }

        return collect($items)->values();
    }

    private function calculatePriority(Task $task, Plan $plan): int
    {
        return (int) ($task->global_order ?? 500);
    }

    private function limitTasksByCapacity(Collection $tasks, int $minutesPerWeek): Collection
    {
        $availableMinutes = max(1, $minutesPerWeek) * 4;
        $selected = collect();
        $usedMinutes = 0;
        foreach ($tasks as $task) {
            $taskMinutes = $this->getTaskDurationMinutes($task);
            $multiplier = 1;
            $frequency = $this->normalizeFrequency($task->frequency);

            if ($frequency === 'weekly') {
                $multiplier = 4;
            } elseif ($frequency === 'bi_weekly') {
                $multiplier = 2;
            }

            $effectiveMinutes = $taskMinutes * $multiplier;

            if (($usedMinutes + $effectiveMinutes) > $availableMinutes) {
                break;
            }

            $selected->push($task);
            $usedMinutes += $effectiveMinutes;
        }

        return $selected->values();
    }

    private function passesQuestionnaireRules(Task $task, Plan $plan): bool
    {
        if (!empty($task->conditions) && is_array($task->conditions)) {
            foreach ($task->conditions as $condition) {
                if (!isset($condition['field']) || !isset($condition['operator']) || !isset($condition['value'])) {
                    continue;
                }

                $field = $condition['field'];
                $operator = $condition['operator'];
                $requiredValue = $condition['value'];

                $planValue = $this->getPlanConditionValue($plan, $field);

                if ($planValue === null) {
                    continue;
                }

                if (!$this->evaluateCondition($planValue, $operator, $requiredValue)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function evaluateCondition($planValue, string $operator, $requiredValue): bool
    {
        return match ($operator) {
            'equals', '==' => $planValue == $requiredValue,
            'not_equals', '!=' => $planValue != $requiredValue,
            'in' => is_array($requiredValue) && in_array($planValue, $requiredValue, true),
            'not_in' => is_array($requiredValue) && !in_array($planValue, $requiredValue, true),
            default => true,
        };
    }

    private function matchesPlanBasics(Task $task, Plan $plan): bool
    {
        if (!$plan->is_local_business && $task->category === 'Local SEO') {
            return false;
        }

        $planCountry = $this->normalizeCountryCode($plan->country);

        $taskCountries = $this->normalizeArrayValues($task->target_countries ?? null);

        if (!empty($taskCountries)) {
            $normalizedTaskCountries = array_map(
                fn ($country) => $this->normalizeCountryCode($country),
                $taskCountries
            );

            if (!in_array($planCountry, $normalizedTaskCountries, true)) {
                return false;
            }
        }

        $planIndustries = is_array($plan->industries) ? $plan->industries : [];
        $planIndustries = array_filter(array_map('strtolower', $planIndustries));

        $taskIndustries = $this->normalizeArrayValues($task->target_industries ?? null);

        if (!empty($taskIndustries) && !empty($planIndustries)) {
            $taskIndustries = array_filter(array_map(static function ($value) {
                return strtolower(trim((string) $value));
            }, $taskIndustries));

            $hasAllIndustriesFlag = in_array('all', $taskIndustries, true)
                || in_array('all industries', $taskIndustries, true);

            if (!$hasAllIndustriesFlag) {
                $intersection = array_intersect($planIndustries, $taskIndustries);

                if (count($intersection) === 0) {
                    return false;
                }
            }
        }

        $capacities = $this->normalizeArrayValues($task->allowed_capacities ?? null);

        if (!empty($capacities)) {
            $capacities = array_map(static fn ($value) => (int) $value, $capacities);
            $capacity = (int) $plan->marketing_time_per_week;

            if (!in_array($capacity, $capacities, true)) {
                return false;
            }
        }

        if (!empty($task->local_presence_options)) {
            $option = $task->local_presence_options;
            $isLocal = (bool) $plan->is_local_business;

            if ($option === 'yes' && !$isLocal) {
                return false;
            }

            if ($option === 'no' && $isLocal) {
                return false;
            }
        }

        if (!empty($task->prerequisites) && is_array($task->prerequisites)) {
            foreach ($task->prerequisites as $prerequisite) {
                if (!isset($prerequisite['condition']) || !isset($prerequisite['value'])) {
                    continue;
                }

                $condition = $prerequisite['condition'];
                $requiredValueRaw = $prerequisite['value'];

                $planValue = $this->getPlanConditionValue($plan, $condition);

                if ($planValue === null && !$this->isBooleanCondition($condition)) {
                    continue;
                }

                $planValueBool = $planValue === true;
                $requiredValueBool = $this->normalizePrerequisiteValue($requiredValueRaw);

                if ($planValueBool !== $requiredValueBool) {
                    return false;
                }
            }
        }

        return true;
    }

    private function getPlanConditionValue(Plan $plan, string $condition): ?bool
    {
        $value = match ($condition) {
            'business_goals_defined' => $plan->business_goals_defined,
            'marketing_goals_defined' => $plan->marketing_goals_defined,
            'google_business_claimed' => $plan->google_business_claimed,
            'core_directories_claimed' => $plan->core_directories_claimed,
            'industry_directories_claimed' => $plan->industry_directories_claimed,
            'business_directories_claimed' => $plan->business_directories_claimed,
            'has_website' => $plan->has_website,
            'email_marketing_tool' => $plan->email_marketing_tool,
            'crm_pipeline' => $plan->crm_pipeline,
            'has_primary_social_channel' => $plan->has_primary_social_channel,
            'has_secondary_social_channel' => $plan->has_secondary_social_channel,
            default => null,
        };

        if ($value === null && $this->isBooleanCondition($condition)) {
            return false;
        }

        return $value === null ? null : (bool) $value;
    }

    private function isBooleanCondition(string $condition): bool
    {
        return in_array($condition, [
            'business_goals_defined',
            'marketing_goals_defined',
            'google_business_claimed',
            'core_directories_claimed',
            'industry_directories_claimed',
            'business_directories_claimed',
            'has_website',
            'email_marketing_tool',
            'crm_pipeline',
            'has_primary_social_channel',
            'has_secondary_social_channel',
        ], true);
    }

    private function normalizePrerequisiteValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            return in_array($normalized, ['yes', 'true', '1'], true);
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return false;
    }

    private function shouldIncludeTaskThisMonth(Task $task, Plan $plan, int $year, int $month): bool
    {
        $frequency = $this->normalizeFrequency($task->frequency);

        if (in_array($frequency, ['once', 'weekly', 'bi_weekly'], true)) {
            return true;
        }

        $planCreatedAt = $plan->created_at instanceof Carbon
            ? $plan->created_at
            : Carbon::parse($plan->created_at ?? Carbon::now());

        $currentDate = Carbon::create($year, $month, 1)->startOfMonth();
        $planStartDate = $planCreatedAt->copy()->startOfMonth();
        $monthsSinceStart = $planStartDate->diffInMonths($currentDate);

        if ($frequency === 'monthly') {
            return true;
        }

        if ($frequency === 'quarterly') {
            return $monthsSinceStart % 3 === 0;
        }

        if ($frequency === 'half_yearly') {
            return $monthsSinceStart % 6 === 0;
        }

        if ($frequency === 'yearly') {
            if ($monthsSinceStart < 12) {
                return $monthsSinceStart % 12 === 0;
            }

            $planTask = PlanTask::where('plan_id', $plan->id)
                ->where('task_id', $task->id)
                ->where('completed', true)
                ->orderByDesc('last_completed_at')
                ->first();

            if ($planTask && $planTask->last_completed_at) {
                $lastCompletedDate = Carbon::parse($planTask->last_completed_at)->startOfMonth();
                $monthsSinceCompletion = $lastCompletedDate->diffInMonths($currentDate);

                return $monthsSinceCompletion >= 12 && $monthsSinceCompletion % 12 === 0;
            }

            return $monthsSinceStart % 12 === 0;
        }

        return true;
    }

    private function normalizeFrequency(?string $frequency): string
    {
        return $frequency ?? 'once';
    }

    private function normalizeCountryCode(?string $country): string
    {
        if ($country === null) {
            return '';
        }

        $code = strtolower(trim($country));

        return match ($code) {
            'uk', 'gb', 'united kingdom' => 'uk',
            'ire', 'ie', 'ireland' => 'ie',
            'de', 'ger', 'germany' => 'de',
            default => $code,
        };
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

    private function normalizeArrayValues($value): array
    {
        if ($value === null) {
            return [];
        }

        $result = [];
        $stack = is_array($value) ? $value : [$value];

        foreach ($stack as $item) {
            if (is_array($item)) {
                if (array_key_exists('s', $item) && $item['s'] === 'arr' && count($item) === 1) {
                    continue;
                }

                foreach ($this->normalizeArrayValues($item) as $nested) {
                    $result[] = $nested;
                }

                continue;
            }

            if ($item === null || $item === '') {
                continue;
            }

            if (is_scalar($item)) {
                $result[] = $item;
            }
        }

        return array_values(array_unique($result));
    }
}
