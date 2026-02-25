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
        Schema::create('pair_answers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('pair_group_id');
            $table->string('answer');
            $table->string('imgURL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pair_answers');
    }
};
