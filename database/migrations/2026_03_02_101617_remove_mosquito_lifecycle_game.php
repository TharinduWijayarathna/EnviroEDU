<?php

use App\Models\Badge;
use App\Models\PlatformGame;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        PlatformGame::query()->where('slug', 'mosquito-lifecycle')->delete();
        Badge::query()->where('slug', 'badge-mosquito-lifecycle')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore deleted platform game and badge.
    }
};
