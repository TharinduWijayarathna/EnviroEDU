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

        return view('play.quiz', compact('quiz'));
    }

    public function miniGame(MiniGame $miniGame): View
    {
        $canView = $miniGame->is_published || (auth()->check() && $miniGame->user_id === auth()->id());
        if (! $canView) {
            abort(404);
        }
        $miniGame->load('gameTemplate');

        return view('play.mini-game', compact('miniGame'));
    }
}
