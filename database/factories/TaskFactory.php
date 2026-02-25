<?php

namespace Database\Factories;

use App\Models\Pair;
use App\Models\Task_assignment;
use App\Models\Task_grouping;
use App\Models\Task_pair;
use App\Models\Task_shortAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'task_type_id' => fake()->numberBetween(2, 2),
            'task_title' => fake()->sentence(1),
            'task_description' => fake()->sentence(3),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($task) {

            switch ($task->task_type_id) {
                case 1: // GROUPING
                    Task_grouping::factory()->for($task)->withGroups(3)->create();

                    break;

                case 2: // PAIR
                    Task_pair::factory()->for($task)->
                    withPairGroups()
                        ->create();
                    break;

                case 3: // SHORT ANSWER
                    Task_shortAnswer::factory()
                        ->for($task)
                        ->withQuestions(3)
                        ->create();
                    break;

                case 4: // ASSIGNMENT
                    Task_assignment::factory()
                        ->for($task)
                        ->withImage(1)
                        ->create();
                    break;
            }

        });
    }
}
