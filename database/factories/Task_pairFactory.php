<?php

namespace Database\Factories;

use App\Models\Pair;
use App\Models\Pair_groups;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task_pair>
 */
class Task_pairFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'feedback' => fake()->word(), // ha van name mező
        ];
    }

    public function withPairGroups($groups = 4)
    {
        return $this->has(
            Pair_groups::factory()->count($groups)->withQuestions()->withAnswers(),
            'pairGroups'
        );
    }
}
