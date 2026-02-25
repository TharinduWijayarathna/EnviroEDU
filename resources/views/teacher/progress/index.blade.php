@extends('layouts.teacher')

@section('title', 'Student Progress')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.teacher') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Dashboard</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">Student Progress</h1>
    <p style="margin-bottom: 1.5rem; color: #555;">View quiz and game attempts, scores, and badges for each student.</p>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse ($students as $s)
            <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $s->name }}</h3>
                    <p style="color: #666; font-size: 0.9rem;">
                        {{ $s->quiz_attempts_count }} quiz attempts · {{ $s->mini_game_attempts_count }} game attempts · {{ $s->badges_count }} badges
                    </p>
                </div>
                <a href="{{ route('teacher.progress.show', $s) }}" class="eco-btn" style="padding: 0.5rem 1rem;">View details</a>
            </div>
        @empty
            <div class="eco-card" style="text-align: center; color: #666;">
                <p>No students have attempted quizzes or games yet.</p>
            </div>
        @endforelse
    </div>

    @if ($students->hasPages())
        <div style="margin-top: 1.5rem;">{{ $students->links() }}</div>
    @endif
@endsection
