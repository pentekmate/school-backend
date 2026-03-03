<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;

class StoreShortAnswerService
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    public function store(Task $task, array $taskData)
    {
        $short_answer = $task->task_shortAnswer()->create([
            'feedback' => $taskData['feedback'],
        ]);

        foreach ($taskData['short_answer']['questions'] as $questionItem) {

            $imagePathForQuestion = null;
            $imagePathForAnswer = null;
            if (! empty($questionItem['question_image'])) {

                if ($questionItem['question_image'] instanceof UploadedFile) {

                    $imagePathForQuestion = $this->imageUploadService->store($questionItem['question_image']);
                } elseif (is_string($questionItem['question_image']) && str_starts_with($questionItem['question_image'], 'data:image')) {

                    $imagePathForQuestion = $this->imageUploadService->storeBase64($questionItem['question_image']);
                }
            }

            if (! empty($questionItem['answer_image'])) {

                if ($questionItem['answer_image'] instanceof UploadedFile) {

                    $imagePathForAnswer = $this->imageUploadService->store($questionItem['answer_image']);
                } elseif (is_string($questionItem['answer_image']) && str_starts_with($questionItem['answer_image'], 'data:image')) {

                    $imagePathForAnswer = $this->imageUploadService->storeBase64($questionItem['answer_image']);
                }
            }

            $sortAnwerQuestion = $short_answer->questions()->create([
                'question' => $questionItem['question'] ?? null,
                'imgURL' => $imagePathForQuestion,
            ]);

            $sortAnwerQuestion->answer()->create([
                'answer' => $questionItem['answer'] ?? null,
                'imgURL' => $imagePathForAnswer,
            ]);
        }
    }
}
