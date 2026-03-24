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

            // Képek feloldása az új logikával
            $imagePathforQuestion = $this->resolveImagePath($groupData['pair_question_image'] ?? null);
            $imagePathforAnswer = $this->resolveImagePath($groupData['pair_answer_image'] ?? null);

            // Kérdés mentése
            $pairGroup->question()->create([
                'question' => $groupData['pair_question'] ?? null,
                'imgURL' => $imagePathforQuestion,
            ]);

            // Válasz mentése
            $pairGroup->answer()->create([
                'answer' => $groupData['pair_answer'] ?? null,
                'imgURL' => $imagePathforAnswer,
            ]);
        }
    }

    /**
     * Egységes képkezelő logika
     */
    private function resolveImagePath($input): ?string
    {
        if (empty($input)) {
            return null;
        }
        if ($input instanceof UploadedFile) {
            return $this->imageUploadService->store($input);
        }

        if (is_string($input)) {
            if (str_starts_with($input, 'data:image')) {
                return $this->imageUploadService->storeBase64($input);
            }
            if (str_starts_with($input, 'temp/')) {
                return $this->imageUploadService->finalizeTempImage($input);
            }

            return $input; // Meglévő URL
        }

        return null;
    }
}
