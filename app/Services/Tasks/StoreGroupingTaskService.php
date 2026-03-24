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

                // Itt hívjuk meg az új logikát
                $imagePath = $this->resolveImagePath($itemData['image'] ?? null);

                $group->items()->create([
                    'name' => $itemData['name'] ?? null,
                    'imgURL' => $imagePath,
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
