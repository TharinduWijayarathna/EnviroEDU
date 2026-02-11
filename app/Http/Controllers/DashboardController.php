<?php

namespace App\Http\Controllers;

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
        $topicCount = Topic::query()->where('user_id', auth()->id())->count();

        return view('dashboard.teacher', compact('topicCount'));
    }

    public function parent(): View
    {
        $children = auth()->user()->children()->orderBy('name')->get();

        return view('dashboard.parent', compact('children'));
    }
}
