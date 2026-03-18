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
                ['id' => 1, 'user_id' => 1, 'name' => '4.c'],
                ['id' => 2, 'user_id' => 1, 'name' => '5.c'],
                ['id' => 3, 'user_id' => 2, 'name' => '6.c'],
                ['id' => 4, 'user_id' => 2, 'name' => '1.c'],
                ['id' => 5, 'user_id' => 3, 'name' => '2.c'],

            ]);
    }
}
