<?php

namespace App\Filament\Resources\ContentIdeaResource\Pages;

use App\Filament\Resources\ContentIdeaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContentIdea extends CreateRecord
{
    protected static string $resource = ContentIdeaResource::class;

    protected function afterFill(): void
    {
        $activeTab = request()->query('activeTab');

        if (!is_string($activeTab) || $activeTab === '') {
            return;
        }

        $this->form->fill([
            'audiences' => [$activeTab],
        ]);
    }

    protected function getRedirectUrl(): string
    {
        $audience = $this->record?->audiences[0] ?? null;

        return ContentIdeaResource::getUrl('index', [
            'activeTab' => is_string($audience) && $audience !== '' ? $audience : null,
        ]);
    }
}
