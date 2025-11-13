<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PixabayImageService
{
    private string $apiKey;
    private string $baseUrl = 'https://pixabay.com/api';

    public function __construct()
    {
        $this->apiKey = env('PIXABAY_API_KEY', '51460067-6d12d6f7d8eea23b761dc65cb');
    }

    /**
     * Search images by keyword
     */
    public function searchImages(string $query, int $page = 1, int $perPage = 20): array
    {
        try {
            $response = Http::get($this->baseUrl, [
                'key' => $this->apiKey,
                'q' => $query,
                'image_type' => 'photo',
                'orientation' => 'all',
                'category' => 'all',
                'min_width' => 0,
                'min_height' => 0,
                'colors' => 'all',
                'safesearch' => 'true',
                'page' => $page,
                'per_page' => min($perPage, 200), // Pixabay max is 200
                'lang' => 'en'
            ]);

            if (!$response->successful()) {
                Log::error('Pixabay API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return $this->getEmptyResponse();
            }

            $data = $response->json();
            return $this->transformResponse($data);

        } catch (\Exception $e) {
            Log::error('Pixabay API exception', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);
            return $this->getEmptyResponse();
        }
    }

    /**
     * Search images by category
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
     * Get popular images (using featured search)
     */
    public function getPopularImages(int $page = 1, int $perPage = 20): array
    {
        try {
            $response = Http::get($this->baseUrl, [
                'key' => $this->apiKey,
                'q' => 'popular trending',
                'image_type' => 'photo',
                'orientation' => 'all',
                'category' => 'all',
                'safesearch' => 'true',
                'page' => $page,
                'per_page' => min($perPage, 200),
                'lang' => 'en'
            ]);

            if (!$response->successful()) {
                return $this->getEmptyResponse();
            }

            $data = $response->json();
            return $this->transformResponse($data);

        } catch (\Exception $e) {
            Log::error('Pixabay API exception for popular images', [
                'message' => $e->getMessage()
            ]);
            return $this->getEmptyResponse();
        }
    }

    /**
     * Transform Pixabay API response to our format
     */
    private function transformResponse(array $data): array
    {
        $images = [];

        if (!isset($data['hits']) || !is_array($data['hits'])) {
            return $this->getEmptyResponse();
        }

        foreach ($data['hits'] as $hit) {
            $images[] = [
                'id' => $hit['id'],
                'title' => $hit['tags'] ?? 'Untitled',
                'alt' => $hit['tags'] ?? '',
                'provider' => 'Pixabay',
                'width' => $hit['imageWidth'],
                'height' => $hit['imageHeight'],
                'previewUrl' => $hit['previewURL'],
                'fullUrl' => $hit['webformatURL'],
                'downloadUrl' => $hit['largeImageURL'],
                'photographer' => $hit['user'] ?? 'Unknown',
                'photographerUrl' => $hit['pageURL'] ?? null,
                'url' => $hit['pageURL'] ?? null,
                'loaded' => false,
                'error' => false
            ];
        }

        $currentPage = $data['page'] ?? 1;
        $perPage = count($images);
        $totalHits = $data['totalHits'] ?? 0;
        
        return [
            'success' => true,
            'images' => $images,
            'total' => $totalHits,
            'page' => $currentPage,
            'perPage' => $perPage,
            'hasNextPage' => ($currentPage * $perPage) < $totalHits && $perPage > 0
        ];
    }

    /**
     * Get empty response on error
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
}
