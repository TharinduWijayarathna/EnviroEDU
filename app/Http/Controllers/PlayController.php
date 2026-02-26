<?php

namespace App\Http\Controllers;

use App\Models\MiniGame;
use App\Models\Quiz;
use Illuminate\View\View;

class PlayController extends Controller
{
    public function quiz(Quiz $quiz): View
    {
        $canView = $quiz->is_published || (auth()->check() && $quiz->user_id === auth()->id());
        if (! $canView) {
            abort(404);
        }
        $quiz->load('questions.options');

        return view('play.quiz', [
            'quiz' => $quiz,
            'studentPage' => 'play',
            'studentLayoutTitle' => $quiz->title,
            'studentFullWidth' => true,
        ]);
    }

    public function miniGame(MiniGame $miniGame): View
    {
        $canView = $miniGame->is_published || (auth()->check() && $miniGame->user_id === auth()->id());
        if (! $canView) {
            abort(404);
        }
        $miniGame->load('gameTemplate');

        return view('play.mini-game', [
            'miniGame' => $miniGame,
            'studentPage' => 'play',
            'studentLayoutTitle' => $miniGame->title,
            'studentFullWidth' => true,
        ]);
    }
}
