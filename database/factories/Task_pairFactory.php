<?php

namespace Database\Factories;

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

    public function withPairs($pairs = 8)
    {
        return $this->has(
            Pair::factory()
            > count($pairs),
            'pairs'
        );
    }
}
