<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['name' => 'Eco Hero', 'icon' => '♻️', 'description' => 'Recycling champion'],
            ['name' => 'Water Saver', 'icon' => '💧', 'description' => 'Saved water in challenges'],
            ['name' => 'Animal Friend', 'icon' => '🐾', 'description' => 'Learned about habitats'],
            ['name' => 'Green Thumb', 'icon' => '🌱', 'description' => 'Plant expert'],
            ['name' => 'Star Student', 'icon' => '⭐', 'description' => 'Top performer'],
            ['name' => 'Champion', 'icon' => '🏆', 'description' => 'Leaderboard champion'],
        ];

        foreach ($achievements as $a) {
            Achievement::firstOrCreate(
                ['name' => $a['name']],
                ['icon' => $a['icon'], 'description' => $a['description']]
            );
        }
    }
}
