<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeTaskData($data);
    }

    protected function normalizeTaskData(array $data): array
    {
        if (isset($data['dependencies']) && is_array($data['dependencies'])) {
            $data['dependencies'] = array_values(array_filter(array_map('intval', $data['dependencies'])));
        }

        if (isset($data['allowed_capacities']) && is_array($data['allowed_capacities'])) {
            $data['allowed_capacities'] = array_values(array_filter(array_map(function ($capacity) {
                return is_numeric($capacity) ? (int) $capacity : null;
            }, $data['allowed_capacities'])));
            sort($data['allowed_capacities']);
        }

        foreach (['target_countries', 'target_industries'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = array_values(array_filter($data[$field]));
            }
        }

        if (isset($data['local_presence_options']) && is_array($data['local_presence_options'])) {
            $data['local_presence_options'] = !empty($data['local_presence_options']) ? $data['local_presence_options'][0] : 'no';
        }

        if (isset($data['template']) && is_array($data['template'])) {
            $data['template'] = !empty($data['template']) ? $data['template'][0] : 'no';
        }

        if (isset($data['prerequisites']) && is_array($data['prerequisites'])) {
            $data['prerequisites'] = array_values(array_filter($data['prerequisites'], function ($prerequisite) {
                return isset($prerequisite['condition']) && isset($prerequisite['value']);
            }));
        } else {
            $data['prerequisites'] = [];
        }

        return $data;
    }
}
