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

    /**
     * Создать план на основе анкеты
     */
    public function generateFromQuestionnaire(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required|string|max:100',
            'business_niche' => 'required|string|max:100',
            'language' => 'required|string|in:ru,en',
            'is_local_business' => 'required|boolean',
            'has_website' => 'required|boolean',
            'marketing_time_per_week' => 'required|integer|min:1|max:40',
            'business_type' => 'required|string|in:any,ecommerce,service,saas,content',
            'additional_requirements' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Создаем план
            $plan = Plan::create([
                'user_id' => Auth::id(),
                'title' => "Маркетинг-план для {$request->business_niche}",
                'country' => $request->country,
                'business_niche' => $request->business_niche,
                'language' => $request->language,
                'is_local_business' => $request->is_local_business,
                'has_website' => $request->has_website,
                'marketing_time_per_week' => $request->marketing_time_per_week,
                'questionnaire_data' => $request->all(),
            ]);

            // Генерируем план с задачами
            $generatedPlan = $this->planGenerator->generatePlan($plan);

            return response()->json([
                'success' => true,
                'message' => 'План успешно создан',
                'plan' => $generatedPlan
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании плана: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить список планов пользователя
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

    /**
     * Получить план с задачами по неделям
     */
    public function show(int $id): JsonResponse
    {
        $plan = Plan::with(['tasks' => function ($query) {
            $query->orderBy('plan_tasks.week')->orderBy('plan_tasks.created_at');
        }])->findOrFail($id);

        // Проверяем, что план принадлежит текущему пользователю
        if ($plan->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'plan' => new PlanResource($plan)
        ]);
    }

    /**
     * Обновить статус выполнения задачи
     */
    public function updateTaskStatus(Request $request, int $planId, int $planTaskId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'completed' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        $plan = Plan::findOrFail($planId);

        // Проверяем права доступа
        if ($plan->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

        $planTask = PlanTask::where('id', $planTaskId)
            ->where('plan_id', $plan->id)
            ->first();

        if (!$planTask) {
            return response()->json([
                'success' => false,
                'message' => 'Задача не найдена в плане'
            ], 404);
        }

        $planTask->update([
            'completed' => $request->completed,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Статус задачи обновлен'
        ]);
    }

    /**
     * Получить список планов пользователя
     */
    public function getUserPlans(): JsonResponse
    {
        $plans = Plan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'business_niche', 'created_at']);

        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }
}
