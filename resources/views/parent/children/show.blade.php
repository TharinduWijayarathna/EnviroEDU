@extends('layouts.app')

@section('title', $child->name . ' – Progress')

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .eco-badge-pill { background: var(--eco-secondary); padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; }
        .weakness-row { background: #fff8f0; border-left: 4px solid var(--eco-accent); }
    </style>
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ route('dashboard.parent') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <nav class="eco-dashboard-nav">
                <a href="{{ route('dashboard.parent') }}">Dashboard</a>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">Logout</button>
                </form>
            </nav>
        </header>

        <main style="flex: 1; padding: 2rem; max-width: 900px; margin: 0 auto; width: 100%;">
            <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.parent') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to dashboard</a></p>
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $child->name }}</h1>
            <p style="margin-bottom: 1.5rem; color: #555;">Badges, progress, and areas to practice.</p>

            <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 0.75rem;">🏆 Badges</h2>
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 2rem;">
                @forelse ($child->badges as $badge)
                    <span class="eco-badge-pill"><span>{{ $badge->icon ?? '🏆' }}</span>{{ $badge->name }}</span>
                @empty
                    <p style="color: #666;">No badges yet. Completing quizzes and games will earn badges!</p>
                @endforelse
            </div>

            <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 0.75rem;">📝 Quiz attempts</h2>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 2rem;">
                @forelse ($child->quizAttempts as $attempt)
                    <div class="eco-card" style="padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <strong>{{ $attempt->quiz->title ?? 'Quiz' }}</strong>
                        <span>{{ $attempt->score }}/{{ $attempt->total_questions }} · {{ $attempt->completed_at->format('M j, Y') }}</span>
                    </div>
                @empty
                    <p style="color: #666;">No quiz attempts yet.</p>
                @endforelse
            </div>

            <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 0.75rem;">🎮 Game attempts</h2>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 2rem;">
                @forelse ($child->miniGameAttempts as $attempt)
                    <div class="eco-card" style="padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <strong>{{ $attempt->miniGame->title ?? 'Game' }}</strong>
                        <span>{{ $attempt->completed_at->format('M j, Y') }}</span>
                    </div>
                @empty
                    <p style="color: #666;">No game attempts yet.</p>
                @endforelse
            </div>

            @if (!empty($weaknesses))
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-accent); margin-bottom: 0.75rem;">📉 Areas to practice</h2>
                <p style="color: #666; margin-bottom: 0.75rem;">Quizzes where average score is below 70%.</p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach ($weaknesses as $w)
                        <div class="eco-card weakness-row" style="padding: 0.75rem 1rem;">
                            <strong>{{ $w['title'] }}</strong> — {{ $w['average'] }}% average ({{ $w['attempts'] }} attempt(s))
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
@endsection
