<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('worksheets', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('subject_id')
                ->references('id')->on('subjects')
                ->cascadeOnDelete();

            $table->foreign('classroom_id')
                ->references('id')->on('classrooms')
                ->cascadeOnDelete();
        });

        Schema::table('task_types', function (Blueprint $table) {
            $table->foreign('subject_id')
                ->references('id')->on('subjects')
                ->cascadeOnDelete();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('worksheet_id')
                ->references('id')->on('worksheets')
                ->cascadeOnDelete();

            $table->foreign('task_type_id')
                ->references('id')->on('task_types')
                ->cascadeOnDelete();
        });

        Schema::table('classroom_worksheet', function (Blueprint $table) {
            $table->foreign('classroom_id')->references('id')->on('classrooms')->cascadeOnDelete();
            $table->foreign('worksheet_id')->references('id')->on('worksheets')->cascadeOnDelete();

        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreign('classroom_id')->references('id')->on('classrooms')->cascadeOnDelete();
        });

        Schema::table('task_groupings', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->foreign('task_grouping_id')->references('id')->on('task_groupings')->cascadeOnDelete();
        });

        Schema::table('group_items', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
        });

        Schema::table('task_pairs', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
        });

        Schema::table('pairs', function (Blueprint $table) {
            $table->foreign('task_pair_id')->references('id')->on('task_pairs')->cascadeOnDelete();
        });

        Schema::table('task_short_answers', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
        });

        Schema::table('task_short_answer_questions', function (Blueprint $table) {
            $table->foreign('task_short_answers_id')->references('id')->on('task_short_answers')->cascadeOnDelete();
        });

        Schema::table('task_short_answer_answers', function (Blueprint $table) {
            $table->foreign('task_short_answer_question_id')->references('id')->on('task_short_answer_questions')->cascadeOnDelete();
        });

        Schema::table('task_assignments', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
        });

        Schema::table('task_assignment_images', function (Blueprint $table) {
            $table->foreign('task_assignment_id')->references('id')->on('task_assignments')->cascadeOnDelete();
        });

        Schema::table('task_assignment_coordinates', function (Blueprint $table) {
            $table->foreign('task_assignment_image_id')->references('id')->on('task_assignment_images')->cascadeOnDelete();
        });

        Schema::table('task_assignment_answers', function (Blueprint $table) {
            $table->foreign('task_assignment_coordinate_id')->references('id')->on('task_assignment_coordinates')->cascadeOnDelete();
        });

    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['worksheet_id']);
            $table->dropForeign(['task_type_id']);
        });

        Schema::table('task_types', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
        });

        Schema::table('worksheets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['classroom_id']);
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('classroom_worksheet', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropForeign(['worksheet_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
        });

        Schema::table('task_grouping', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['task_grouping_id']);
        });

        Schema::table('group_items', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });

        Schema::table('task_pairs', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });

        Schema::table('pairs', function (Blueprint $table) {
            $table->dropForeign(['task_pair_id']);
        });

        Schema::table('task_short_answers', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });

        Schema::table('task_short_answer_questions', function (Blueprint $table) {
            $table->dropForeign(['task_short_answer_id']);
        });

        Schema::table('task_short_answer_answers', function (Blueprint $table) {
            $table->dropForeign(['task_short_answer_question_id']);
        });

        Schema::table('task_assignments', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });

        Schema::table('task_assignment_images', function (Blueprint $table) {
            $table->dropForeign(['task_assignment_id']);
        });

        Schema::table('task_assignment_coordinates', function (Blueprint $table) {
            $table->dropForeign(['task_assignment_image_id']);
        });

        Schema::table('task_assignment_answers', function (Blueprint $table) {
            $table->dropForeign(['task_assignment_coordinate_id']);
        });

    }
};
