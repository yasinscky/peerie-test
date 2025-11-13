<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hashtag;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HashtagController extends Controller
{
    /**
     * Return hashtags for the latest user plan by industry and country.
     */
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        $plan = Plan::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first(['country', 'industries']);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        $country = $plan->country; // ie|uk|de
        $industry = is_array($plan->industries) && count($plan->industries) > 0 ? $plan->industries[0] : null; // beauty|physio|coaching
        $language = $plan->language ?? 'en'; // en|de

        if (!$country || !$industry) {
            return response()->json([
                'success' => false,
                'message' => 'Industry or country is missing in the plan'
            ], 422);
        }

        $hashtags = Hashtag::where('country', $country)
            ->where('industry', $industry)
            ->where('language', $language)
            ->first(['title', 'intro_title', 'intro_description', 'guidelines', 'tags', 'hashtag_blocks']);

        // Fallbacks: если точного совпадения нет, пробуем по индустрии и языку без учета страны,
        // затем по индустрии и стране без учета языка, чтобы пользователь всегда получил рабочий список.
        if (!$hashtags) {
            $hashtags = Hashtag::where('industry', $industry)
                ->where('language', $language)
                ->orderByRaw("CASE country WHEN ? THEN 0 ELSE 1 END", [$country])
                ->first(['title', 'intro_title', 'intro_description', 'guidelines', 'tags', 'hashtag_blocks']);
        }
        
        if (!$hashtags) {
            $hashtags = Hashtag::where('country', $country)
                ->where('industry', $industry)
                ->orderByRaw("CASE language WHEN ? THEN 0 ELSE 1 END", [$language])
                ->first(['title', 'intro_title', 'intro_description', 'guidelines', 'tags', 'hashtag_blocks']);
        }
        
        if (!$hashtags) {
            $hashtags = Hashtag::where('industry', $industry)
                ->orderByRaw("CASE country WHEN ? THEN 0 ELSE 1 END", [$country])
                ->orderByRaw("CASE language WHEN ? THEN 0 ELSE 1 END", [$language])
                ->first(['title', 'intro_title', 'intro_description', 'guidelines', 'tags', 'hashtag_blocks']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $hashtags->title ?? null,
                'intro_title' => $hashtags->intro_title ?? null,
                'intro_description' => $hashtags->intro_description ?? null,
                'guidelines' => $hashtags->guidelines ?? [],
                'tags' => $hashtags->tags ?? [],
                'hashtag_blocks' => $hashtags->hashtag_blocks ?? [],
            ]
        ]);
    }
}


