<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;

class StoreAssignmentService
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    public function store(Task $task, array $taskData)
    {
        $assignmentTask = $task->task_assignment()->create();

        // Az új, egységes képfeloldó logika hívása
        $imagePathForAssignment = $this->resolveImagePath($taskData['assignment']['image'] ?? null);

        $assignmentImage = $assignmentTask->image()->create([
            'imgURL' => $imagePathForAssignment,
        ]);

        foreach ($taskData['assignment']['coordinatesAndAnswers'] as $item) {

            $assigmentCoordinate = $assignmentImage->assignmentCoordinates()->create([
                'coordinate' => $item['coordinate'],
            ]);

            foreach ($item['answers'] as $coordinateAnswerItem) {

                $assigmentCoordinate->assignmentAnswers()->create([
                    'answer' => $coordinateAnswerItem['answer'],
                    'isCorrect' => $coordinateAnswerItem['isCorrect'],
                ]);
            }
        }
    }

    /**
     * Egységes képkezelő logika (Async, Base64, UploadedFile, Meglévő)
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
