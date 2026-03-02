<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function students(): View
    {
        $schoolId = auth()->user()->school_id;
        $students = User::query()
            ->where('role', Role::Student)
            ->where('school_id', $schoolId)
            ->with(['enrolledClasses'])
            ->withCount(['quizAttempts', 'miniGameAttempts', 'badges'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.students', compact('students'));
    }

    public function teachers(): View
    {
        $schoolId = auth()->user()->school_id;
        $teachers = User::query()
            ->where('role', Role::Teacher)
            ->where('school_id', $schoolId)
            ->withCount(['teachingClasses'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.teachers', compact('teachers'));
    }
}
