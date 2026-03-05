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
        Schema::table('short_answer_user_answers', function (Blueprint $table) {
            $table->foreign('worksheet_solution_id')
                ->references('id')
                ->on('worksheet_solutions')
                ->cascadeOnDelete();
        });

        Schema::table('short_answer_user_answers', function (Blueprint $table) {
            $table->foreign('task_short_answer_question_id')
                ->references('id')
                ->on('task_short_answer_questions')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('short_answer_user_answers', function (Blueprint $table) {
            $table->dropForeign(['task_short_answer_question_id']);
            $table->dropForeign(['worksheet_solution_id']);
        });
    }
};
