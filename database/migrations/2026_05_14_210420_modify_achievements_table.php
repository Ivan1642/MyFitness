<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropForeign(['exercise_id']);
            $table->dropColumn(['type', 'value', 'exercise_id']);
            $table->string('slug')->after('user_id');
            $table->string('name')->after('slug');
            $table->unique(['user_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'slug']);
            $table->dropColumn(['slug', 'name']);
            $table->enum('type', ['PR', 'VOLUME'])->nullable();
            $table->decimal('value', 10, 2)->nullable();
            $table->foreignId('exercise_id')->nullable()->constrained()->onDelete('set null');
        });
    }
};