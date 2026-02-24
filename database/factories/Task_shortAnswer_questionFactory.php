<?php

namespace Database\Factories;

use App\Models\Task_shortAnswer_answer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task_shortAnswer_question>
 */
class Task_shortAnswer_questionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question'=>fake()->word(1)
        ];
    }


    public function withAnswer()
    {
        return $this->has(
            Task_shortAnswer_answer::factory(),
            'answer'
        );
    }
}
