<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Task::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        if ($request->has('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        if ($request->has('language')) {
            $query->where('language', $request->language);
        }

        $tasks = $query->orderBy('category')
                      ->orderBy('title')
                      ->paginate(20);

        return response()->json([
            'success' => true,
            'tasks' => TaskResource::collection($tasks->items()),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ]
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        return response()->json([
            'success' => true,
            'task' => new TaskResource($task)
        ]);
    }
}