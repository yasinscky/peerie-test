<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'duration_hours' => $this->duration_hours,
            'frequency' => $this->frequency,
            'dependencies' => $this->dependencies,
            'business_type' => $this->business_type,
            'language' => $this->language,
            'is_local' => $this->is_local,
            'requires_website' => $this->requires_website,
            'category' => $this->category,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pivot' => $this->when($this->pivot, function () {
                return [
                    'week' => $this->pivot->week,
                    'completed' => $this->pivot->completed,
                    'notes' => $this->pivot->notes,
                ];
            }),
        ];
    }
}
