@extends('layouts.admin')

@section('title', 'Pending approvals')

@section('admin')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">Pending approvals</h1>
    <p style="margin-bottom: 1.5rem; font-size: 1rem; color: #555;">Approve teachers and students who have registered with your school code.</p>

    @if ($pendingTeachers->isEmpty() && $pendingStudents->isEmpty())
        <div class="eco-card" style="max-width: 480px; padding: 1.5rem;">
            <p style="color: #666;">No pending approvals.</p>
            <a href="{{ route('dashboard.admin') }}" class="eco-btn" style="margin-top: 1rem;">Back to dashboard</a>
        </div>
    @else
        @if ($pendingTeachers->isNotEmpty())
            <div class="eco-card" style="margin-bottom: 1.5rem;">
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 1rem;">Teachers</h2>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach ($pendingTeachers as $teacher)
                        <li style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #eee;">
                            <span>{{ $teacher->name }} — {{ $teacher->email }}</span>
                            <form method="POST" action="{{ route('admin.approvals.approve', $teacher) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="eco-btn" style="padding: 0.4rem 1rem; font-size: 0.9rem;">Approve</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($pendingStudents->isNotEmpty())
            <div class="eco-card">
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 1rem;">Students</h2>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach ($pendingStudents as $student)
                        <li style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #eee;">
                            <span>{{ $student->name }} — {{ $student->email }}</span>
                            <form method="POST" action="{{ route('admin.approvals.approve', $student) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="eco-btn" style="padding: 0.4rem 1rem; font-size: 0.9rem;">Approve</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
@endsection
