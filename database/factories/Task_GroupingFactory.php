<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task_Grouping>
 */
class Task_GroupingFactory extends Factory
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

    public function withGroups($groupCount = 2, $itemsPerGroup = 3)
    {
        return $this->has(
            Group::factory()
                ->count($groupCount)
                ->withItems($itemsPerGroup),
            'groups'
        );
    }
}
