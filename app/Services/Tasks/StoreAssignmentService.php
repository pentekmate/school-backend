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
        $assignmentTask = $task->task_assignment()->create([
            'feedback' => $taskData['feedback'],
        ]);
        $imagePathForAssignment = $taskData['assignment']['image'];
        // $imagePathForAssignment = null;
        // if (! empty($taskData['assignment']['image'])) {

        //     if ($taskData['assignment']['image'] instanceof UploadedFile) {

        //         $imagePathForAssignment = $this->imageUploadService->store($taskData['assignment']['image']);
        //     } elseif (is_string($taskData['assignment']['image']) && str_starts_with($taskData['assignment']['image'], 'data:image')) {

        //         $imagePathForAssignment = $this->imageUploadService->storeBase64($taskData['assignment']['image']);
        //     }
        // }

        $assignmentImage = $assignmentTask->image()->create([
            'imageURL' => $imagePathForAssignment,
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
}
