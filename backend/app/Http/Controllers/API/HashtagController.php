<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Hashtag;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HashtagController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        $plan = Plan::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first(['country', 'industries', 'language']);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        $country = $plan->country;
        $industry = is_array($plan->industries) && count($plan->industries) > 0 ? $plan->industries[0] : null;
        $language = $plan->language ?? 'en';

        if (!$country || !$industry) {
            return response()->json([
                'success' => false,
                'message' => 'Industry or country is missing in the plan'
            ], 422);
        }

        // 1. Exact match by country + industry + language
        $hashtags = Hashtag::where('industry', $industry)
            ->where('country', $country)
            ->where('language', $language)
            ->first(['title', 'intro_title', 'intro_description', 'guidelines', 'tags', 'hashtag_blocks']);

        // 2. If there is no required language for this country, take any tags for the same country and industry
        if (!$hashtags) {
            $hashtags = Hashtag::where('industry', $industry)
                ->where('country', $country)
                ->first(['title', 'intro_title', 'intro_description', 'guidelines', 'tags', 'hashtag_blocks']);
        }

        // 3. If there is nothing for this country, try by industry and language (different country, same language)
        if (!$hashtags) {
            $hashtags = Hashtag::where('industry', $industry)
                ->where('language', $language)
                ->first(['title', 'intro_title', 'intro_description', 'guidelines', 'tags', 'hashtag_blocks']);
        }

        // 4. Final fallback – any tags by industry
        if (!$hashtags) {
            $hashtags = Hashtag::where('industry', $industry)
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


