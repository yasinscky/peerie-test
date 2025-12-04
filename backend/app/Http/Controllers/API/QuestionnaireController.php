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
        $validator = Validator::make($request->all(), [
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

            if ($user) {
                $user->language = $request->language;
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
                'country' => $request->country,
                'language' => $request->language,
                'is_local_business' => $request->is_local_business,
                'has_website' => $request->has_website,
                'marketing_time_per_week' => $request->marketing_time_per_week,
                'questionnaire_data' => $request->all(),
                
                'industries' => [$request->industry],
                'business_goals_defined' => $request->business_goals_defined,
                'marketing_goals_defined' => $request->marketing_goals_defined,
                'google_business_claimed' => $request->google_business_claimed ?? false,
                'core_directories_claimed' => $request->core_directories_claimed ?? false,
                'industry_directories_claimed' => $request->industry_directories_claimed ?? false,
                'business_directories_claimed' => $request->business_directories_claimed ?? false,
                'email_marketing_tool' => $request->email_marketing_tool,
                'crm_pipeline' => $request->crm_pipeline,
                'running_ads' => $runningAds,
                'has_primary_social_channel' => $request->has_primary_social_channel,
                'primary_social_channel' => $request->primary_social_channel,
                'has_secondary_social_channel' => $request->has_secondary_social_channel,
                'secondary_social_channel' => $request->secondary_social_channel,
            ]);

            $generatedPlan = $this->planGenerator->generatePlan($plan);

            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully',
                'plan' => [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'weeks' => $generatedPlan['weeks'] ?? [],
                    'total_tasks' => $generatedPlan['total_tasks'] ?? 0,
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