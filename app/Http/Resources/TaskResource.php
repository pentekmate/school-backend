<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $cacheKey = "task_blank_structure_{$this->id}";

        return Cache::remember($cacheKey, now()->addDays(1), function () {
            return match ($this->task_type->name) {
                'short_answer' => $this->shortAnswerData(),
                'grouping' => $this->groupingData(),
                'assignment' => $this->assigmentData(),
                'pairing' => $this->pairingData(),
                default => [],
            };
        });
    }

    private function shortAnswerData()
    {
        return [
            'task_title' => $this->task_title,
            'task_description' => $this->task_description,
            'task_type_id' => $this->task_type->id,

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
            'task_type_id' => $this->task_type->id,

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
                            'imgURL' => $item->imgURL,

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
            'task_type_id' => $this->task_type->id,
            'task_id' => $this->id,

            'img' => $image?->imgURL, // vagy ami az oszlop neve

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
        // Ellenőrizzük, hogy van-e task_pair, különben hiba lesz a loop-nál
        $pairGroups = $this->task_pair?->pairGroups ?: collect();

        return [
            'task_title' => $this->task_title,
            'task_description' => $this->task_description,
            'task_type_id' => $this->task_type->id,

            'task_id' => $this->id,

            'pairQuestions' => $pairGroups->map(function ($group) {
                // A hasOne kapcsolatot sima tulajdonosként érjük el
                $q = $group->question;

                return $q ? [
                    'id' => $q->id,
                    'question' => $q->question,
                    'img' => $q->imgURL,
                ] : null;
            })->filter()->shuffle()->values(),

            'pairAnswers' => $pairGroups->map(function ($group) {
                $a = $group->answer;

                return $a ? [
                    'id' => $a->id,
                    'answer' => $a->answer,
                    'img' => $a->imgURL,
                ] : null;
            })->filter()->shuffle()->values(),
        ];
    }
}
