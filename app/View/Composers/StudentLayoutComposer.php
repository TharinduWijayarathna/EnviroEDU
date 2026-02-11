<?php

namespace App\View\Composers;

use App\Models\MiniGame;
use App\Models\Quiz;
use App\Models\Topic;
use Illuminate\View\View;

class StudentLayoutComposer
{
    public function compose(View $view): void
    {
        $topics = collect();
        $standaloneQuizzes = collect();
        $standaloneMiniGames = collect();
        $grade = request()->integer('grade', 0);
        $topicsPayload = [];

        if (auth()->check() && auth()->user()->role?->value === 'student') {
            $gradeFilter = fn ($q) => $q->where(fn ($q2) => $q2->whereNull('grade_level')->orWhere('grade_level', $grade));

            $topics = Topic::query()
                ->where('is_published', true)
                ->when($grade > 0, $gradeFilter)
                ->orderBy('order')
                ->orderBy('title')
                ->with([
                    'quizzes' => fn ($q) => $q->where('is_published', true),
                    'miniGames' => fn ($q) => $q->where('is_published', true)->with('gameTemplate'),
                ])
                ->get();

            $standaloneQuizzes = Quiz::query()
                ->where('is_published', true)
                ->whereNull('topic_id')
                ->when($grade > 0, $gradeFilter)
                ->latest()
                ->limit(20)
                ->get();

            $standaloneMiniGames = MiniGame::query()
                ->where('is_published', true)
                ->whereNull('topic_id')
                ->when($grade > 0, $gradeFilter)
                ->with('gameTemplate')
                ->latest()
                ->limit(20)
                ->get();

            $topicsPayload = $topics->map(function ($t) {
                return [
                    'id' => $t->id,
                    'title' => $t->title,
                    'description' => $t->description,
                    'video_url' => $t->video_url,
                    'quizzes' => $t->quizzes->map(fn ($q) => [
                        'id' => $q->id,
                        'title' => $q->title,
                        'play_url' => route('play.quiz', $q),
                    ])->values()->all(),
                    'mini_games' => $t->miniGames->map(fn ($g) => [
                        'id' => $g->id,
                        'title' => $g->title,
                        'play_url' => route('play.mini-game', $g),
                    ])->values()->all(),
                ];
            })->values()->all();
        }

        $view->with(compact('topics', 'standaloneQuizzes', 'standaloneMiniGames', 'grade', 'topicsPayload'));
    }
}
