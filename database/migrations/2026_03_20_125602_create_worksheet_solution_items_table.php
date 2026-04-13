<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('worksheet_solution_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worksheet_solution_id');
            $table->unsignedBigInteger('task_id'); // A feladat azonosítója
            $table->integer('score'); // Erre a feladatra kapott pont
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worksheet_solution_items');
    }
};
