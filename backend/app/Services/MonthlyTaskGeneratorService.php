<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\PlanTask;
use App\Services\PlanGeneratorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MonthlyTaskGeneratorService
{
    protected PlanGeneratorService $planGenerator;

    public function __construct(PlanGeneratorService $planGenerator)
    {
        $this->planGenerator = $planGenerator;
    }

    public function generateTasksForNewMonth(): void
    {
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        $plans = Plan::whereHas('user', function ($query) {
            $query->whereNotNull('email_verified_at');
        })->get();

        foreach ($plans as $plan) {
            $hasTasksForCurrentMonth = PlanTask::where('plan_id', $plan->id)
                ->where('year', $currentYear)
                ->where('month', $currentMonth)
                ->exists();

            if (!$hasTasksForCurrentMonth) {
                try {
                    $this->planGenerator->generatePlanForMonth($plan, $currentYear, $currentMonth);
                    Log::info("Generated tasks for plan {$plan->id} for {$currentYear}-{$currentMonth}");
                } catch (\Exception $e) {
                    Log::error("Failed to generate tasks for plan {$plan->id}: " . $e->getMessage());
                }
            }
        }
    }
}
