<?php

namespace Database\Seeders;

use App\Models\Task_type;
use Illuminate\Database\Seeder;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task_type::insert(
            [
                ['id' => 1, 'name' => 'grouping', 'subject_id' => 1],
                ['id' => 2, 'name' => 'pairing', 'subject_id' => 1],
                ['id' => 3, 'name' => 'short_answer', 'subject_id' => 1],
                ['id' => 4, 'name' => 'assignment', 'subject_id' => 1],
            ]);
    }
}
