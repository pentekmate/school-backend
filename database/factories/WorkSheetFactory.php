<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkSheet>
 */
class WorkSheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // 'user_id' => User::factory(),
            'user_id' => 1,
            'subject_id' => 1,
            'lifetime_minutes' => 60,
            'max_time_to_resolve_minutes' => 45,
            'classroom_id' => 1,
            'is_public' => true,
            'grade' => fake()->randomElement(['1', '2', '3', '4']),
        ];
    }

    public function withTasks($count = 5)
    {
        return $this->has(
            Task::factory()->count($count),
            'tasks'
        );
    }
}
