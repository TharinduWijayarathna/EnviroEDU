<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\ClassRoom;
use App\Models\MiniGame;
use App\Models\Quiz;
use App\Models\Topic;
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
        $earnedBadges = [];
        if (auth()->check() && auth()->user()->role?->value === 'student') {
            $earnedBadges = auth()->user()->badges()->orderByPivot('earned_at', 'desc')->get();
        }

        return view('dashboard.student', [
            'studentPage' => 'dashboard',
            'studentLayoutTitle' => 'Student Dashboard',
            'earnedBadges' => $earnedBadges,
        ]);
    }

    public function teacher(): View
    {
        $userId = auth()->id();
        $classCount = ClassRoom::query()->where('user_id', $userId)->count();
        $studentCount = ClassRoom::query()
            ->where('user_id', $userId)
            ->withCount('students')
            ->get()
            ->sum('students_count');
        $topicCount = Topic::query()->where('user_id', $userId)->count();
        $quizCount = Quiz::query()->where('user_id', $userId)->count();
        $miniGameCount = MiniGame::query()->where('user_id', $userId)->count();
        $topicIds = Topic::query()->where('user_id', $userId)->pluck('id');
        $badgeCount = Badge::query()->whereIn('topic_id', $topicIds)->count();

        return view('dashboard.teacher', compact('classCount', 'studentCount', 'topicCount', 'quizCount', 'miniGameCount', 'badgeCount'));
    }

    public function parent(): View
    {
        $children = auth()->user()->children()->orderBy('name')->get();

        return view('dashboard.parent', compact('children'));
    }
}
