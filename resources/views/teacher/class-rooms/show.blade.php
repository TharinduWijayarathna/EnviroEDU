@extends('layouts.teacher')

@section('title', 'Class: ' . $classRoom->name)

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.class-rooms.index') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to classes</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $classRoom->name }}</h1>
    @if ($classRoom->description)
        <p style="margin-bottom: 1.5rem; color: #555;">{{ $classRoom->description }}</p>
    @else
        <p style="margin-bottom: 1.5rem; color: #555;">Add students by email below. They must already have a student account.</p>
    @endif

    <div class="eco-card" style="margin-bottom: 1.5rem;">
        <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 0.75rem;">Invite student</h2>
        <form method="POST" action="{{ route('teacher.class-rooms.students.store', $classRoom) }}" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div>
                <label for="email" style="display: block; font-weight: 600; margin-bottom: 0.25rem;">Student email</label>
                <input id="email" type="email" name="email" class="eco-input" value="{{ old('email') }}" required placeholder="student@example.com" style="min-width: 220px;">
                @error('email')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="eco-btn">Add to class</button>
        </form>
    </div>

    <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 0.75rem;">Students ({{ $classRoom->students->count() }})</h2>
    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
        @forelse ($classRoom->students as $student)
            <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <strong>{{ $student->name }}</strong>
                    <span style="color: #666; font-size: 0.9rem;">{{ $student->email }}</span>
                </div>
                <form method="POST" action="{{ route('teacher.class-rooms.students.destroy', [$classRoom, $student]) }}" style="display: inline;" onsubmit="return confirm('Remove this student from the class?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="eco-logout-btn" style="padding: 0.4rem 0.75rem; font-size: 0.85rem;">Remove</button>
                </form>
            </div>
        @empty
            <div class="eco-card" style="text-align: center; color: #666;">
                <p>No students in this class yet. Use the form above to add students by their account email.</p>
            </div>
        @endforelse
    </div>
@endsection
