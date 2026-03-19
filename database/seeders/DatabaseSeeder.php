<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(3)->create();
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => 'sajt',
        ]);
        $this->call([
            ClassRoomSeeder::class,
        ]);

        $this->call([
            SubjectSeeder::class,
        ]);
        $this->call([
            TaskTypeSeeder::class,
        ]);

        $this->call([
            StudentSeeder::class,
        ]);

        // Worksheet::factory()
        //     ->count(1)
        //     ->withTasks(6)
        //     ->create();
    }
}
