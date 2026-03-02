@extends('layouts.student')

@section('title', __('messages.dashboard.my_learning'))

@section('student-main')
    <div class="eco-env-wrap">
        <div id="eco-env-container" class="eco-env-canvas" aria-hidden="true"></div>
        <div class="eco-env-overlay">
            <div class="eco-env-card">
                <h1 class="eco-env-hero">Hi, {{ \Illuminate\Support\Str::before(auth()->user()->name ?? 'Friend', ' ') }}! 👋</h1>
                @if (isset($enrolledClasses) && $enrolledClasses->isNotEmpty())
                    <p class="eco-env-hero-class" style="font-size: 0.95rem; color: #2d5a52; margin: 0.25rem 0 0;">{{ __('messages.dashboard.class') }}: {{ $enrolledClasses->pluck('name')->join(', ') }}</p>
                @endif
                <p class="eco-env-hero-sub">{{ __('messages.dashboard.choose_where') }}</p>
                <div class="eco-dashboard-split">
                    <div class="eco-dashboard-left">
                        <div class="eco-env-gateways">
                            <a href="{{ route('dashboard.student.topics') }}" class="eco-env-gate eco-env-gate-topics">
                                <span class="eco-env-gate-icon">📚</span>
                                <span class="eco-env-gate-label">{{ __('messages.dashboard.topics') }}</span>
                                <span class="eco-env-gate-arrow">→</span>
                            </a>
                            <a href="{{ route('dashboard.student.games') }}" class="eco-env-gate eco-env-gate-games">
                                <span class="eco-env-gate-icon">🎮</span>
                                <span class="eco-env-gate-label">{{ __('messages.dashboard.games') }}</span>
                                <span class="eco-env-gate-arrow">→</span>
                            </a>
                            <a href="{{ route('dashboard.student.quizzes') }}" class="eco-env-gate eco-env-gate-quizzes">
                                <span class="eco-env-gate-icon">📝</span>
                                <span class="eco-env-gate-label">{{ __('messages.dashboard.quizzes') }}</span>
                                <span class="eco-env-gate-arrow">→</span>
                            </a>
                        </div>
                    </div>

                    <div class="eco-dashboard-right">
                        @if (isset($leaderboard) && $leaderboard->isNotEmpty())
                            <div class="eco-leaderboard">
                                <h3 class="eco-leaderboard-title">{{ __('messages.dashboard.leaderboard') }}</h3>
                                <p class="eco-leaderboard-desc">{{ __('messages.dashboard.leaderboard_desc') }}</p>
                                <div class="eco-leaderboard-table">
                                    <div class="eco-leaderboard-header">
                                        <span>#</span>
                                        <span>{{ __('messages.dashboard.leaderboard_name') }}</span>
                                        <span>{{ __('messages.dashboard.leaderboard_quiz') }}</span>
                                        <span>{{ __('messages.dashboard.leaderboard_games') }}</span>
                                        <span>{{ __('messages.dashboard.leaderboard_total') }}</span>
                                    </div>
                                    @foreach ($leaderboard as $entry)
                                        <div class="eco-leaderboard-row {{ $entry['user']->id === auth()->id() ? 'eco-leaderboard-me' : '' }}">
                                            <span class="eco-leaderboard-rank">{{ $entry['rank'] }}</span>
                                            <span class="eco-leaderboard-name">{{ $entry['user']->name }}</span>
                                            <span>{{ $entry['quiz_score'] }}</span>
                                            <span>{{ $entry['game_score'] }}</span>
                                            <span class="eco-leaderboard-total">{{ $entry['total_score'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif (isset($enrolledClasses) && $enrolledClasses->isEmpty())
                            <p class="eco-leaderboard-empty">{{ __('messages.dashboard.leaderboard_no_class') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .eco-game-area:has(.eco-env-wrap) { overflow: hidden; padding: 0; max-height: none; height: calc(100vh - 100px); min-height: 0; }
    .eco-env-wrap { position: relative; height: 100%; min-height: 480px; width: 100%; overflow: hidden; }
        .eco-env-canvas { position: absolute; inset: 0; width: 100%; height: 100%; display: block; }
        .eco-env-overlay { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; pointer-events: none; }
        .eco-env-card { pointer-events: auto; background: #fff; border-radius: 24px; padding: 2rem 2.5rem; border: 3px solid rgba(78, 205, 196, 0.5); box-shadow: 0 4px 16px rgba(0,0,0,0.1); width: 100%; max-width: 980px; }
        .eco-dashboard-split { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start; margin-top: 1.25rem; }
        .eco-env-overlay .eco-env-gateways { display: flex; flex-wrap: wrap; justify-content: center; gap: 1.25rem; margin-top: 0; }
        .eco-dashboard-right { border-left: 2px solid rgba(78, 205, 196, 0.35); padding-left: 1.5rem; }
        .eco-env-hero { font-family: 'Bubblegum Sans', cursive; font-size: 1.9rem; color: #1a3c34; margin: 0 0 0.2rem; text-shadow: 0 1px 2px rgba(255,255,255,0.8); }
        .eco-env-hero-sub { font-size: 1.05rem; color: #2d5a52; margin: 0; text-shadow: 0 1px 2px rgba(255,255,255,0.8); }
        .eco-env-gate { display: flex; align-items: center; gap: 0.75rem; padding: 1.1rem 1.6rem; border-radius: 20px; text-decoration: none; color: #1a3c34; font-weight: 700; font-size: 1.2rem; background: rgba(255,255,255,0.92); border: 3px solid rgba(78, 205, 196, 0.6); box-shadow: 0 6px 24px rgba(0,0,0,0.12); transition: all 0.3s; cursor: pointer; position: relative; z-index: 10; animation: eco-float 4s ease-in-out infinite; }
        .eco-env-gate:nth-child(1) { animation-delay: 0s; }
        .eco-env-gate:nth-child(2) { animation-delay: 0.4s; }
        .eco-env-gate:nth-child(3) { animation-delay: 0.8s; }
        .eco-env-gate:hover { transform: scale(1.08); border-color: var(--eco-primary); box-shadow: 0 10px 32px rgba(78, 205, 196, 0.35); background: #fff; }
        .eco-env-gate-icon { font-size: 2rem; }
        .eco-env-gate-label { flex: 1; }
        .eco-env-gate-arrow { font-size: 1.1rem; color: var(--eco-primary); }
        .eco-env-gate-games { border-color: rgba(90, 138, 176, 0.6); }
        .eco-env-gate-games:hover { border-color: #5a8ab0; }
        .eco-env-gate-quizzes { border-color: rgba(212, 168, 75, 0.6); }
        .eco-env-gate-quizzes:hover { border-color: #d4a84b; }
        @keyframes eco-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .eco-leaderboard { margin-top: 0; padding-top: 0; border-top: 0; }
        .eco-leaderboard-title { font-size: 1.1rem; font-weight: 700; color: #1a3c34; margin: 0 0 0.25rem; }
        .eco-leaderboard-desc { font-size: 0.85rem; color: #2d5a52; margin: 0 0 0.75rem; }
        .eco-leaderboard-table { font-size: 0.9rem; }
        .eco-leaderboard-header { display: grid; grid-template-columns: 2rem 1fr 3.5rem 3.5rem 3.5rem; gap: 0.5rem; padding: 0.4rem 0; font-weight: 700; color: #1a3c34; border-bottom: 2px solid rgba(78, 205, 196, 0.5); }
        .eco-leaderboard-row { display: grid; grid-template-columns: 2rem 1fr 3.5rem 3.5rem 3.5rem; gap: 0.5rem; padding: 0.35rem 0; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.06); }
        .eco-leaderboard-row.eco-leaderboard-me { background: rgba(78, 205, 196, 0.2); margin: 0 -0.5rem; padding-left: 0.5rem; padding-right: 0.5rem; border-radius: 8px; font-weight: 600; }
        .eco-leaderboard-rank { font-weight: 700; color: var(--eco-primary); }
        .eco-leaderboard-total { font-weight: 700; color: #1a3c34; }
        .eco-leaderboard-empty { font-size: 0.9rem; color: #5a6b5d; margin: 1rem 0 0; }
        @media (max-width: 900px) {
            .eco-env-card { max-width: 540px; }
            .eco-dashboard-split { grid-template-columns: 1fr; }
            .eco-dashboard-right { border-left: 0; padding-left: 0; border-top: 2px solid rgba(78, 205, 196, 0.35); padding-top: 1.25rem; }
        }
    </style>
@endpush

