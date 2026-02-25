<?php

namespace App\Http\Controllers;

use App\Models\MiniGame;
use App\Models\MiniGameAttempt;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function __construct(
        private BadgeService $badgeService
    ) {}

    public function recordQuiz(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quiz_id' => ['required', 'integer', 'exists:quizzes,id'],
            'score' => ['required', 'integer', 'min:0'],
            'total_questions' => ['required', 'integer', 'min:1'],
            'answers' => ['nullable', 'array'],
            'answers.*.question_id' => ['required', 'integer'],
            'answers.*.option_index' => ['required', 'integer'],
            'answers.*.correct' => ['required', 'boolean'],
        ]);

        $quiz = Quiz::query()->findOrFail($validated['quiz_id']);
        $canView = $quiz->is_published || (Auth::check() && $quiz->user_id === Auth::id());
        if (! $canView) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $attempt = QuizAttempt::query()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => (int) $validated['score'],
            'total_questions' => (int) $validated['total_questions'],
            'answers' => $validated['answers'] ?? [],
            'completed_at' => now(),
        ]);

        $newBadges = $this->badgeService->awardForQuizAttempt($user, $attempt);

        return response()->json([
            'attempt_id' => $attempt->id,
            'new_badges' => collect($newBadges)->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'icon' => $b->icon,
                'image_url' => $b->image_path ? asset('storage/'.$b->image_path) : null,
            ])->values()->all(),
        ]);
    }

    public function recordMiniGame(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mini_game_id' => ['required', 'integer', 'exists:mini_games,id'],
            'completed' => ['boolean'],
            'details' => ['nullable', 'array'],
        ]);

        $miniGame = MiniGame::query()->findOrFail($validated['mini_game_id']);
        $canView = $miniGame->is_published || (Auth::check() && $miniGame->user_id === Auth::id());
        if (! $canView) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $attempt = MiniGameAttempt::query()->create([
            'user_id' => $user->id,
            'mini_game_id' => $miniGame->id,
            'completed' => $validated['completed'] ?? true,
            'details' => $validated['details'] ?? [],
            'completed_at' => now(),
        ]);

        $newBadges = $this->badgeService->awardForMiniGameAttempt($user, $attempt);

        return response()->json([
            'attempt_id' => $attempt->id,
            'new_badges' => collect($newBadges)->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'icon' => $b->icon,
                'image_url' => $b->image_path ? asset('storage/'.$b->image_path) : null,
            ])->values()->all(),
        ]);
    }
}
