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
    }
};
