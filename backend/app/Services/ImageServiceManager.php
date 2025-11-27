<?php

namespace App\Services;

class ImageServiceManager
{
    private array $services;

    public function __construct(
        PexelsImageService $pexelsService,
        UnsplashImageService $unsplashService,
        PixabayImageService $pixabayService
    ) {
        $this->services = [
            'pexels' => $pexelsService,
            'unsplash' => $unsplashService,
            'pixabay' => $pixabayService
        ];
    }

    /**
     * Search images using all services simultaneously
     */
    public function searchImages(string $query, int $page = 1, int $perPage = 20): array
    {
        $results = [];
        $allImages = [];
        $totalResults = 0;
        $servicesUsed = [];
        
        foreach ($this->services as $serviceName => $service) {
            try {
                $result = $service->searchImages($query, $page, $perPage);
                if ($result['success'] && !empty($result['images'])) {
                    $allImages = array_merge($allImages, $result['images']);
                    $totalResults += $result['total'] ?? 0;
                    $servicesUsed[] = $serviceName;
                }
                $results[$serviceName] = $result;
            } catch (\Exception $e) {
                $results[$serviceName] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        $perServicePerPage = ceil($perPage / count($this->services));
        $hasNextPage = count($allImages) >= $perPage;
        
        return [
            'success' => !empty($allImages),
            'images' => array_slice($allImages, 0, $perPage),
            'total' => $totalResults,
            'page' => $page,
            'perPage' => $perPage,
            'hasNextPage' => $hasNextPage,
            'service_used' => implode(', ', $servicesUsed),
            'all_results' => $results
        ];
    }

    /**
     * Search by category using all services simultaneously
     */
    public function searchByCategory(string $category, int $page = 1, int $perPage = 16): array
    {
        $results = [];
        $allImages = [];
        $totalResults = 0;
        $servicesUsed = [];
        
        foreach ($this->services as $serviceName => $service) {
            try {
                $result = $service->searchByCategory($category, $page, $perPage);
                if ($result['success'] && !empty($result['images'])) {
                    $allImages = array_merge($allImages, $result['images']);
                    $totalResults += $result['total'] ?? 0;
                    $servicesUsed[] = $serviceName;
                }
                $results[$serviceName] = $result;
            } catch (\Exception $e) {
                $results[$serviceName] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        $hasNextPage = count($allImages) >= $perPage;
        
        return [
            'success' => !empty($allImages),
            'images' => array_slice($allImages, 0, $perPage),
            'total' => $totalResults,
            'page' => $page,
            'perPage' => $perPage,
            'hasNextPage' => $hasNextPage,
            'service_used' => implode(', ', $servicesUsed),
            'all_results' => $results
        ];
    }

    /**
     * Get popular images using all services simultaneously
     */
    public function getPopularImages(int $page = 1, int $perPage = 20): array
    {
        $results = [];
        $allImages = [];
        $totalResults = 0;
        $servicesUsed = [];
        
        foreach ($this->services as $serviceName => $service) {
            try {
                $result = $service->getPopularImages($page, $perPage);
                if ($result['success'] && !empty($result['images'])) {
                    $allImages = array_merge($allImages, $result['images']);
                    $totalResults += $result['total'] ?? 0;
                    $servicesUsed[] = $serviceName;
                }
                $results[$serviceName] = $result;
            } catch (\Exception $e) {
                $results[$serviceName] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        $hasNextPage = count($allImages) >= $perPage;
        
        return [
            'success' => !empty($allImages),
            'images' => array_slice($allImages, 0, $perPage),
            'total' => $totalResults,
            'page' => $page,
            'perPage' => $perPage,
            'hasNextPage' => $hasNextPage,
            'service_used' => implode(', ', $servicesUsed),
            'all_results' => $results
        ];
    }


    /**
     * Get specific service by name
     */
    public function getService(string $serviceName): ?object
    {
        return $this->services[$serviceName] ?? null;
    }

    /**
     * Get all available services
     */
    public function getAvailableServices(): array
    {
        return array_keys($this->services);
    }
}
