<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnsplashImageService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.unsplash.com';

    public function __construct()
    {
        $this->apiKey = env('UNSPLASH_API_KEY', 'GvXuByG_Q371bMDYCkkoRj6f9K9HikxwWS91IgpJbMU');
    }

    /**
     * Search images by keyword
     */
    public function searchImages(string $query, int $page = 1, int $perPage = 20): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/search/photos', [
                'query' => $query,
                'page' => $page,
                'per_page' => $perPage,
                'orientation' => 'all',
                'order_by' => 'relevant'
            ]);

            if (!$response->successful()) {
                Log::error('Unsplash API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return $this->getEmptyResponse();
            }

            $data = $response->json();
            return $this->transformResponse($data);

        } catch (\Exception $e) {
            Log::error('Unsplash API exception', [
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
            'Business' => 'business office work team meeting startup marketing',
            'Coach' => 'coach coaching mentor consulting business training',
            'Therapy' => 'therapy therapist counseling mental health psychology',
            'Beauty' => 'beauty salon makeup skincare cosmetics hair stylist',
            'Style' => 'fashion style outfit clothes street style modern',
            'Nails' => 'nail salon manicure pedicure nails beauty hands',
            'Sports' => 'sport fitness exercise gym workout athlete',
            'Massage' => 'massage spa relaxation wellness body care',
            'Doctor' => 'doctor medical clinic hospital healthcare nurse',
        ];

        $keywords = $categoryKeywords[$category] ?? $category;
        return $this->searchImages($keywords, $page, $perPage);
    }

    /**
     * Get popular images
     */
    public function getPopularImages(int $page = 1, int $perPage = 20): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/photos', [
                'page' => $page,
                'per_page' => $perPage,
                'order_by' => 'popular'
            ]);

            if (!$response->successful()) {
                return $this->getEmptyResponse();
            }

            $data = $response->json();
            return $this->transformResponse($data);

        } catch (\Exception $e) {
            Log::error('Unsplash API exception for popular images', [
                'message' => $e->getMessage()
            ]);
            return $this->getEmptyResponse();
        }
    }

    /**
     * Transform Unsplash API response to our format
     */
    private function transformResponse(array $data): array
    {
        $images = [];

        if (!isset($data['results']) && !isset($data[0])) {
            return $this->getEmptyResponse();
        }

        $photos = $data['results'] ?? $data;

        foreach ($photos as $photo) {
            $images[] = [
                'id' => $photo['id'],
                'title' => $photo['alt_description'] ?? $photo['description'] ?? 'Untitled',
                'alt' => $photo['alt_description'] ?? '',
                'provider' => 'Unsplash',
                'width' => $photo['width'],
                'height' => $photo['height'],
                'previewUrl' => $photo['urls']['small'] ?? $photo['urls']['thumb'],
                'fullUrl' => $photo['urls']['regular'] ?? $photo['urls']['full'],
                'downloadUrl' => $photo['urls']['full'],
                'photographer' => $photo['user']['name'] ?? 'Unknown',
                'photographerUrl' => $photo['user']['links']['html'] ?? null,
                'url' => $photo['links']['html'] ?? null,
                'loaded' => false,
                'error' => false
            ];
        }

        $currentPage = $data['page'] ?? 1;
        $totalPages = $data['total_pages'] ?? 1;
        $perPage = count($images);
        
        return [
            'success' => true,
            'images' => $images,
            'total' => $data['total'] ?? count($images),
            'page' => $currentPage,
            'perPage' => $perPage,
            'hasNextPage' => $currentPage < $totalPages && $perPage > 0
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
