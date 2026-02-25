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
        Schema::table('badges', function (Blueprint $table) {
            $table->foreignId('topic_id')->nullable()->after('order')->constrained()->nullOnDelete();
            $table->string('award_for', 20)->nullable()->after('topic_id')->comment('quiz, game, or both');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn(['topic_id', 'award_for']);
        });
    }
};
