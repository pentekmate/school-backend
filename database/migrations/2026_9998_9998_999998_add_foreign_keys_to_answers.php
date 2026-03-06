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

            $table->foreign('task_short_answer_question_id')
                ->references('id')
                ->on('task_short_answer_questions')
                ->cascadeOnDelete();
        });

        Schema::table('pairing_user_answers', function (Blueprint $table) {
            $table->foreign('worksheet_solution_id')
                ->references('id')
                ->on('worksheet_solutions')
                ->cascadeOnDelete();

            $table->foreign('pair_question_id')
                ->references('id')
                ->on('pair_questions')
                ->cascadeOnDelete();

            $table->foreign('pair_answer_id')
                ->references('id')
                ->on('pair_answers')
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

        Schema::table('pairing_user_answers', function (Blueprint $table) {
            $table->dropForeign(['worksheet_solution_id']);
            $table->dropForeign(['pair_question_id']);
            $table->dropForeign(['pair_answer_id']);
        });
    }
};
