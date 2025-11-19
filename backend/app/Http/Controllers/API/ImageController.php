<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageServiceManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
                'message' => $result['success'] ? 'Изображения найдены' : ($result['error'] ?? 'Ошибка поиска')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при поиске изображений',
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
            $result = $this->imageServiceManager->searchByCategory($category, $page, $perPage);
            
            return response()->json([
                'success' => $result['success'],
                'data' => $result,
                'message' => $result['success'] ? 'Изображения найдены' : ($result['error'] ?? 'Ошибка поиска')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при поиске изображений',
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
                'message' => $result['success'] ? 'Популярные изображения загружены' : ($result['error'] ?? 'Ошибка загрузки')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при загрузке изображений',
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
                    'message' => 'Изображение не найдено'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $image,
                'message' => 'Информация об изображении получена'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении информации об изображении',
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
                    'message' => 'Изображение не найдено'
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
                'message' => 'URL для скачивания получен'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении URL для скачивания',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Скачать изображение через прокси сервера
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
                    'message' => 'Изображение недоступно'
                ], 404);
            }

            $imageContent = file_get_contents($imageUrl);
            
            if ($imageContent === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось загрузить изображение'
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
                'message' => 'Произошла ошибка при скачивании изображения',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
