<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PexelsImageService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.pexels.com/v1';

    public function __construct()
    {
        $this->apiKey = env('PEXELS_API_KEY', 'NKFAg9kOaZDFWn6tSDABVyGC2goD5QC7QPhMpNePLAyswOeaUja0Kk4p');
    }

    /**
     * Поиск изображений по ключевому слову
     */
    public function searchImages(string $query, int $page = 1, int $perPage = 20): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/search', [
                'query' => $query,
                'page' => $page,
                'per_page' => $perPage,
                'orientation' => 'all',
                'size' => 'all',
                'color' => 'all',
                'locale' => 'en-US'
            ]);

            if (!$response->successful()) {
                Log::error('Pexels API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return $this->getEmptyResponse();
            }

            $data = $response->json();
            return $this->transformResponse($data);

        } catch (\Exception $e) {
            Log::error('Pexels API exception', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);
            return $this->getEmptyResponse();
        }
    }

    /**
     * Поиск изображений по категории
     */
    public function searchByCategory(string $category, int $page = 1, int $perPage = 16): array
    {
        $categoryKeywords = [
            'Business' => 'business office work',
            'Technology' => 'technology computer digital',
            'Nature' => 'nature landscape forest',
            'People' => 'people portrait human',
            'Food' => 'food restaurant meal',
            'Sports' => 'sport fitness exercise',
            'Travel' => 'travel vacation journey',
            'Architecture' => 'architecture building design',
            'Art' => 'art creative artistic',
            'Animals' => 'animals pets wildlife'
        ];

        $keywords = $categoryKeywords[$category] ?? $category;
        return $this->searchImages($keywords, $page, $perPage);
    }

    /**
     * Получить популярные изображения
     */
    public function getPopularImages(int $page = 1, int $perPage = 20): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/curated', [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            if (!$response->successful()) {
                return $this->getEmptyResponse();
            }

            $data = $response->json();
            return $this->transformResponse($data);

        } catch (\Exception $e) {
            Log::error('Pexels API exception for popular images', [
                'message' => $e->getMessage()
            ]);
            return $this->getEmptyResponse();
        }
    }

    /**
     * Трансформировать ответ от Pexels API в наш формат
     */
    private function transformResponse(array $data): array
    {
        $images = [];

        if (!isset($data['photos']) || !is_array($data['photos'])) {
            return $this->getEmptyResponse();
        }

        foreach ($data['photos'] as $photo) {
            $images[] = [
                'id' => $photo['id'],
                'title' => $photo['alt'] ?? 'Untitled',
                'alt' => $photo['alt'] ?? '',
                'provider' => 'Pexels',
                'width' => $photo['width'],
                'height' => $photo['height'],
                'previewUrl' => $photo['src']['medium'] ?? $photo['src']['small'] ?? $photo['src']['tiny'],
                'fullUrl' => $photo['src']['large'] ?? $photo['src']['medium'] ?? $photo['src']['original'],
                'downloadUrl' => $photo['src']['original'],
                'photographer' => $photo['photographer'] ?? 'Unknown',
                'photographerUrl' => $photo['photographer_url'] ?? null,
                'url' => $photo['url'] ?? null,
                'loaded' => false,
                'error' => false
            ];
        }

        $currentPage = $data['page'] ?? 1;
        $perPage = $data['per_page'] ?? count($images);
        $totalResults = $data['total_results'] ?? count($images);
        
        return [
            'success' => true,
            'images' => $images,
            'total' => $totalResults,
            'page' => $currentPage,
            'perPage' => $perPage,
            'hasNextPage' => ($currentPage * $perPage) < $totalResults
        ];
    }

    /**
     * Получить пустой ответ при ошибке
     */
    private function getEmptyResponse(): array
    {
        return [
            'success' => false,
            'images' => [],
            'total' => 0,
            'page' => 1,
            'perPage' => 0,
            'hasNextPage' => false,
            'error' => 'Failed to load images'
        ];
    }

    /**
     * Получить информацию об изображении по ID
     */
    public function getImageById(int $id): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/photos/' . $id);

            if (!$response->successful()) {
                return null;
            }

            $photo = $response->json();
            $transformed = $this->transformResponse(['photos' => [$photo]]);
            
            return $transformed['images'][0] ?? null;

        } catch (\Exception $e) {
            Log::error('Pexels API exception for image by ID', [
                'message' => $e->getMessage(),
                'id' => $id
            ]);
            return null;
        }
    }
}
