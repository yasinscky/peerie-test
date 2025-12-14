<?php

namespace App\Filament\Resources\ResourceFileResource\Pages;

use App\Filament\Resources\ResourceFileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditResourceFile extends EditRecord
{
    protected static string $resource = ResourceFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    if ($record->file_path && Storage::disk('local')->exists($record->file_path)) {
                        Storage::disk('local')->delete($record->file_path);
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($this->record->file_path)) {
            $data['file'] = [$this->record->file_path];
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['file']) && is_array($data['file']) && count($data['file']) > 0) {
            $oldFilePath = $this->record->file_path;
            
            if ($oldFilePath && Storage::disk('local')->exists($oldFilePath)) {
                Storage::disk('local')->delete($oldFilePath);
            }
            
            $filePath = $data['file'][0];
            $data['file_path'] = $filePath;
            
            if (!isset($data['original_filename'])) {
                $data['original_filename'] = basename($filePath);
            }
        } else if (isset($data['file']) && is_string($data['file']) && !empty($data['file'])) {
            $oldFilePath = $this->record->file_path;
            
            if ($oldFilePath && Storage::disk('local')->exists($oldFilePath) && $oldFilePath !== $data['file']) {
                Storage::disk('local')->delete($oldFilePath);
            }
            
            $data['file_path'] = $data['file'];
            if (!isset($data['original_filename'])) {
                $data['original_filename'] = basename($data['file']);
            }
        } else {
            unset($data['file_path']);
        }
        
        unset($data['file']);
        
        return $data;
    }
}
