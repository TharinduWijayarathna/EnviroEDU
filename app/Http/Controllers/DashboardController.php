<?php

namespace App\Http\Controllers;

use App\Models\MiniGame;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = auth()->user();
        $role = $user->role?->value ?? 'student';

        return redirect()->route("dashboard.{$role}");
    }

    public function student(): View
    {
        $quizzes = Quiz::query()->where('is_published', true)->latest()->limit(20)->get();
        $miniGames = MiniGame::query()->where('is_published', true)->with('gameTemplate')->latest()->limit(20)->get();

        return view('dashboard.student', compact('quizzes', 'miniGames'));
    }

    public function teacher(): View
    {
        return view('dashboard.teacher');
    }

    public function parent(): View
    {
        return view('dashboard.parent');
    }
}
