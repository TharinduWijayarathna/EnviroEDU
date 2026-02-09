<?php

namespace App\Services;

use App\Models\User;
use App\Models\VideoLesson;
use Illuminate\Support\Collection;

class EnviroEduProgressService
{
    public const GAME_SLUGS = [
        'quiz',
        'living_non_living',
        'who_am_i',
        'plant_builder',
        'plant_matching',
        'habitats_match',
        'mini_safari',
        'clean_city',
        'water_saver',
    ];

    public function progressPercentForUser(User $user): int
    {
        $totalLessons = VideoLesson::count();
        $totalActivities = $totalLessons + count(self::GAME_SLUGS);
        if ($totalActivities === 0) {
            return 0;
        }

        $completedLessons = $user->lessonCompletions()->pluck('video_lesson_id')->unique()->count();
        $gamesPlayed = $user->gameScores()->pluck('game_slug')->unique()->count();
        $completedActivities = $completedLessons + $gamesPlayed;

        return (int) round(($completedActivities / $totalActivities) * 100);
    }

    public function totalPointsForUser(User $user): int
    {
        return (int) $user->gameScores()->sum('score');
    }

    /**
     * @return Collection<int, array{user: User, points: int, rank: int}>
     */
    public function leaderboardByPoints(int $limit = 10): Collection
    {
        $students = User::query()
            ->where('role', 'student')
            ->withSum('gameScores as total_points', 'score')
            ->orderByDesc('total_points')
            ->limit($limit)
            ->get();

        return $students->map(function (User $user, int $index) {
            return [
                'user' => $user,
                'points' => (int) ($user->total_points ?? 0),
                'rank' => $index + 1,
            ];
        });
    }

    public function averageScoreForUser(User $user): ?float
    {
        $scores = $user->gameScores();
        $count = $scores->count();
        if ($count === 0) {
            return null;
        }

        return round($scores->avg('score'), 1);
    }

    public function weeklyRankForUser(User $user): ?int
    {
        $leaderboard = $this->leaderboardByPoints(100);
        $rank = $leaderboard->search(fn (array $row) => $row['user']->id === $user->id);

        return $rank !== false ? $rank + 1 : null;
    }
}
