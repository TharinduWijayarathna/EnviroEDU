<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(): View
    {
        $school = auth()->user()->school;
        if (! $school) {
            abort(404);
        }
        $pendingTeachers = $school->pendingTeachers()->orderBy('name')->get();
        $pendingStudents = $school->pendingStudents()->orderBy('name')->get();

        return view('admin.approvals.index', [
            'school' => $school,
            'pendingTeachers' => $pendingTeachers,
            'pendingStudents' => $pendingStudents,
        ]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $school = auth()->user()->school;
        if (! $school || $user->school_id !== $school->id || $user->is_approved) {
            abort(403);
        }
        $user->update(['is_approved' => true]);

        return back()->with('status', "{$user->name} has been approved.");
    }
}
