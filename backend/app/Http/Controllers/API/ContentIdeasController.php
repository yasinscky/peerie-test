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

    public function getByMonth(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = (int) $request->input('year');
        $month = (int) $request->input('month');
        
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $userLanguage = $user->language ?? 'en';
        $audience = $this->audienceFromUser($user);

        $query = ContentIdea::whereBetween('date', [$startDate, $endDate]);

        if ($audience) {
            $query->where(function ($q) use ($audience, $userLanguage) {
                $q->whereJsonContains('audiences', $audience)
                  ->orWhere(function ($subQuery) use ($userLanguage) {
                      $subQuery->where('language', $userLanguage)
                               ->where(function ($innerQuery) {
                                   $innerQuery->whereNull('audiences')
                                              ->orWhereRaw("audiences = '[]'::jsonb");
                               });
                  });
            });
        } else {
            $query->where('language', $userLanguage)
                  ->where(function ($subQuery) {
                      $subQuery->whereNull('audiences')
                               ->orWhereRaw("audiences = '[]'::jsonb");
                  });
        }

        $contentIdeas = $query->orderBy('date', 'asc')->get();

        $ideas = $contentIdeas->map(function ($idea) {
            $date = $idea->date instanceof \Carbon\Carbon 
                ? $idea->date->format('Y-m-d')
                : \Carbon\Carbon::parse($idea->date)->format('Y-m-d');

            return [
                'id' => $idea->id,
                'date' => $date,
                'title' => $idea->title,
                'caption' => $idea->caption,
                'hashtags' => $idea->hashtags,
                'tips' => $idea->tips,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $ideas
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
                ->whereJsonContains('audiences', $audience)
                ->first();
        }

        if (!$contentIdea) {
            $contentIdea = ContentIdea::where('date', $date)
                ->where('language', $userLanguage)
                ->where(function ($query) {
                    $query
                        ->whereNull('audiences')
                        ->orWhereRaw("audiences = '[]'::jsonb");
                })
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
