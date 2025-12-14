<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Services\PlanGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionnaireController extends Controller
{
    protected PlanGeneratorService $planGenerator;

    public function __construct(PlanGeneratorService $planGenerator)
    {
        $this->planGenerator = $planGenerator;
    }

    public function submit(Request $request): JsonResponse
    {
        $data = $request->all();
        if (($data['country'] ?? null) === 'de') {
            $data['language'] = 'de';
        }

        $validator = Validator::make($data, [
            'country' => 'required|string|in:de,uk,ie',
            'industry' => 'required|string|in:beauty,physio,coaching',
            'language' => 'required|string|in:de,en',
            'is_local_business' => 'required|boolean',
            'marketing_time_per_week' => 'required|integer|in:2,4',
            
            'business_goals_defined' => 'required|boolean',
            'marketing_goals_defined' => 'required|boolean',
            
            'google_business_claimed' => 'nullable|boolean',
            'core_directories_claimed' => 'nullable|boolean',
            'industry_directories_claimed' => 'nullable|boolean',
            'business_directories_claimed' => 'nullable|boolean',
            
            'has_website' => 'required|boolean',
            'email_marketing_tool' => 'required|boolean',
            'crm_pipeline' => 'required|boolean',
            'running_ads' => 'nullable|array',
            'running_ads.*' => 'string|in:retargeting,paid_search,prospecting_social',
            
            'has_primary_social_channel' => 'required|boolean',
            'primary_social_channel' => 'nullable|string|in:instagram,facebook,linkedin,tiktok,youtube,twitter',
            'has_secondary_social_channel' => 'required|boolean',
            'secondary_social_channel' => 'nullable|string|in:instagram,facebook,linkedin,tiktok,youtube,twitter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $language = $data['language'];

            if ($user) {
                $user->language = $language;
                $user->save();
            }

            $industryLabels = [
                'beauty' => 'Beauty',
                'physio' => 'Physio',
                'coaching' => 'Coaching',
            ];
            
            $planTitle = 'Marketing Plan for ' . ($industryLabels[$request->industry] ?? $request->industry);
            
            $runningAds = $request->input('running_ads');

            if (!is_array($runningAds) || empty($runningAds)) {
                $runningAds = [];
            }
            
            $plan = Plan::create([
                'user_id' => Auth::id(),
                'title' => $planTitle,
                'country' => $data['country'],
                'language' => $language,
                'is_local_business' => $data['is_local_business'],
                'has_website' => $data['has_website'],
                'marketing_time_per_week' => $data['marketing_time_per_week'],
                'questionnaire_data' => $data,
                
                'industries' => [$data['industry']],
                'business_goals_defined' => $data['business_goals_defined'],
                'marketing_goals_defined' => $data['marketing_goals_defined'],
                'google_business_claimed' => $data['google_business_claimed'] ?? false,
                'core_directories_claimed' => $data['core_directories_claimed'] ?? false,
                'industry_directories_claimed' => $data['industry_directories_claimed'] ?? false,
                'business_directories_claimed' => $data['business_directories_claimed'] ?? false,
                'email_marketing_tool' => $data['email_marketing_tool'],
                'crm_pipeline' => $data['crm_pipeline'],
                'running_ads' => $runningAds,
                'has_primary_social_channel' => $data['has_primary_social_channel'],
                'primary_social_channel' => $data['primary_social_channel'] ?? null,
                'has_secondary_social_channel' => $data['has_secondary_social_channel'],
                'secondary_social_channel' => $data['secondary_social_channel'] ?? null,
            ]);

            $plan->refresh();
            $generatedPlan = $this->planGenerator->generatePlan($plan);

            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully',
                'plan' => [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'tasks' => $generatedPlan['tasks'] ?? [],
                    'total_tasks' => $generatedPlan['total_tasks'] ?? 0,
                    'total_minutes' => $generatedPlan['total_minutes'] ?? 0,
                ]
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating plan: ' . $e->getMessage()
            ], 500);
        }
    }
}