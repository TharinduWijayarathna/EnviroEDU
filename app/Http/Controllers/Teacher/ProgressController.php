<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(): View
    {
        $students = User::query()
            ->where('role', Role::Student)
            ->withCount(['quizAttempts', 'miniGameAttempts', 'badges'])
            ->orderBy('name')
            ->paginate(20);

        return view('teacher.progress.index', compact('students'));
    }

    public function show(User $student): View
    {
        if ($student->role !== Role::Student) {
            abort(404);
        }

        $student->load([
            'quizAttempts' => fn ($q) => $q->with('quiz')->latest('completed_at')->limit(50),
            'miniGameAttempts' => fn ($q) => $q->with('miniGame')->latest('completed_at')->limit(50),
            'badges',
        ]);

        return view('teacher.progress.show', compact('student'));
    }
}
