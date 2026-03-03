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
            if (! empty($groupData['pair_question_image'])) {

                if ($groupData['pair_question_image'] instanceof UploadedFile) {

                    $imagePathforQuestion = $this->imageUploadService->store($groupData['pair_question_image']);
                } elseif (is_string($groupData['pair_question_image']) && str_starts_with($groupData['pair_question_image'], 'data:image')) {

                    $imagePathforQuestion = $this->imageUploadService->storeBase64($groupData['pair_question_image']);
                }
            }

            $sortAnwerQuestion = $short_answer->questions()->create([
                'question' => $questionItem['question'],
            ]);

            $sortAnwerQuestion->answer()->create([
                'answer' => $questionItem['answer'],
            ]);
        }
    }
}
