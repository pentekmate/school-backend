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
    }
};
