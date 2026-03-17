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
        Schema::create('class_worksheets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('classroom_id');

            $table->unsignedBigInteger('worksheet_id');

            $table->string('access_code', 8)->unique();
            $table->string('password', 8)->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
