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
        return view('dashboard.student', [
            'studentPage' => 'dashboard',
            'studentLayoutTitle' => 'Student Dashboard',
        ]);
    }

    public function teacher(): View
    {
        $topicCount = Topic::query()->where('user_id', auth()->id())->count();

        return view('dashboard.teacher', compact('topicCount'));
    }

    public function parent(): View
    {
        return view('dashboard.parent');
    }
}
