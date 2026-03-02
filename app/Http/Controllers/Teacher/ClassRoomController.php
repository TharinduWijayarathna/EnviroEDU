<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRoomRequest;
use App\Http\Requests\UpdateClassRoomRequest;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassRoomController extends Controller
{
    public function index(): View
    {
        $classRooms = ClassRoom::query()
            ->where('user_id', auth()->id())
            ->withCount('students')
            ->orderBy('name')
            ->paginate(15);

        return view('teacher.class-rooms.index', compact('classRooms'));
    }

    public function create(): View
    {
        return view('teacher.class-rooms.create');
    }

    public function store(StoreClassRoomRequest $request): RedirectResponse
    {
        ClassRoom::query()->create([
            'user_id' => auth()->id(),
            'school_id' => auth()->user()->school_id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'grade_level' => (int) $request->input('grade_level'),
        ]);

        return redirect()->route('teacher.class-rooms.index')->with('status', 'Class created.');
    }

    public function show(ClassRoom $classRoom): View|RedirectResponse
    {
        if ($classRoom->user_id !== auth()->id()) {
            abort(403);
        }
        $classRoom->load('students');

        return view('teacher.class-rooms.show', compact('classRoom'));
    }

    public function edit(ClassRoom $classRoom): View|RedirectResponse
    {
        if ($classRoom->user_id !== auth()->id()) {
            abort(403);
        }

        return view('teacher.class-rooms.edit', compact('classRoom'));
    }

    public function update(UpdateClassRoomRequest $request, ClassRoom $classRoom): RedirectResponse
    {
        if ($classRoom->user_id !== auth()->id()) {
            abort(403);
        }

        $classRoom->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'grade_level' => (int) $request->input('grade_level'),
        ]);

        return redirect()->route('teacher.class-rooms.show', $classRoom)->with('status', 'Class updated.');
    }

    public function destroy(ClassRoom $classRoom): RedirectResponse
    {
        if ($classRoom->user_id !== auth()->id()) {
            abort(403);
        }
        $classRoom->delete();

        return redirect()->route('teacher.class-rooms.index')->with('status', 'Class deleted.');
    }

    public function addStudent(Request $request, ClassRoom $classRoom): RedirectResponse
    {
        if ($classRoom->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $student = User::query()
            ->where('email', $request->input('email'))
            ->where('role', Role::Student)
            ->first();

        if (! $student) {
            return back()->withErrors(['email' => 'No student account found with this email.']);
        }

        if ($classRoom->students()->where('user_id', $student->id)->exists()) {
            return back()->with('status', 'Student is already in this class.');
        }

        $classRoom->students()->attach($student->id);

        return back()->with('status', $student->name.' has been added to the class.');
    }

    public function removeStudent(ClassRoom $classRoom, User $student): RedirectResponse
    {
        if ($classRoom->user_id !== auth()->id()) {
            abort(403);
        }
        if ($student->role !== Role::Student) {
            abort(404);
        }

        $classRoom->students()->detach($student->id);

        return back()->with('status', 'Student removed from class.');
    }
}
