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
     * Search images using random service rotation
     */
    public function searchImages(string $query, int $page = 1, int $perPage = 20): array
    {
        $serviceName = $this->getNextService();
        $service = $this->services[$serviceName];
        
        $result = $service->searchImages($query, $page, $perPage);
        $result['service_used'] = $serviceName;
        
        return $result;
    }

    /**
     * Search by category using random service rotation
     */
    public function searchByCategory(string $category, int $page = 1, int $perPage = 16): array
    {
        $serviceName = $this->getNextService();
        $service = $this->services[$serviceName];
        
        $result = $service->searchByCategory($category, $page, $perPage);
        $result['service_used'] = $serviceName;
        
        return $result;
    }

    /**
     * Get popular images using random service rotation
     */
    public function getPopularImages(int $page = 1, int $perPage = 20): array
    {
        $serviceName = $this->getNextService();
        $service = $this->services[$serviceName];
        
        $result = $service->getPopularImages($page, $perPage);
        $result['service_used'] = $serviceName;
        
        return $result;
    }

    /**
     * Get random service
     */
    private function getNextService(): string
    {
        $serviceNames = array_keys($this->services);
        return $serviceNames[array_rand($serviceNames)];
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
