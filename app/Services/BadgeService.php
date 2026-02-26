<?php

namespace App\Services;

use App\Enums\BadgeAwardFor;
use App\Models\Badge;
use App\Models\MiniGameAttempt;
use App\Models\PlatformGameAttempt;
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

        $topicId = $attempt->quiz->topic_id;
        if ($topicId) {
            $topicBadges = Badge::query()
                ->where('topic_id', $topicId)
                ->whereIn('award_for', [BadgeAwardFor::Quiz->value, BadgeAwardFor::Both->value])
                ->get();
            foreach ($topicBadges as $badge) {
                if ($this->award($user, $badge, 'quiz_attempt', $attempt->id)) {
                    $newBadges[] = $badge;
                }
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

        $topicId = $attempt->miniGame->topic_id;
        if ($topicId) {
            $topicBadges = Badge::query()
                ->where('topic_id', $topicId)
                ->whereIn('award_for', [BadgeAwardFor::Game->value, BadgeAwardFor::Both->value])
                ->get();
            foreach ($topicBadges as $topicBadge) {
                if ($this->award($user, $topicBadge, 'mini_game_attempt', $attempt->id)) {
                    $newBadges[] = $topicBadge;
                }
            }
        }

        return $newBadges;
    }

    public function awardForPlatformGameAttempt(User $user, PlatformGameAttempt $attempt): array
    {
        $newBadges = [];
        $slug = $attempt->platformGame->slug;

        $candidates = Badge::query()
            ->where('criteria_type', 'platform_game_complete')
            ->get();

        foreach ($candidates as $badge) {
            $config = $badge->criteria_config ?? [];
            $gameSlug = is_array($config) ? ($config['platform_game_slug'] ?? null) : null;
            if ($gameSlug === $slug && $this->award($user, $badge, 'platform_game_attempt', $attempt->id)) {
                $newBadges[] = $badge;
            }
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
