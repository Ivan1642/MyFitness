<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_likes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_usuario')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('likeable_id');
            $table->string('likeable_type');

            $table->dateTime('fecha')->useCurrent();

            $table->timestamps();

            $table->unique(['id_usuario', 'likeable_id', 'likeable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_likes');
    }
};