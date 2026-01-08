<?php

namespace App\Filament\Resources\ResourceFileResource\Pages;

use App\Filament\Resources\ResourceFileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateResourceFile extends CreateRecord
{
    protected static string $resource = ResourceFileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['file']) && is_array($data['file']) && count($data['file']) > 0) {
            $filePath = $data['file'][0];
            $data['file_path'] = $filePath;
            
            if (!isset($data['original_filename']) || empty($data['original_filename'])) {
                $data['original_filename'] = basename($filePath);
            }
        } else if (isset($data['file']) && is_string($data['file']) && !empty($data['file'])) {
            $data['file_path'] = $data['file'];
            if (!isset($data['original_filename']) || empty($data['original_filename'])) {
                $data['original_filename'] = basename($data['file']);
            }
        }
        
        unset($data['file']);
        
        return $data;
    }
}
