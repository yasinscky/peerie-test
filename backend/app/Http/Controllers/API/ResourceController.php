<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ResourceFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        $language = $user->language ?? 'en';
        
        $resourceFilesQuery = ResourceFile::where('language', $language)
            ->orderBy('sort_order')
            ->orderByRaw('COALESCE(published_at, created_at) DESC');

        $resourceFiles = $resourceFilesQuery->get();

        if ($resourceFiles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Resource file not found for your language'
            ], 404);
        }

        $latestResourceFile = $resourceFiles->first();

        return response()->json([
            'success' => true,
            'resource' => [
                'id' => $latestResourceFile->id,
                'title' => $latestResourceFile->title,
                'language' => $latestResourceFile->language,
                'filename' => $latestResourceFile->original_filename,
            ],
            'resources' => $resourceFiles->map(fn (ResourceFile $resourceFile) => [
                'id' => $resourceFile->id,
                'title' => $resourceFile->title,
                'language' => $resourceFile->language,
                'filename' => $resourceFile->original_filename,
            ])->values(),
        ]);
    }

    public function download(): BinaryFileResponse|JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        $language = $user->language ?? 'en';
        
        $resourceFile = ResourceFile::where('language', $language)
            ->orderBy('sort_order')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->first();

        if (!$resourceFile) {
            return response()->json([
                'success' => false,
                'message' => 'Resource file not found for your language'
            ], 404);
        }

        if (!Storage::disk('local')->exists($resourceFile->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found on server'
            ], 404);
        }

        $downloadName = $resourceFile->original_filename;
        if (is_string($downloadName)) {
            $downloadName = preg_replace('/[\x00-\x1F\x7F]/', '', $downloadName) ?? $downloadName;
            $downloadName = trim($downloadName);
            $downloadName = rtrim($downloadName, ". \t");
        }
        if (!is_string($downloadName) || $downloadName === '') {
            $downloadName = 'resource';
        }

        return response()->download(
            Storage::disk('local')->path($resourceFile->file_path),
            $downloadName
        );
    }

    public function downloadById(int $id): BinaryFileResponse|JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        $language = $user->language ?? 'en';

        $resourceFile = ResourceFile::where('id', $id)
            ->where('language', $language)
            ->first();

        if (!$resourceFile) {
            return response()->json([
                'success' => false,
                'message' => 'Resource file not found for your language'
            ], 404);
        }

        if (!Storage::disk('local')->exists($resourceFile->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found on server'
            ], 404);
        }

        $downloadName = $resourceFile->original_filename;
        if (is_string($downloadName)) {
            $downloadName = preg_replace('/[\x00-\x1F\x7F]/', '', $downloadName) ?? $downloadName;
            $downloadName = trim($downloadName);
            $downloadName = rtrim($downloadName, ". \t");
        }
        if (!is_string($downloadName) || $downloadName === '') {
            $downloadName = 'resource';
        }

        return response()->download(
            Storage::disk('local')->path($resourceFile->file_path),
            $downloadName
        );
    }
}
