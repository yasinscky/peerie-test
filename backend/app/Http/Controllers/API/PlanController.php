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
    public function index(): JsonResponse
    {
        $plans = Plan::where('user_id', Auth::id())
            ->with(['tasks' => function ($query) {
                $query->orderBy('plan_tasks.week')->orderBy('plan_tasks.created_at');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'plans' => PlanResource::collection($plans)
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $plan = Plan::with(['tasks' => function ($query) {
            $query->orderBy('plan_tasks.week')->orderBy('plan_tasks.created_at');
        }])->findOrFail($id);

        if ($plan->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'plan' => new PlanResource($plan)
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

        $planTask->update([
            'completed' => $request->completed,
            'notes' => $request->notes,
        ]);

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
}
