<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;

class StoreShortAnswerService
{
    public function __construct(protected ImageUploadService $imageUploadService) {}

    public function store(Task $task, array $taskData)
    {
        $short_answer = $task->task_shortAnswer()->create();

        foreach ($taskData['short_answer']['questions'] as $questionItem) {

            $imgQ = $this->resolveImagePath($questionItem['question_image'] ?? null);
            $imgA = $this->resolveImagePath($questionItem['answer_image'] ?? null);

            $question = $short_answer->questions()->create([
                'question' => $questionItem['question'] ?? null,
                'imgURL' => $imgQ,
            ]);

            $question->answer()->create([
                'answer' => $questionItem['answer'] ?? null,
                'imgURL' => $imgA,
            ]);
        }
    }

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
