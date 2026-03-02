@extends('layouts.app')

@section('title', $child->name . ' – ' . __('messages.parent.progress_title'))

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .eco-badge-pill { background: var(--eco-secondary); padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; }
        .weakness-row { background: #fff8f0; border-left: 4px solid var(--eco-accent); }
        .parent-child-grid { display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start; }
        .parent-section-card { background: #fff; border-radius: 20px; padding: 1.25rem; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 2px solid rgba(78, 205, 196, 0.3); margin-bottom: 1rem; }
        .parent-section-title { font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .parent-game-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid #eee; }
        .parent-game-item:last-child { border-bottom: none; }
        .parent-game-progress { flex: 1; height: 8px; background: #e0e0e0; border-radius: 4px; overflow: hidden; }
        .parent-game-progress-fill { height: 100%; background: var(--eco-primary); border-radius: 4px; }
        .parent-stats-row { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .parent-stat-card { background: #fff; border-radius: 16px; padding: 1rem 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 2px solid rgba(78, 205, 196, 0.25); display: flex; align-items: center; gap: 0.75rem; min-width: 140px; }
        .parent-stat-icon { font-size: 1.75rem; }
        .parent-stat-value { font-weight: 700; font-size: 1.5rem; color: var(--eco-primary); }
        .parent-stat-label { font-size: 0.85rem; color: #666; }
        .parent-badge-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #eee; }
        .parent-badge-item:last-child { border-bottom: none; }
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
                <a href="{{ route('dashboard.parent') }}" class="{{ request()->routeIs('dashboard.parent') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.nav.dashboard') }}</a>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">{{ __('messages.nav.logout') }}</button>
                </form>
            </nav>
        </header>

        <main style="flex: 1; padding: 2rem; max-width: 1100px; margin: 0 auto; width: 100%;">
            <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.parent') }}" style="color: var(--eco-primary); font-weight: 600; text-decoration: none;">{{ __('messages.parent.back_to_dashboard') }}</a></p>
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $child->name }}</h1>
            <p style="margin-bottom: 1.5rem; color: #555;">{{ __('messages.parent.progress_title') }}</p>

            @php
                $badgeCount = $child->badges->count();
                $quizAttempts = $child->quizAttempts;
                $quizAvg = $quizAttempts->isNotEmpty()
                    ? round(100 * $quizAttempts->sum('score') / $quizAttempts->sum('total_questions'), 0)
                    : null;
                $gameAttemptsCount = $child->miniGameAttempts->count() + $child->platformGameAttempts->count();
            @endphp

            <div class="parent-stats-row">
                <div class="parent-stat-card">
                    <span class="parent-stat-icon">🏆</span>
                    <div>
                        <div class="parent-stat-value">{{ $badgeCount }}</div>
                        <div class="parent-stat-label">{{ __('messages.parent.badges') }}</div>
                    </div>
                </div>
                <div class="parent-stat-card">
                    <span class="parent-stat-icon">📋</span>
                    <div>
                        <div class="parent-stat-value">{{ $quizAttempts->count() }}</div>
                        <div class="parent-stat-label">{{ __('messages.parent.quiz_attempts') }}</div>
                    </div>
                </div>
                @if ($quizAvg !== null)
                    <div class="parent-stat-card">
                        <span class="parent-stat-icon">📊</span>
                        <div>
                            <div class="parent-stat-value">{{ $quizAvg }}%</div>
                            <div class="parent-stat-label">{{ __('messages.parent.quiz_avg') }}</div>
                        </div>
                    </div>
                @endif
                <div class="parent-stat-card">
                    <span class="parent-stat-icon">🎮</span>
                    <div>
                        <div class="parent-stat-value">{{ $gameAttemptsCount }}</div>
                        <div class="parent-stat-label">{{ __('messages.parent.game_attempts') }}</div>
                    </div>
                </div>
            </div>

            <div class="parent-child-grid">
                <div>
                    <div class="parent-section-card">
                        <h2 class="parent-section-title">🏆 {{ __('messages.parent.badges') }}</h2>
                        @forelse ($child->badges as $badge)
                            <div class="parent-badge-item">
                                @if($badge->image_path)
                                    <img src="{{ asset('storage/'.$badge->image_path) }}" alt="" style="width: 48px; height: 48px; object-fit: contain; border-radius: 8px;">
                                @else
                                    <span style="font-size: 2rem;">{{ $badge->icon ?? '🏆' }}</span>
                                @endif
                                <div style="flex: 1; min-width: 0;">
                                    <strong style="display: block; color: #333;">{{ $badge->name }}</strong>
                                    @if ($badge->description)
                                        <p style="font-size: 0.9rem; color: #666; margin: 0.25rem 0;">{{ $badge->description }}</p>
                                    @endif
                                    <span style="font-size: 0.8rem; color: #888;">{{ $badge->pivot->earned_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                        @empty
                            <p style="color: #666; font-size: 0.95rem;">{{ __('messages.parent.no_badges_yet') }}</p>
                        @endforelse
                    </div>

                    <div class="parent-section-card">
                        <h2 class="parent-section-title">📋 {{ __('messages.parent.quiz_attempts') }}</h2>
                        @forelse ($child->quizAttempts->take(8) as $attempt)
                            <div class="eco-card" style="padding: 0.75rem 1rem; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                <strong>{{ $attempt->quiz->title ?? 'Quiz' }}</strong>
                                <span>{{ $attempt->score }}/{{ $attempt->total_questions }} · {{ $attempt->completed_at->format('M j, Y') }}</span>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 1rem; color: #666;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📋</div>
                                <p style="font-weight: 600;">{{ __('messages.parent.no_quiz_attempts') }}</p>
                                <p style="font-size: 0.9rem;">{{ __('messages.parent.quiz_attempts_desc', ['name' => $child->name]) }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="parent-section-card">
                        <h2 class="parent-section-title">🎮 {{ __('messages.parent.game_attempts') }}</h2>
                        @php
                            $miniGameAttempts = $child->miniGameAttempts->take(4);
                            $platformAttempts = $child->platformGameAttempts->take(4);
                        @endphp
                        @forelse ($miniGameAttempts->concat($platformAttempts)->sortByDesc('completed_at')->take(8) as $attempt)
                            @php
                                $gameTitle = $attempt->miniGame?->title ?? $attempt->platformGame?->title ?? 'Game';
                                $pct = isset($attempt->details['progress']) ? (int) $attempt->details['progress'] : 100;
                            @endphp
                            <div class="parent-game-item">
                                <span style="font-size: 1.25rem;">🌿</span>
                                <div style="flex: 1; min-width: 0;">
                                    <strong style="display: block; color: #333;">{{ $gameTitle }}</strong>
                                    <span style="font-size: 0.85rem; color: #666;">{{ $attempt->completed_at->format('M j, Y') }}</span>
                                </div>
                                <span style="font-weight: 700; color: var(--eco-primary);">{{ $pct }}%</span>
                                <div class="parent-game-progress" style="width: 60px;"><div class="parent-game-progress-fill" style="width: {{ $pct }}%;"></div></div>
                            </div>
                        @empty
                            <p style="color: #666; font-size: 0.95rem;">{{ __('messages.parent.no_game_attempts') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if (!empty($weaknesses))
                <div class="parent-section-card" style="margin-top: 1rem;">
                    <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-accent); margin-bottom: 0.75rem;">📉 {{ __('messages.parent.areas_to_practice') }}</h2>
                    <p style="color: #666; margin-bottom: 0.75rem;">{{ __('messages.parent.areas_to_practice_desc') }}</p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach ($weaknesses as $w)
                            <div class="eco-card weakness-row" style="padding: 0.75rem 1rem;">
                                <strong>{{ $w['title'] }}</strong> — {{ $w['average'] }}% {{ __('messages.parent.average') }} ({{ $w['attempts'] }} {{ __('messages.parent.attempts') }})
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($badgeCount === 0 && $quizAttempts->isEmpty() && $gameAttemptsCount === 0)
                <div style="margin-top: 1.5rem; padding: 0.75rem 1rem; background: #fffde7; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">💡</span>
                    <p style="margin: 0; font-size: 0.95rem;"><strong>{{ __('messages.parent.tip_title') }}:</strong> {{ __('messages.parent.tip', ['name' => $child->name]) }}</p>
                </div>
            @endif
        </main>
    </div>
@endsection
