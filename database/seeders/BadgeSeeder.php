<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => 'First Quiz',
                'slug' => 'first-quiz',
                'description' => 'Complete your first quiz.',
                'icon' => '📝',
                'criteria_type' => 'first_quiz_complete',
                'criteria_config' => null,
                'order' => 1,
            ],
            [
                'name' => 'Perfect Score',
                'slug' => 'quiz-perfect-score',
                'description' => 'Get every question right on a quiz.',
                'icon' => '💯',
                'criteria_type' => 'quiz_perfect_score',
                'criteria_config' => null,
                'order' => 2,
            ],
            [
                'name' => 'First Game',
                'slug' => 'first-game',
                'description' => 'Complete your first mini game.',
                'icon' => '🎮',
                'criteria_type' => 'first_game_complete',
                'criteria_config' => null,
                'order' => 3,
            ],
        ];

        foreach ($badges as $b) {
            Badge::query()->firstOrCreate(
                ['slug' => $b['slug']],
                $b
            );
        }
    }
}
