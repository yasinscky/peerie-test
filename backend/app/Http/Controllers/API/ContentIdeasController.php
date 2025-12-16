<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContentIdea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentIdeasController extends Controller
{
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

        $contentIdea = ContentIdea::where('date', $date)
            ->where('language', $userLanguage)
            ->first();

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
