<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Set grade_level to null where it is not 4 or 5 (app only supports grades 4 and 5).
     */
    public function up(): void
    {
        $allowed = [4, 5];
        foreach (['topics', 'quizzes', 'mini_games'] as $table) {
            DB::table($table)
                ->whereNotNull('grade_level')
                ->whereNotIn('grade_level', $allowed)
                ->update(['grade_level' => null]);
        }
    }

    /**
     * Reverse the migrations.
     * Cannot restore previous grade_level values.
     */
    public function down(): void
    {
        // No-op: previous values are not stored
    }
};
