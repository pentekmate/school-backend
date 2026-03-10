<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Student::insert(
            [
                ['id' => 1, 'name' => 'Joco', 'classroom_id' => '1'],

            ]);
    }
}
