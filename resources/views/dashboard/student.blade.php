@extends('layouts.student')

@section('title', __('messages.dashboard.my_learning'))

@section('student-main')
    <div class="eco-env-wrap">
        <div id="eco-env-container" class="eco-env-canvas" aria-hidden="true"></div>
        <div class="eco-env-overlay">
            <div class="eco-env-card">
                <h1 class="eco-env-hero">Hi, {{ \Illuminate\Support\Str::before(auth()->user()->name ?? 'Friend', ' ') }}! 👋</h1>
                <p class="eco-env-hero-sub">{{ __('messages.dashboard.choose_where') }}</p>
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
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .eco-game-area:has(.eco-env-wrap) { overflow: hidden; padding: 0; max-height: none; height: calc(100vh - 100px); min-height: 0; }
    .eco-env-wrap { position: relative; height: 100%; min-height: 480px; width: 100%; overflow: hidden; }
        .eco-env-canvas { position: absolute; inset: 0; width: 100%; height: 100%; display: block; }
        .eco-env-overlay { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; pointer-events: none; }
        .eco-env-card { pointer-events: auto; background: #fff; border-radius: 24px; padding: 2rem 2.5rem; border: 3px solid rgba(78, 205, 196, 0.5); box-shadow: 0 4px 16px rgba(0,0,0,0.1); width: 100%; max-width: 420px; }
        .eco-env-overlay .eco-env-gateways { display: flex; flex-wrap: wrap; justify-content: center; gap: 1.25rem; margin-top: 1rem; }
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
    </style>
@endpush

