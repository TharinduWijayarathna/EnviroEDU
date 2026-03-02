<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class LeaderboardService
{
    /**
     * Get top 10 students by combined quiz score + game completions, filtered by class.
     *
     * @return Collection<int, array{rank: int, user: User, quiz_score: int, game_score: int, total_score: int}>
     */
    public function getClassLeaderboard(User $student, int $limit = 10): Collection
    {
        $classIds = $student->enrolledClasses()->pluck('class_rooms.id');

        if ($classIds->isEmpty()) {
            return collect();
        }

        $studentIds = User::query()
            ->where('role', Role::Student)
            ->whereHas('enrolledClasses', fn ($q) => $q->whereIn('class_rooms.id', $classIds))
            ->pluck('id');

        if ($studentIds->isEmpty()) {
            return collect();
        }

        $quizScores = \Illuminate\Support\Facades\DB::table('quiz_attempts')
            ->whereIn('user_id', $studentIds)
            ->selectRaw('user_id, COALESCE(SUM(score), 0) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $platformCompletions = \Illuminate\Support\Facades\DB::table('platform_game_attempts')
            ->whereIn('user_id', $studentIds)
            ->where('completed', true)
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $miniCompletions = \Illuminate\Support\Facades\DB::table('mini_game_attempts')
            ->whereIn('user_id', $studentIds)
            ->where('completed', true)
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $totals = [];
        foreach ($studentIds as $uid) {
            $quiz = (int) ($quizScores[$uid] ?? 0);
            $platform = (int) ($platformCompletions[$uid] ?? 0);
            $mini = (int) ($miniCompletions[$uid] ?? 0);
            $totals[$uid] = [
                'quiz_score' => $quiz,
                'game_score' => $platform + $mini,
                'total_score' => $quiz + $platform + $mini,
            ];
        }

        uasort($totals, fn ($a, $b) => $b['total_score'] <=> $a['total_score']);

        $users = User::query()
            ->whereIn('id', array_keys($totals))
            ->get()
            ->keyBy('id');

        $rank = 1;
        $result = collect();
        foreach (array_slice($totals, 0, $limit, true) as $uid => $scores) {
            $user = $users->get($uid);
            if ($user) {
                $result->push([
                    'rank' => $rank,
                    'user' => $user,
                    'quiz_score' => $scores['quiz_score'],
                    'game_score' => $scores['game_score'],
                    'total_score' => $scores['total_score'],
                ]);
                $rank++;
            }
        }

        return $result;
    }
}
