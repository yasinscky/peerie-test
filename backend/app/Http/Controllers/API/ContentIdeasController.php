<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContentIdea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentIdeasController extends Controller
{
    private function audienceFromUser(?\App\Models\User $user): ?string
    {
        if (!$user) {
            return null;
        }

        $plan = $user->latestPlan;
        if (!$plan) {
            return null;
        }

        $industry = data_get($plan, 'questionnaire_data.industry');
        $country = data_get($plan, 'questionnaire_data.country');

        if (!is_string($industry) || !is_string($country) || $industry === '' || $country === '') {
            return null;
        }

        $industrySegment = match ($industry) {
            'coaching' => 'coaches',
            'physio' => 'physio',
            'beauty' => 'beauty',
            default => null,
        };

        $countrySegment = match ($country) {
            'de' => 'de',
            'uk' => 'uk',
            'ie' => 'ie',
            default => null,
        };

        if (!$industrySegment || !$countrySegment) {
            return null;
        }

        return $industrySegment . '_' . $countrySegment;
    }

    public function getAvailableMonths(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        $now = \Carbon\Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        $currentMonthDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
        $nextMonthDate = $currentMonthDate->copy()->addMonth();

        $months = [
            [
                'year' => (int) $currentYear,
                'month' => (int) $currentMonth,
            ],
            [
                'year' => (int) $nextMonthDate->year,
                'month' => (int) $nextMonthDate->month,
            ],
        ];

        return response()->json([
            'success' => true,
            'months' => $months
        ]);
    }

    public function getByDate(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        $request->validate([
            'date' => 'required|date',
        ]);

        $date = \Carbon\Carbon::parse($request->input('date'))->format('Y-m-d');
        $userLanguage = $user->language ?? 'en';
        $audience = $this->audienceFromUser($user);

        $contentIdea = null;

        if ($audience) {
            $contentIdea = ContentIdea::where('date', $date)
                ->where('audience', $audience)
                ->first();
        }

        if (!$contentIdea) {
            $contentIdea = ContentIdea::where('date', $date)
                ->where('language', $userLanguage)
                ->first();
        }

        if (!$contentIdea) {
            return response()->json([
                'success' => false,
                'message' => 'Content idea not found for this date'
            ], 404);
        }

        $date = $contentIdea->date instanceof \Carbon\Carbon 
            ? $contentIdea->date->format('Y-m-d')
            : \Carbon\Carbon::parse($contentIdea->date)->format('Y-m-d');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $contentIdea->id,
                'date' => $date,
                'title' => $contentIdea->title,
                'caption' => $contentIdea->caption,
                'hashtags' => $contentIdea->hashtags,
                'tips' => $contentIdea->tips,
            ]
        ]);
    }
}
