<?php

namespace App\Http\Controllers;

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
        return view('dashboard.teacher');
    }

    public function parent(): View
    {
        return view('dashboard.parent');
    }
}
