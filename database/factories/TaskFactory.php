<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupItem;
use App\Models\Task_grouping;
use App\Models\Task_pair;
use App\Models\Pair;
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
            'task_type_id' => fake()->numberBetween(1, 2),
            'task_title' => fake()->sentence(1),
            'task_description' => fake()->sentence(3),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($task) {

            switch ($task->task_type_id) {
                case 1: // GROUPING
                    Task_grouping::factory()
                        ->for($task) // ← ez állítja be a task_id-t
                        ->has(
                            Group::factory()
                                ->has(GroupItem::factory()->count(3), 'items'),
                            'groups'
                        )
                        ->create();
                    break;

                    case 2: // PAIR
                        Task_pair::factory()->for($task)->has(
                            Pair::factory(),'pairs'
                        )->create();
                        break;

                    // case 3: // SHORT ANSWER
                    //     TaskShortAnswer::factory()->create([
                    //         'task_id' => $task->id
                    //     ]);
                    //     break;

                    // case 4: // ASSIGNMENT
                    TaskAssignment::factory()->create([
                        'task_id' => $task->id,
                    ]);
                    break;
            }

        });
    }
}
