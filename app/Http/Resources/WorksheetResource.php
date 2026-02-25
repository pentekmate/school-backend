<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorksheetResource extends JsonResource
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
            'user_id' => $this->user_id,
            'subject_id' => $this->subject_id,
            'classroom_id' => $this->classroom_id,
            'lifetime_minutes' => $this->lifetime_minutes,
            'max_time_to_resolve_minutes' => $this->max_time_to_resolve_minutes,
            'is_public' => $this->is_public,
            'grade' => $this->grade,
            'tasks' => TaskResource::collection($this->tasks),
        ];
    }
}
