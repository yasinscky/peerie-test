<?php

namespace App\Filament\Resources\ContentIdeaResource\Pages;

use App\Filament\Resources\ContentIdeaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListContentIdeas extends ListRecords
{
    protected static string $resource = ContentIdeaResource::class;

    public function getDefaultActiveTab(): string | int | null
    {
        return 'coaches_de';
    }

    public function getTabs(): array
    {
        return [
            'coaches_de' => Tab::make('Coaches DE')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'coaches_de')),
            'coaches_uk' => Tab::make('Coaches UK')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'coaches_uk')),
            'coaches_ie' => Tab::make('Coaches IRE')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'coaches_ie')),
            'physio_de' => Tab::make('Physio DE')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'physio_de')),
            'physio_uk' => Tab::make('Physio UK')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'physio_uk')),
            'physio_ie' => Tab::make('Physio IRE')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'physio_ie')),
            'beauty_de' => Tab::make('Beauty DE')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'beauty_de')),
            'beauty_uk' => Tab::make('Beauty UK')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'beauty_uk')),
            'beauty_ie' => Tab::make('Beauty IRE')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereJsonContains('audiences', 'beauty_ie')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(fn (): string => ContentIdeaResource::getUrl('create', ['activeTab' => $this->activeTab])),
        ];
    }
}
