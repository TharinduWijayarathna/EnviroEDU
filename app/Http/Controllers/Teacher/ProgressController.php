<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
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
            ->with(['enrolledClasses'])
            ->withCount(['quizAttempts', 'miniGameAttempts', 'badges'])
            ->orderBy('name');

        $grade = $request->integer('grade', 0);
        $allowedGrades = config('app.grade_levels', [4, 5]);
        if ($grade > 0 && in_array($grade, $allowedGrades, true)) {
            $query->where('grade_level', $grade);
        }

        $classId = $request->integer('class', 0);
        if ($classId > 0) {
            $query->whereHas('enrolledClasses', fn ($q) => $q->where('class_rooms.id', $classId));
        }

        $students = $query->paginate(20)->withQueryString();

        $classes = ClassRoom::query()
            ->where('school_id', $schoolId)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get(['id', 'name', 'grade_level']);

        return view('teacher.progress.index', compact('students', 'grade', 'classId', 'classes'));
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
