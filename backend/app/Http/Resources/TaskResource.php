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
            'short_description' => $this->short_description,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'frequency' => $this->frequency,
            'dependencies' => $this->dependencies,
            'language' => $this->language,
            'is_local' => $this->is_local,
            'requires_website' => $this->requires_website,
            'category' => $this->category,
            'document_key' => $this->document_key,
            'document_group' => $this->document_group,
            'document_layout' => $this->document_layout,
            'document_short_label' => $this->document_short_label,
            'document_description' => $this->document_description,
            'document_fields' => $this->document_fields,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pivot' => $this->when($this->pivot, function () {
                return [
                    'id' => $this->pivot->id,
                    'week' => $this->pivot->week,
                    'completed' => $this->pivot->completed,
                    'notes' => $this->pivot->notes,
                ];
            }),
        ];
    }
}
