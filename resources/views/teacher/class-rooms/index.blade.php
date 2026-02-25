@extends('layouts.teacher')

@section('title', 'My Classes')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.teacher') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Dashboard</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">My Classes</h1>
    <p style="margin-bottom: 1.5rem; color: #555;">Create classes and add students by email. Students must already have an account.</p>

    <a href="{{ route('teacher.class-rooms.create') }}" class="eco-btn" style="margin-bottom: 1.5rem;">+ Add Class</a>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse ($classRooms as $classRoom)
            <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $classRoom->name }}</h3>
                    @if ($classRoom->description)
                        <p style="color: #666; font-size: 0.9rem;">{{ Str::limit($classRoom->description, 120) }}</p>
                    @endif
                    <p style="color: #666; font-size: 0.9rem; margin-top: 0.35rem;">{{ $classRoom->students_count }} student(s)</p>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('teacher.class-rooms.show', $classRoom) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View</a>
                    <a href="{{ route('teacher.class-rooms.edit', $classRoom) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #2C3E50;">Edit</a>
                    <form method="POST" action="{{ route('teacher.class-rooms.destroy', $classRoom) }}" style="display: inline;" onsubmit="return confirm('Delete this class? Students will be unenrolled.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="eco-logout-btn" style="padding: 0.5rem 1rem;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="eco-card" style="text-align: center; color: #666;">
                <p>No classes yet. Create a class, then invite students by adding their account email.</p>
                <a href="{{ route('teacher.class-rooms.create') }}" class="eco-btn" style="margin-top: 1rem;">Add Class</a>
            </div>
        @endforelse
    </div>

    @if ($classRooms->hasPages())
        <div style="margin-top: 1.5rem;">{{ $classRooms->links() }}</div>
    @endif
@endsection
