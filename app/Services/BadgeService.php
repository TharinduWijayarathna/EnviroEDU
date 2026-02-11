<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\MiniGameAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    public function awardForQuizAttempt(User $user, QuizAttempt $attempt): array
    {
        $newBadges = [];
        $total = $attempt->total_questions;
        $score = $attempt->score;
        $isPerfect = $total > 0 && $score === $total;
        $firstQuiz = $user->quizAttempts()->where('id', $attempt->id)->count() === 1
            && $user->quizAttempts()->count() === 1;

        $candidates = Badge::query()
            ->whereIn('criteria_type', ['first_quiz_complete', 'quiz_perfect_score'])
            ->get();

        foreach ($candidates as $badge) {
            if ($badge->criteria_type === 'first_quiz_complete' && ! $firstQuiz) {
                continue;
            }
            if ($badge->criteria_type === 'quiz_perfect_score' && ! $isPerfect) {
                continue;
            }
            if ($this->award($user, $badge, 'quiz_attempt', $attempt->id)) {
                $newBadges[] = $badge;
            }
        }

        return $newBadges;
    }

    public function awardForMiniGameAttempt(User $user, MiniGameAttempt $attempt): array
    {
        $newBadges = [];
        $firstGame = $user->miniGameAttempts()->where('id', $attempt->id)->count() === 1
            && $user->miniGameAttempts()->count() === 1;

        $badge = Badge::query()->where('criteria_type', 'first_game_complete')->first();
        if ($badge && $firstGame && $this->award($user, $badge, 'mini_game_attempt', $attempt->id)) {
            $newBadges[] = $badge;
        }

        return $newBadges;
    }

    private function award(User $user, Badge $badge, string $sourceType, int $sourceId): bool
    {
        $exists = DB::table('badge_user')
            ->where('badge_id', $badge->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return false;
        }

        $user->badges()->attach($badge->id, [
            'earned_at' => now(),
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);

        return true;
    }
}
