<?php

namespace Database\Factories;

use App\Models\Task_assignment_image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class task_assignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'feedback' => fake()->word(), // ha van name mező
        ];
    }

    public function withImage($imageCount = 2)
    {
        return $this->has(
            Task_assignment_image::factory()
                ->count($imageCount)
                ->withCoordinates(),
            'image'
        );
    }
}
