<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ImageServiceManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    private ImageServiceManager $imageServiceManager;

    public function __construct(ImageServiceManager $imageServiceManager)
    {
        $this->imageServiceManager = $imageServiceManager;
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|max:255',
            'page' => 'integer|min:1|max:80',
            'per_page' => 'integer|min:1|max:80'
        ]);

        $query = $request->input('query');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        try {
            $result = $this->imageServiceManager->searchImages($query, $page, $perPage);
            
            return response()->json([
                'success' => $result['success'],
                'data' => $result,
                'message' => $result['success'] ? 'Images found' : ($result['error'] ?? 'Search error')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchByCategory(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'page' => 'integer|min:1|max:80',
            'per_page' => 'integer|min:1|max:80'
        ]);

        $category = $request->input('category');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 16);

        try {
            Log::info('Image search by category request', [
                'category' => $category,
                'page' => $page,
                'per_page' => $perPage
            ]);
            
            $result = $this->imageServiceManager->searchByCategory($category, $page, $perPage);
            
            Log::info('Image search by category result', [
                'success' => $result['success'] ?? false,
                'images_count' => count($result['images'] ?? []),
                'error' => $result['error'] ?? null,
                'service_used' => $result['service_used'] ?? null
            ]);
            
            return response()->json([
                'success' => $result['success'],
                'data' => $result,
                'message' => $result['success'] ? 'Images found' : ($result['error'] ?? 'Search error')
            ]);

        } catch (\Exception $e) {
            Log::error('Image search by category exception', [
                'category' => $category,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function popular(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'integer|min:1|max:80',
            'per_page' => 'integer|min:1|max:80'
        ]);

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        try {
            $result = $this->imageServiceManager->getPopularImages($page, $perPage);
            
            return response()->json([
                'success' => $result['success'],
                'data' => $result,
                'message' => $result['success'] ? 'Popular images loaded' : ($result['error'] ?? 'Load error')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getImage(Request $request, int $id): JsonResponse
    {
        try {
            $image = null;
            foreach ($this->imageServiceManager->getAvailableServices() as $serviceName) {
                $service = $this->imageServiceManager->getService($serviceName);
                if (method_exists($service, 'getImageById')) {
                    $image = $service->getImageById($id);
                    if ($image) break;
                }
            }
            
            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $image,
                'message' => 'Image information loaded'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading image information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function download(Request $request, int $id): JsonResponse
    {
        try {
            $image = null;
            foreach ($this->imageServiceManager->getAvailableServices() as $serviceName) {
                $service = $this->imageServiceManager->getService($serviceName);
                if (method_exists($service, 'getImageById')) {
                    $image = $service->getImageById($id);
                    if ($image) break;
                }
            }
            
            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'downloadUrl' => $image['downloadUrl'],
                    'filename' => 'pexels-' . $id . '.jpg',
                    'title' => $image['title'],
                    'photographer' => $image['photographer']
                ],
                'message' => 'Download URL received'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while getting download URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download image via server proxy
     */
    public function downloadProxy(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'filename' => 'string|max:255'
        ]);

        $imageUrl = $request->input('url');
        $filename = $request->input('filename', 'image.jpg');

        try {
            $headers = get_headers($imageUrl, 1);
            if (!$headers || !str_contains($headers[0], '200')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image is unavailable'
                ], 404);
            }

            $imageContent = file_get_contents($imageUrl);
            
            if ($imageContent === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to download image'
                ], 500);
            }

            $mimeType = 'image/jpeg';
            if (isset($headers['Content-Type'])) {
                $mimeType = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
            }

            return response()->stream(function () use ($imageContent) {
                echo $imageContent;
            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($imageContent),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while downloading image',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
