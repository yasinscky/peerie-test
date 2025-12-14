<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ResourceFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        
        $resourceFile = ResourceFile::where('language', $language)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$resourceFile) {
            return response()->json([
                'success' => false,
                'message' => 'Resource file not found for your language'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'resource' => [
                'id' => $resourceFile->id,
                'title' => $resourceFile->title,
                'language' => $resourceFile->language,
                'filename' => $resourceFile->original_filename,
            ]
        ]);
    }

    public function download(): StreamedResponse|JsonResponse
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
            ->orderBy('created_at', 'desc')
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

        return Storage::disk('local')->download(
            $resourceFile->file_path,
            $resourceFile->original_filename
        );
    }
}
