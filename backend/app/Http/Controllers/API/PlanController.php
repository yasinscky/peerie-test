<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Models\Task;
use App\Models\PlanTask;
use App\Services\PlanGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    protected PlanGeneratorService $planGenerator;

    public function __construct(PlanGeneratorService $planGenerator)
    {
        $this->planGenerator = $planGenerator;
    }

    public function generateFromQuestionnaire(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:100',
            'title' => 'required|string|max:150',
            'language' => 'required|string|in:de,en',
            'is_local_business' => 'required|boolean',
            'has_website' => 'required|boolean',
            'marketing_time_per_week' => 'required|integer|min:1|max:40',
            'additional_requirements' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $plan = Plan::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'country' => $request->country,
                'language' => $request->language,
                'is_local_business' => $request->is_local_business,
                'has_website' => $request->has_website,
                'marketing_time_per_week' => $request->marketing_time_per_week,
                'questionnaire_data' => $request->all(),
            ]);

            $generatedPlan = $this->planGenerator->generatePlan($plan);

            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully',
                'plan' => $generatedPlan
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user plans list
     */
    public function index(Request $request): JsonResponse
    {
        $year = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        $plans = Plan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $currentYear = now()->year;
        $currentMonth = now()->month;

        foreach ($plans as $plan) {
            $hasTasksForRequestedMonth = \App\Models\PlanTask::where('plan_id', $plan->id)
                ->where('year', $year)
                ->where('month', $month)
                ->exists();

            if (!$hasTasksForRequestedMonth) {
                try {
                    $this->planGenerator->generatePlanForMonth($plan, $year, $month);
                } catch (\Exception $e) {
                    \Log::error("Failed to auto-generate tasks for plan {$plan->id} for {$year}-{$month}: " . $e->getMessage());
                }
            } else {
                $planCreatedAt = $plan->created_at instanceof \Carbon\Carbon
                    ? $plan->created_at
                    : \Carbon\Carbon::parse($plan->created_at ?? \Carbon\Carbon::now());
                
                $requestedDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
                $planStartDate = \Carbon\Carbon::create($planCreatedAt->year, $planCreatedAt->month, 1)->startOfMonth();
                
                if ($requestedDate->gt($planStartDate)) {
                    $previousMonthsTasks = \App\Models\PlanTask::where('plan_id', $plan->id)
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
                    
                    $currentMonthTasks = \App\Models\PlanTask::where('plan_id', $plan->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->pluck('task_id')
                        ->unique()
                        ->toArray();
                    
                    $onceTasksInCurrentMonth = \App\Models\Task::whereIn('id', $currentMonthTasks)
                        ->where('frequency', 'once')
                        ->pluck('id')
                        ->toArray();
                    
                    $onceTasksInPreviousMonths = \App\Models\Task::whereIn('id', $previousMonthsTasks)
                        ->where('frequency', 'once')
                        ->pluck('id')
                        ->toArray();
                    
                    $hasOnceTasksFromPreviousMonths = !empty(array_intersect($onceTasksInCurrentMonth, $onceTasksInPreviousMonths));
                    
                    if ($hasOnceTasksFromPreviousMonths) {
                        try {
                            $this->planGenerator->generatePlanForMonth($plan, $year, $month);
                        } catch (\Exception $e) {
                            \Log::error("Failed to re-generate tasks for plan {$plan->id} for {$year}-{$month}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        foreach ($plans as $plan) {
            try {
                $this->planGenerator->syncNewTasksForMonth($plan, $year, $month);
            } catch (\Exception $e) {
                \Log::error("Failed to sync new tasks for plan {$plan->id} for {$year}-{$month}: " . $e->getMessage());
            }
        }

        $plans->load(['tasks' => function ($query) use ($year, $month) {
            $query->where('plan_tasks.year', $year)
                  ->where('plan_tasks.month', $month)
                  ->orderBy('plan_tasks.week')
                  ->orderBy('plan_tasks.created_at');
        }]);

        return response()->json([
            'success' => true,
            'plans' => PlanResource::collection($plans),
            'year' => (int) $year,
            'month' => (int) $month,
        ]);
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $plan = Plan::findOrFail($id);

        if ($plan->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $year = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        try {
            $this->planGenerator->syncNewTasksForMonth($plan, $year, $month);
        } catch (\Exception $e) {
            \Log::error("Failed to sync new tasks for plan {$plan->id} for {$year}-{$month}: " . $e->getMessage());
        }

        $plan->load(['tasks' => function ($query) use ($year, $month) {
            $query->where('plan_tasks.year', $year)
                  ->where('plan_tasks.month', $month)
                  ->orderBy('plan_tasks.week')
                  ->orderBy('plan_tasks.created_at');
        }]);

        return response()->json([
            'success' => true,
            'plan' => new PlanResource($plan),
            'year' => (int) $year,
            'month' => (int) $month,
        ]);
    }

    public function updateTaskStatus(Request $request, int $planId, int $planTaskId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'completed' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $plan = Plan::findOrFail($planId);

        if ($plan->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $planTask = PlanTask::where('id', $planTaskId)
            ->where('plan_id', $plan->id)
            ->first();

        if (!$planTask) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found in plan'
            ], 404);
        }

        $updateData = [
            'completed' => $request->completed,
            'notes' => $request->notes,
        ];

        if ($request->completed && !$planTask->completed) {
            $updateData['last_completed_at'] = now();
        }

        $planTask->update($updateData);

        return response()->json([
                'success' => true,
                'message' => 'Task status updated'
        ]);
    }

    /**
     * Get user plans list
     */
    public function getUserPlans(): JsonResponse
    {
        $plans = Plan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'created_at']);

        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }

    public function getAvailableMonths(): JsonResponse
    {
        $userPlans = Plan::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();

        if ($userPlans->isEmpty()) {
            $now = \Carbon\Carbon::now();
            return response()->json([
                'success' => true,
                'months' => [[
                    'year' => $now->year,
                    'month' => $now->month,
                ]]
            ]);
        }

        $firstPlan = $userPlans->first();
        $planCreatedAt = \Carbon\Carbon::parse($firstPlan->created_at);
        $planStartYear = $planCreatedAt->year;
        $planStartMonth = $planCreatedAt->month;

        $now = \Carbon\Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        $months = [];
        $startDate = \Carbon\Carbon::create($planStartYear, $planStartMonth, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $months[] = [
                'year' => (int) $current->year,
                'month' => (int) $current->month,
            ];
            $current->addMonth();
        }

        $months = array_reverse($months);

        return response()->json([
            'success' => true,
            'months' => $months
        ]);
    }

    public function generateMonth(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $year = (int) $request->input('year');
        $month = (int) $request->input('month');

        $plans = Plan::where('user_id', Auth::id())->get();

        if ($plans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No plans found'
            ], 404);
        }

        $generated = [];
        foreach ($plans as $plan) {
            $hasTasks = \App\Models\PlanTask::where('plan_id', $plan->id)
                ->where('year', $year)
                ->where('month', $month)
                ->exists();

            if (!$hasTasks) {
                try {
                    $this->planGenerator->generatePlanForMonth($plan, $year, $month);
                    $taskCount = \App\Models\PlanTask::where('plan_id', $plan->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->count();
                    $generated[] = [
                        'plan_id' => $plan->id,
                        'tasks_created' => $taskCount
                    ];
                } catch (\Exception $e) {
                    \Log::error("Failed to generate tasks for plan {$plan->id}: " . $e->getMessage());
                }
            } else {
                $generated[] = [
                    'plan_id' => $plan->id,
                    'tasks_created' => 0,
                    'message' => 'Tasks already exist for this month'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tasks generated successfully',
            'year' => $year,
            'month' => $month,
            'plans' => $generated
        ]);
    }
}
