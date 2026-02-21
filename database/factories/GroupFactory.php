<?php

namespace Database\Factories;

use App\Models\GroupItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(1),
        ];
    }

    public function withItems($count = 3)
    {
        return $this->has(
            GroupItem::factory()->count($count),
            'items'
        );
    }
}
