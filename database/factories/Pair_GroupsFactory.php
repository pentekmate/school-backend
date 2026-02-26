<?php

namespace Database\Factories;

use App\Models\Pair_answer;
use App\Models\Pair_question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pair_groups>
 */
class Pair_GroupsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];

    }

    public function withQuestions($itemsPerGroup = 2)
    {
        return $this->has(
            Pair_question::factory()->count($itemsPerGroup), 'questions'
        );
    }

    public function withAnswers($itemsPerGroup = 2)
    {
        return $this->has(
            Pair_answer::factory()->count($itemsPerGroup), 'answers'
        );
    }
}
