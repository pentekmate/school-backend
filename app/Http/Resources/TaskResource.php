<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->task_type->name === 'short_answer') {
            return [
                'task_title' => $this->task_title,
                'task_description' => $this->task_description,
                'task_type' => 'shortAnswer',
                'feedback' => $this->task_shortAnswer?->feedback,

                'questionsOrImages' => $this->task_shortAnswer?->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question,
                        'img' => $question->imgURL,
                    ];
                }),

                'answers' => $this->task_shortAnswer?->questions
                    ->map(fn ($q) => $q->answer?->answer)
                    ->filter()
                    ->values(),
            ];
        }
        if ($this->task_type->name === 'grouping') {

            return [
                'task_title' => $this->task_title,
                'task_description' => $this->task_description,
                'task_type' => 'grouping',
                'feedback' => $this->task_grouping?->feedback,

                'groups' => $this->task_grouping?->groups
                    ->map(function ($group) {
                        return [
                            'id' => $group->id,
                            'name' => $group->name,
                        ];
                    })->values(),

                'group_items' => $this->task_grouping?->groups
                    ->flatMap(function ($group) {
                        return $group->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,

                            ];
                        });
                    })->values(),
            ];
        }
        if ($this->task_type->name === 'assignment') {

            $image = $this->task_assignment?->image;

            return [
                'task_title' => $this->task_title,
                'task_description' => $this->task_description,
                'task_type' => 'assignment',

                'img' => $image?->imgURL, // vagy ami az oszlop neve

                'coordinates' => $image?->assignmentCoordinates
                    ->map(function ($coordinate) {
                        return [
                            'id' => $coordinate->id,
                            'coordinate' => $coordinate->coordinate,
                        ];
                    })->values(),

                'answers' => $image?->assignmentCoordinates
                    ->map(fn ($coordinate) => $coordinate->assignmentAnswer?->answer)
                    ->filter()
                    ->values(),
            ];
        }

        return [];
    }
}
