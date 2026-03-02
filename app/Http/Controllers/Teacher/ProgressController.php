<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    public function index(Request $request): View
    {
        $schoolId = auth()->user()->school_id;
        $query = User::query()
            ->where('role', Role::Student)
            ->where('school_id', $schoolId)
            ->withCount(['quizAttempts', 'miniGameAttempts', 'badges'])
            ->orderBy('name');

        $grade = $request->integer('grade', 0);
        $allowedGrades = config('app.grade_levels', [4, 5]);
        if ($grade > 0 && in_array($grade, $allowedGrades, true)) {
            $query->where('grade_level', $grade);
        }

        $students = $query->paginate(20)->withQueryString();

        return view('teacher.progress.index', compact('students', 'grade'));
    }

    public function show(User $student): View
    {
        if ($student->role !== Role::Student) {
            abort(404);
        }
        if ($student->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $student->load([
            'quizAttempts' => fn ($q) => $q->with('quiz')->latest('completed_at')->limit(50),
            'miniGameAttempts' => fn ($q) => $q->with('miniGame')->latest('completed_at')->limit(50),
            'badges',
        ]);

        return view('teacher.progress.show', compact('student'));
    }
}
