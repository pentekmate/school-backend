<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProcessImage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $path) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        $fullPath = storage_path("app/public/{$this->path}");
        $manager = new ImageManager(new Driver);

        $image = $manager->read($fullPath);

        $image->resize(width: 1200)
            ->toJpeg(80)
            ->save($fullPath);
    }
}
