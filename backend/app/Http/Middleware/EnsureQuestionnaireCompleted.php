<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureQuestionnaireCompleted
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated',
            ], 401);
        }

        if ($user->plans()->exists()) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'requires_questionnaire' => true,
            'message' => 'Questionnaire is required',
        ], 403);
    }
}


