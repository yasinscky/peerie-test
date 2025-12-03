<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->before(function (Actions\DeleteAction $action) {
                    if (auth()->id() === $this->record->id) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('You cannot delete your own account')
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}

