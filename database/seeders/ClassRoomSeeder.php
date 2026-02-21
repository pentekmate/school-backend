<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Seeder;

class ClassRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classroom::insert(
            [
                ['id' => 1, 'user_id' => 1, 'name' => 'matek'],

            ]);
    }
}
