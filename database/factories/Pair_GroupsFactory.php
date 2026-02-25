<?php

namespace Database\Factories;

use App\Models\Pair;
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

    public function withGroupItems($itemsPerGroup=2){
        return $this->has(
            Pair::factory()->count($itemsPerGroup),'pairs'
        );
    }
}
