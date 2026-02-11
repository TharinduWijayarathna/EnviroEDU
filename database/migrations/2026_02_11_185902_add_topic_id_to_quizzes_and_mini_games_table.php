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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
        Schema::table('mini_games', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
        });
        Schema::table('mini_games', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
        });
    }
};
