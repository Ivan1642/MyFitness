<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_exercise', function (Blueprint $table) {
            $table->id();

            $table->foreignId('routine_id')
                ->constrained('routines')
                ->onDelete('cascade');

            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->onDelete('cascade');

            $table->unsignedInteger('order');

            $table->timestamps();

            $table->unique(['routine_id', 'exercise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_exercise');
    }
};