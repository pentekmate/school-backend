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
            'assignment' => $this->assigmentData(),
            'pairing' => $this->pairingData(),
            default => [],
        };

    }

    private function shortAnswerData()
    {
        return [
            'task_title' => $this->task_title,
            'task_description' => $this->task_description,
            'task_type' => $this->task_type->id,
            'feedback' => $this->task_shortAnswer?->feedback,
            'task_id' => $this->id,

            'questionsOrImages' => $this->task_shortAnswer?->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'img' => $question->imgURL,
                ];
            }),

            // 'answers'=>$this ->task_shortAnswer ->questions->map(function($question){
            //     return [
            //         'id'=>$question->answer->id,
            //         'answer'=>$question->answer->answer
            //     ];
            // })
        ];
    }

    private function groupingData()
    {
        return [
            'task_title' => $this->task_title,
            'task_description' => $this->task_description,
            'task_type' => $this->task_type->id,
            'feedback' => $this->task_grouping?->feedback,
            'task_id' => $this->id,

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
                            'imgURL' => $item->imgUrl,

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
            'task_type' => $this->task_type->id,
            'task_id' => $this->id,

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
            'task_type' => $this->task_type->id,
            'feedback' => $this->task_pair?->feedback,
            'task_id' => $this->id,

            'pairQuestions' => $this->task_pair?->pairGroups
                ->flatMap->questions
                ->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question,
                        'img' => $question->imgURL,
                    ];
                })->shuffle()->values(),
            'pairAnswers' => $this->task_pair?->pairGroups->flatMap->answers
                ->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'answer' => $answer->answer,
                        'img' => $answer->imgURL,
                    ];
                })->shuffle()->values(),
        ];
    }
}
