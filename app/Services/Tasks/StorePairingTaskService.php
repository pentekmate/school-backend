<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;

class StorePairingTaskService
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    public function store(Task $task, array $taskData)
    {
        $pairing = $task->task_pair()->create();

        foreach ($taskData['pairing']['pairing_groups'] as $groupData) {

            $pairGroup = $pairing->pairGroups()->create();
            $imagePathforQuestion = null;
            $imagePathforAnswer = null;
            if (! empty($groupData['pair_question_image'])) {

                if ($groupData['pair_question_image'] instanceof UploadedFile) {

                    $imagePathforQuestion = $this->imageUploadService->store($groupData['pair_question_image']);
                } elseif (is_string($groupData['pair_question_image']) && str_starts_with($groupData['pair_question_image'], 'data:image')) {

                    $imagePathforQuestion = $this->imageUploadService->storeBase64($groupData['pair_question_image']);
                }
            }

            if (! empty($groupData['pair_answer_image'])) {

                if ($groupData['pair_answer_image'] instanceof UploadedFile) {

                    $imagePathforAnswer = $this->imageUploadService->store($groupData['pair_answer_image']);
                } elseif (is_string($groupData['pair_answer_image']) && str_starts_with($groupData['pair_answer_image'], 'data:image')) {

                    $imagePathforAnswer = $this->imageUploadService->storeBase64($groupData['pair_answer_image']);
                }
            }

            $pairGroup->question()->create([
                'question' => $groupData['pair_question'] ?? null,
                'imgURL' => $imagePathforQuestion,
            ]);

            $pairGroup->answer()->create([
                'answer' => $groupData['pair_answer'] ?? null,
                'imgURL' => $imagePathforAnswer,
            ]);
        }
    }
}
