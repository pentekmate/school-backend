<?php

namespace Database\Factories;

use App\Models\Task_assignment_coordinate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class task_assignment_imageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'imageURL' => 'https://picsum.photos/640/480?random='.rand(1, 1000),
        ];
    }

    public function withCoordinates($maxCoordinates = 5)
    {
        return $this->has(
            Task_assignment_coordinate::factory()
                ->count($maxCoordinates)
                ->withAnswer(),
            'assignmentCoordinates'
        );
    }
}
