@extends('layouts.app')

@section('title', $child->name . ' – Progress')

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .eco-badge-pill { background: var(--eco-secondary); padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; }
        .weakness-row { background: #fff8f0; border-left: 4px solid var(--eco-accent); }
        .parent-child-grid { display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start; }
        .parent-section-card { background: #fff; border-radius: 20px; padding: 1.25rem; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 2px solid rgba(78, 205, 196, 0.3); margin-bottom: 1rem; }
        .parent-section-title { font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .parent-section-actions { display: flex; justify-content: flex-end; margin-top: 1rem; }
        .parent-game-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid #eee; }
        .parent-game-item:last-child { border-bottom: none; }
        .parent-game-progress { flex: 1; height: 8px; background: #e0e0e0; border-radius: 4px; overflow: hidden; }
        .parent-game-progress-fill { height: 100%; background: var(--eco-primary); border-radius: 4px; }
        @media (max-width: 900px) { .parent-child-grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #f8f8f8;">
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

        <main style="flex: 1; padding: 2rem; max-width: 1100px; margin: 0 auto; width: 100%;">
            <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.parent') }}" style="color: var(--eco-primary); font-weight: 600; text-decoration: none;">← Back to dashboard</a></p>
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $child->name }}</h1>
            <p style="margin-bottom: 1.5rem; color: #555;">Badges, progress, and areas to practice.</p>

            <div class="parent-child-grid">
                <div>
                    <div class="parent-section-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <h2 class="parent-section-title">🏆 Badges</h2>
                            <a href="{{ route('dashboard.parent') }}" class="eco-btn" style="padding: 0.4rem 0.9rem; font-size: 0.9rem;">All Badges →</a>
                        </div>
                        @forelse ($child->badges->take(1) as $badge)
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                <span style="font-size: 2.5rem;">{{ $badge->icon ?? '🏆' }}</span>
                                <div>
                                    <strong style="display: block; color: #333;">{{ $badge->name }}</strong>
                                    <p style="font-size: 0.9rem; color: #666; margin: 0.25rem 0;">Earned for completing activities!</p>
                                    <div style="height: 6px; background: #e0e0e0; border-radius: 3px; width: 80px; margin-top: 0.5rem; overflow: hidden;"><div style="height: 100%; width: 40%; background: var(--eco-secondary); border-radius: 3px;"></div></div>
                                </div>
                                <span style="background: var(--eco-secondary); width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">{{ $child->badges->count() }}</span>
                            </div>
                        @empty
                            <p style="color: #666; font-size: 0.95rem;">No badges yet. Completing quizzes and games will earn badges!</p>
                        @endforelse
                        <div class="parent-section-actions">
                            <a href="{{ route('dashboard.student') }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Take a Quiz →</a>
                        </div>
                    </div>

                    <div class="parent-section-card">
                        <h2 class="parent-section-title">📋 Quiz Attempts</h2>
                        @forelse ($child->quizAttempts->take(5) as $attempt)
                            <div class="eco-card" style="padding: 0.75rem 1rem; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                <strong>{{ $attempt->quiz->title ?? 'Quiz' }}</strong>
                                <span>{{ $attempt->score }}/{{ $attempt->total_questions }} · {{ $attempt->completed_at->format('M j, Y') }}</span>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 1rem; color: #666;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📋</div>
                                <p style="font-weight: 600;">No quiz attempts yet</p>
                                <p style="font-size: 0.9rem;">When {{ $child->name }} attempts quizzes, they will appear here.</p>
                            </div>
                        @endforelse
                        @if ($child->quizAttempts->isNotEmpty())
                            <p style="margin-top: 0.75rem;"><a href="{{ route('dashboard.parent') }}" style="color: var(--eco-primary); font-weight: 600;">View All →</a></p>
                        @endif
                        <div class="parent-section-actions">
                            <a href="{{ route('dashboard.student') }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Take a Quiz →</a>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="parent-section-card">
                        <h2 class="parent-section-title">🎮 Game Attempts</h2>
                        @forelse ($child->miniGameAttempts->take(5) as $attempt)
                            @php
                                $pct = isset($attempt->details['progress']) ? (int) $attempt->details['progress'] : 100;
                            @endphp
                            <div class="parent-game-item">
                                <span style="font-size: 1.25rem;">🌿</span>
                                <div style="flex: 1; min-width: 0;">
                                    <strong style="display: block; color: #333;">{{ $attempt->miniGame->title ?? 'Game' }}</strong>
                                    <span style="font-size: 0.85rem; color: #666;">{{ $attempt->completed_at->format('M j, Y') }}</span>
                                </div>
                                <span style="font-weight: 700; color: var(--eco-primary);">{{ $pct }}%</span>
                                <div class="parent-game-progress" style="width: 60px;"><div class="parent-game-progress-fill" style="width: {{ $pct }}%;"></div></div>
                            </div>
                        @empty
                            <p style="color: #666; font-size: 0.95rem;">No game attempts yet.</p>
                        @endforelse
                        @if ($child->miniGameAttempts->isNotEmpty())
                            <p style="margin-top: 0.75rem;"><a href="{{ route('dashboard.parent') }}" style="color: var(--eco-primary); font-weight: 600;">View All →</a></p>
                        @endif
                    </div>
                </div>
            </div>

            @if (!empty($weaknesses))
                <div class="parent-section-card" style="margin-top: 1rem;">
                    <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-accent); margin-bottom: 0.75rem;">📉 Areas to practice</h2>
                    <p style="color: #666; margin-bottom: 0.75rem;">Quizzes where average score is below 70%.</p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach ($weaknesses as $w)
                            <div class="eco-card weakness-row" style="padding: 0.75rem 1rem;">
                                <strong>{{ $w['title'] }}</strong> — {{ $w['average'] }}% average ({{ $w['attempts'] }} attempt(s))
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="margin-top: 1.5rem; padding: 0.75rem 1rem; background: #fffde7; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">💡</span>
                <p style="margin: 0; font-size: 0.95rem;"><strong>Tip:</strong> Once {{ $child->name }} attempts a quiz, you'll see their score and progress here!</p>
            </div>
        </main>
    </div>
@endsection
