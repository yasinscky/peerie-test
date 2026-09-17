<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\WorksheetRegistry;
use App\Services\WorksheetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorksheetController extends Controller
{
    public function __construct(private WorksheetService $worksheets)
    {
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User is not authenticated'], 401);
        }

        return response()->json([
            'success' => true,
            'worksheets' => $this->worksheets->statusForUser($user),
            'catalog' => $this->worksheets->catalog($user->language ?? 'en'),
        ]);
    }

    public function show(string $kind): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User is not authenticated'], 401);
        }

        $kind = $this->resolveKind($kind);
        if (!$kind) {
            return response()->json(['success' => false, 'message' => 'Unknown worksheet'], 404);
        }

        $planTask = $this->worksheets->findPlanTask($user, $kind);
        $document = $planTask
            ? $this->worksheets->documentFromNotes($kind, $planTask->notes)
            : $this->worksheets->emptyDocument($kind);

        return response()->json([
            'success' => true,
            'kind' => $kind,
            'slug' => WorksheetRegistry::kindToSlug($kind),
            'document' => $document,
            'fields' => $document['fields'] ?? [],
            'filled' => $this->worksheets->isFilled($kind, $document),
            'plan_id' => $planTask?->plan_id,
            'plan_task_id' => $planTask?->id,
            'task_title' => $planTask?->task?->title,
            'schema' => $this->worksheets->frontendSchema($kind, $user->language ?? 'en'),
        ]);
    }

    public function update(Request $request, string $kind): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User is not authenticated'], 401);
        }

        $kind = $this->resolveKind($kind);
        if (!$kind) {
            return response()->json(['success' => false, 'message' => 'Unknown worksheet'], 404);
        }

        $validator = Validator::make($request->all(), [
            'fields' => 'nullable|array',
            'fields.*' => 'nullable|string|max:8000',
            'rows' => 'nullable|array',
            'rows.*' => 'nullable|array',
            'rows.*.*' => 'nullable|string|max:2000',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|array',
            'categories.*.*' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $planTask = $this->worksheets->findPlanTask($user, $kind);
        if (!$planTask) {
            return response()->json([
                'success' => false,
                'message' => 'Related task was not found in your plan',
            ], 404);
        }

        $document = $this->worksheets->saveDocument($planTask, $kind, $request->all());

        return response()->json([
            'success' => true,
            'kind' => $kind,
            'document' => $document,
            'fields' => $document['fields'] ?? [],
            'filled' => $this->worksheets->isFilled($kind, $document),
            'plan_id' => $planTask->plan_id,
            'plan_task_id' => $planTask->id,
        ]);
    }

    public function export(Request $request, string $kind): Response|JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User is not authenticated'], 401);
        }

        $kind = $this->resolveKind($kind);
        if (!$kind) {
            return response()->json(['success' => false, 'message' => 'Unknown worksheet'], 404);
        }

        $format = strtolower((string) $request->query('format', 'pdf'));
        if (!in_array($format, ['pdf', 'docx'], true)) {
            return response()->json(['success' => false, 'message' => 'Unsupported format'], 422);
        }

        $planTask = $this->worksheets->findPlanTask($user, $kind);
        $document = $planTask
            ? $this->worksheets->documentFromNotes($kind, $planTask->notes)
            : $this->worksheets->emptyDocument($kind);

        $language = $user->language ?? 'en';
        $filename = $this->worksheets->filename($kind);
        $binary = $this->worksheets->export($kind, $document, $language, $format);
        if ($binary === '') {
            return response()->json(['success' => false, 'message' => 'Could not create file'], 500);
        }

        if ($format === 'docx') {
            return response($binary, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => "attachment; filename=\"{$filename}.docx\"",
            ]);
        }

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}.pdf\"",
        ]);
    }

    private function resolveKind(string $kind): ?string
    {
        $normalized = str_replace('-', '_', $kind);
        if (WorksheetRegistry::get($normalized) || $this->worksheets->hasKind($normalized)) {
            return $normalized;
        }

        return WorksheetRegistry::slugToKind($kind);
    }
}
