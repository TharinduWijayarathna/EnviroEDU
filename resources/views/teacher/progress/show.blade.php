@extends('layouts.teacher')

@section('title', 'Progress: ' . $student->name)

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.progress.index') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Student Progress</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $student->name }}</h1>
    <p style="margin-bottom: 1.5rem; color: #555;">Quiz attempts, game attempts, and earned badges.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="eco-card" style="padding: 1rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--eco-primary);">{{ $student->quizAttempts->count() }}</div>
            <div style="color: #666;">Quiz attempts</div>
        </div>
        <div class="eco-card" style="padding: 1rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--eco-primary);">{{ $student->miniGameAttempts->count() }}</div>
            <div style="color: #666;">Game attempts</div>
        </div>
        <div class="eco-card" style="padding: 1rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--eco-secondary);">{{ $student->badges->count() }}</div>
            <div style="color: #666;">Badges</div>
        </div>
    </div>

    <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 0.75rem;">🏆 Badges</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 2rem;">
        @forelse ($student->badges as $badge)
            <span class="eco-card" style="padding: 0.5rem 1rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.5rem;">{{ $badge->icon ?? '🏆' }}</span>
                <span>{{ $badge->name }}</span>
            </span>
        @empty
            <p style="color: #666;">No badges yet.</p>
        @endforelse
    </div>

    <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 0.75rem;">📝 Quiz attempts</h2>
    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
        @forelse ($student->quizAttempts as $attempt)
            <div class="eco-card" style="padding: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <strong>{{ $attempt->quiz->title ?? 'Quiz' }}</strong>
                        <span style="color: #666;"> — {{ $attempt->score }}/{{ $attempt->total_questions }}</span>
                    </div>
                    <span style="font-size: 0.85rem; color: #666;">{{ $attempt->completed_at->format('M j, Y g:i A') }}</span>
                </div>
                @if (!empty($attempt->answers))
                    <details style="margin-top: 0.75rem;">
                        <summary style="cursor: pointer; font-size: 0.9rem; color: var(--eco-primary);">View answers</summary>
                        <ul style="margin-top: 0.5rem; padding-left: 1.25rem; font-size: 0.9rem;">
                            @foreach ($attempt->answers as $a)
                                <li>{{ $a['correct'] ? '✓' : '✗' }} Question (option {{ $a['option_index'] ?? '?' }})</li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </div>
        @empty
            <p style="color: #666;">No quiz attempts yet.</p>
        @endforelse
    </div>

    <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 0.75rem;">🎮 Game attempts</h2>
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        @forelse ($student->miniGameAttempts as $attempt)
            <div class="eco-card" style="padding: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <strong>{{ $attempt->miniGame->title ?? 'Game' }}</strong>
                <span style="font-size: 0.85rem; color: #666;">{{ $attempt->completed_at->format('M j, Y g:i A') }}</span>
            </div>
        @empty
            <p style="color: #666;">No game attempts yet.</p>
        @endforelse
    </div>
@endsection
