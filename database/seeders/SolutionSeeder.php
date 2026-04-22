<?php

namespace Database\Seeders;

use App\Models\Worksheet_solution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentIds = DB::table('students')
            ->whereBetween('id', [1, 20])
            ->pluck('id');

        foreach ($studentIds as $id) {
            // 1. Létrehozzuk a fő megoldást
            $solution = Worksheet_solution::create([
                'student_id' => $id,
                'worksheet_id' => 1,
                'score' => 2, // 1+1 pont
            ]);

            // 2. Létrehozzuk hozzá a részpontokat
            $solution->items()->createMany([
                ['task_id' => 1, 'score' => 1],
                ['task_id' => 2, 'score' => 1],
                ['task_id' => 3, 'score' => 1],
                ['task_id' => 4, 'score' => 1],
                ['task_id' => 5, 'score' => 1],
                ['task_id' => 6, 'score' => 1],
                ['task_id' => 7, 'score' => 1],
                ['task_id' => 8, 'score' => 1],
                ['task_id' => 9, 'score' => 1],
                ['task_id' => 10, 'score' => 1],
            ]);
        }
    }
}
