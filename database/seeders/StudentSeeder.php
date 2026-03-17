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
        Student::insert([
            ['id' => 1, 'name' => 'Kovács János', 'classroom_id' => 1],
            ['id' => 2, 'name' => 'Nagy Erzsébet', 'classroom_id' => 1],
            ['id' => 3, 'name' => 'Tóth Gábor', 'classroom_id' => 1],
            ['id' => 4, 'name' => 'Szabó Petra', 'classroom_id' => 1],
            ['id' => 5, 'name' => 'Horváth Bence', 'classroom_id' => 1],
            ['id' => 6, 'name' => 'Varga Lilla', 'classroom_id' => 1],
            ['id' => 7, 'name' => 'Kiss Attila', 'classroom_id' => 1],
            ['id' => 8, 'name' => 'Molnár Zsófia', 'classroom_id' => 1],
            ['id' => 9, 'name' => 'Németh Balázs', 'classroom_id' => 1],
            ['id' => 10, 'name' => 'Farkas Dóra', 'classroom_id' => 1],
            ['id' => 11, 'name' => 'Balogh Tamás', 'classroom_id' => 1],
            ['id' => 12, 'name' => 'Papp Noémi', 'classroom_id' => 1],
            ['id' => 13, 'name' => 'Takács Kristóf', 'classroom_id' => 1],
            ['id' => 14, 'name' => 'Juhász Eszter', 'classroom_id' => 1],
            ['id' => 15, 'name' => 'Mészáros Ádám', 'classroom_id' => 1],
            ['id' => 16, 'name' => 'Lakatos Vivien', 'classroom_id' => 1],
            ['id' => 17, 'name' => 'Simon Péter', 'classroom_id' => 1],
            ['id' => 18, 'name' => 'Oláh Krisztina', 'classroom_id' => 1],
            ['id' => 19, 'name' => 'Fekete Dániel', 'classroom_id' => 1],
            ['id' => 20, 'name' => 'Rácz Luca', 'classroom_id' => 1],
            ['id' => 21, 'name' => 'Szilágyi Márk', 'classroom_id' => 2], // Átraktam pár embert a 2-es osztályba is
            ['id' => 22, 'name' => 'Török Bianka', 'classroom_id' => 2],
            ['id' => 23, 'name' => 'Fehér Roland', 'classroom_id' => 2],
            ['id' => 24, 'name' => 'Bodnár Kinga', 'classroom_id' => 2],
            ['id' => 25, 'name' => 'Szalai Gergő', 'classroom_id' => 2],
            ['id' => 26, 'name' => 'Vincze Réka', 'classroom_id' => 2],
            ['id' => 27, 'name' => 'Hegedűs Marcell', 'classroom_id' => 2],
            ['id' => 28, 'name' => 'Kerekes Enikő', 'classroom_id' => 2],
            ['id' => 29, 'name' => 'Major Barnabás', 'classroom_id' => 2],
            ['id' => 30, 'name' => 'Balla Fanni', 'classroom_id' => 2],
        ]);
    }
}
