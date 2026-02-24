<?php

namespace Database\Factories;

use App\Models\Task_shortAnswer_question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task_shortAnswer>
 */
class Task_shortAnswerFactory extends Factory
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

    public function withQuestions($count = 1)
    {
        return $this->has(
            Task_shortAnswer_question::factory()
                ->count($count)
                ->withAnswer(),
            'questions'
        );
    }
}
