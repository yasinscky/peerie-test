<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'country' => $this->country,
            'business_niche' => $this->business_niche,
            'language' => $this->language,
            'is_local_business' => $this->is_local_business,
            'has_website' => $this->has_website,
            'marketing_time_per_week' => $this->marketing_time_per_week,
            'questionnaire_data' => $this->questionnaire_data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'weeks' => $this->when($this->relationLoaded('tasks'), function () {
                return $this->getPlanByWeeks();
            }),
            'total_tasks' => $this->when($this->relationLoaded('tasks'), function () {
                return $this->tasks->count();
            }),
            'completed_tasks' => $this->when($this->relationLoaded('tasks'), function () {
                return $this->tasks()->wherePivot('completed', true)->count();
            }),
        ];
    }
}
