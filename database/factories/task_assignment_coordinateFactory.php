<?php

namespace Database\Factories;

use App\Models\Task_assignment_answer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class task_assignment_coordinateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coordinate' => fake()->numberBetween(0, 1920).','.fake()->numberBetween(0, 1080),
        ];
    }

    public function withAnswer()
    {
        return $this->has(
            Task_assignment_answer::factory(),
            'assignmentAnswer'
        );
    }
}
