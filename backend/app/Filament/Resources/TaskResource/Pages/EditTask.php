<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Преобразуем массив ID зависимостей в формат для Select
        if (isset($data['dependencies']) && is_array($data['dependencies'])) {
            $data['dependencies'] = array_map('intval', $data['dependencies']);
        }

        // Преобразуем числа в allowed_capacities в строки для TagsInput
        if (isset($data['allowed_capacities']) && is_array($data['allowed_capacities'])) {
            $data['allowed_capacities'] = array_map('strval', $data['allowed_capacities']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizeTaskData($data);
    }

    protected function normalizeTaskData(array $data): array
    {
        // Нормализуем dependencies - если есть, преобразуем в массив ID
        if (isset($data['dependencies']) && is_array($data['dependencies'])) {
            $data['dependencies'] = array_values(array_filter(array_map('intval', $data['dependencies'])));
        }

        // Нормализуем allowed_capacities - преобразуем строки в числа
        if (isset($data['allowed_capacities']) && is_array($data['allowed_capacities'])) {
            $data['allowed_capacities'] = array_values(array_filter(array_map(function ($capacity) {
                return is_numeric($capacity) ? (int) $capacity : null;
            }, $data['allowed_capacities'])));
            sort($data['allowed_capacities']);
        }

        // Нормализуем target_countries и target_industries - убираем пустые значения
        foreach (['target_countries', 'target_industries', 'local_presence_options'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = array_values(array_filter($data[$field]));
            }
        }

        return $data;
    }
}
