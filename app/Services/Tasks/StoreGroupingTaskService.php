<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;

class StoreGroupingTaskService
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    public function store(Task $task, array $taskData): void
    {
        $grouping = $task->task_grouping()->create();

        foreach ($taskData['grouping']['groups'] ?? [] as $groupData) {

            $group = $grouping->groups()->create([
                'name' => $groupData['name'],
            ]);

            foreach ($groupData['items'] ?? [] as $itemData) {

                $imagePath = null;

                if (! empty($itemData['image'])) {

                    if ($itemData['image'] instanceof UploadedFile) {

                        $imagePath = $this->imageUploadService->store($itemData['image']);
                    } elseif (is_string($itemData['image']) && str_starts_with($itemData['image'], 'data:image')) {

                        $imagePath = $this->imageUploadService->storeBase64($itemData['image']);
                    }
                }

                $group->items()->create([
                    'name' => $itemData['name'] ?? null,
                    'imgUrl' => $imagePath,
                ]);
            }
        }
    }
}
