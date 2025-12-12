<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;
use App\Models\PlanTask;
use App\Services\PlanGeneratorService;
use Carbon\Carbon;

class TestMonthlyTasks extends Command
{
    protected $signature = 'test:monthly-tasks {plan_id?} {--month=} {--year=}';

    protected $description = 'Create test tasks for different months to test monthly functionality';

    public function handle(PlanGeneratorService $planGenerator): int
    {
        $planId = $this->argument('plan_id');
        $targetMonth = $this->option('month') ? (int) $this->option('month') : null;
        $targetYear = $this->option('year') ? (int) $this->option('year') : null;

        if (!$planId) {
            $plan = Plan::latest()->first();
            if (!$plan) {
                $this->error('No plans found. Please create a plan first.');
                return Command::FAILURE;
            }
            $planId = $plan->id;
        } else {
            $plan = Plan::findOrFail($planId);
        }

        $this->info("Using plan ID: {$plan->id} (created: {$plan->created_at})");

        $planCreatedAt = Carbon::parse($plan->created_at);
        $currentDate = Carbon::now();

        $startDate = Carbon::create($planCreatedAt->year, $planCreatedAt->month, 1)->startOfMonth();
        $endDate = $targetYear && $targetMonth 
            ? Carbon::create($targetYear, $targetMonth, 1)->startOfMonth()
            : $currentDate->copy()->startOfMonth();

        $this->info("Generating tasks from {$startDate->format('Y-m')} to {$endDate->format('Y-m')}");

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $year = $current->year;
            $month = $current->month;

            $hasTasks = PlanTask::where('plan_id', $plan->id)
                ->where('year', $year)
                ->where('month', $month)
                ->exists();

            if ($hasTasks) {
                $this->line("Tasks for {$year}-{$month} already exist, skipping...");
            } else {
                try {
                    $this->info("Generating tasks for {$year}-{$month}...");
                    $planGenerator->generatePlanForMonth($plan, $year, $month);
                    $taskCount = PlanTask::where('plan_id', $plan->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->count();
                    $this->info("✓ Created {$taskCount} tasks for {$year}-{$month}");
                } catch (\Exception $e) {
                    $this->error("Failed to generate tasks for {$year}-{$month}: " . $e->getMessage());
                }
            }

            $current->addMonth();
        }

        $this->info("\nDone! You can now test the monthly functionality.");
        $this->line("Available months for this plan:");
        $months = PlanTask::where('plan_id', $plan->id)
            ->selectRaw('DISTINCT year, month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        foreach ($months as $m) {
            $taskCount = PlanTask::where('plan_id', $plan->id)
                ->where('year', $m->year)
                ->where('month', $m->month)
                ->count();
            $this->line("  - {$m->year}-{$m->month}: {$taskCount} tasks");
        }

        return Command::SUCCESS;
    }
}
