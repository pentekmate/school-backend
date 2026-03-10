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
        Schema::create('assignment_user_answers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('worksheet_solution_id');
            $table->unsignedBigInteger('task_assignment_image_id');
            $table->unsignedBigInteger('task_assignment_coordinate_id');
            $table->unsignedBigInteger('task_assignment_answer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_user_answers');
    }
};
