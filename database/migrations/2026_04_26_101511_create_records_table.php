<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->onDelete('cascade');

            $table->decimal('max_weight', 6, 2);

            $table->timestamps();

            $table->unique(['user_id', 'exercise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};