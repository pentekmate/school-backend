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
        return match ($this->task_type->name) {
            'short_answer' => $this->shortAnswerData(),
            'grouping' => $this->groupingData(),
            'assignment' => $this->assignmentData(),
            'pairing' => $this->pairingData(),
            default => [],
        };

    }

    private function shortAnswerData()
    {
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

    private function groupingData()
    {
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

    private function assigmentData()
    {
        $image = $this->task_assignment?->image;

        return [
            'task_title' => $this->task_title,
            'task_description' => $this->task_description,
            'task_type' => 'assignment',

            'img' => $image?->imageURL, // vagy ami az oszlop neve

            'coordinates' => $image?->assignmentCoordinates
                ->map(function ($coordinate) {
                    return [
                        'id' => $coordinate->id,
                        'coordinate' => $coordinate->coordinate,
                    ];
                })->values(),

            'answers' => $image?->assignmentCoordinates
                ->flatMap(function ($coordinate) {
                    return $coordinate->assignmentAnswers->map(function ($answer) {
                        return [
                            'answer' => $answer->answer,

                        ];
                    });
                }),
        ];
    }

    private function pairingData()
    {
        return [
            'task_title' => $this->task_title,
            'task_description' => $this->task_description,
            'task_type' => 'pairing',
            'feedback' => $this->task_pair?->feedback,

            'pairQuestions' => $this->task_pair?->pairGroups
                ->flatMap->questions
                ->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question,
                        'img' => $question->imgURL,
                    ];
                }),
            'pairAnswers' => $this->task_pair?->pairGroups->flatMap->answers
                ->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'answer' => $answer->answer,
                        'img' => $answer->imgURL,
                    ];
                }),
        ];
    }
}
