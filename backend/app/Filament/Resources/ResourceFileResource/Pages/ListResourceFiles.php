<?php

namespace App\Filament\Resources\ResourceFileResource\Pages;

use App\Filament\Resources\ResourceFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResourceFiles extends ListRecords
{
    protected static string $resource = ResourceFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
