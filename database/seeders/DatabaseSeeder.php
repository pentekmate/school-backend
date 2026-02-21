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
        User::factory()->create();

        $this->call([
            ClassRoomSeeder::class,
        ]);

        $this->call([
            SubjectSeeder::class,
        ]);
        $this->call([
            TaskTypeSeeder::class,
        ]);

        Worksheet::factory()
            ->count(1)
            ->withTasks(6)
            ->create();
    }
}
