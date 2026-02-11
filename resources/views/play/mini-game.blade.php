@extends('layouts.app')

@section('title', $miniGame->title)

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .play-game-container { max-width: 900px; margin: 0 auto; padding: 2rem; }
        .game-drop-zone { background: #f0f4f3; border: 4px dashed var(--eco-primary); border-radius: 15px; padding: 1rem; min-height: 120px; margin-bottom: 1rem; }
        .game-drop-zone.drag-over { background: rgba(78, 205, 196, 0.2); }
        .game-draggable { background: #fff; border: 2px solid var(--eco-secondary); border-radius: 12px; padding: 0.75rem 1rem; cursor: grab; display: inline-block; margin: 0.25rem; }
        .game-option { background: #fff; border: 2px solid var(--eco-secondary); border-radius: 12px; padding: 0.8rem 1rem; margin-bottom: 0.5rem; cursor: pointer; }
        .game-option:hover { background: var(--eco-secondary); }
        .game-option.correct { background: var(--eco-green); }
        .game-option.incorrect { background: var(--eco-accent); }
        .game-match-row { display: flex; gap: 1rem; align-items: center; margin-bottom: 0.75rem; }
        .game-match-left, .game-match-right { flex: 1; padding: 0.5rem; border-radius: 8px; }
        .match-item.selected { border-color: var(--eco-primary); background: rgba(78, 205, 196, 0.2); }
        .match-item.matched { opacity: 0.6; cursor: default; }
        .game-result { text-align: center; padding: 2rem; }
    </style>
@endpush

@section('content')
    <div style="min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ auth()->check() ? route('dashboard.student') : route('home') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <nav class="eco-dashboard-nav">
                <span style="font-weight: 600;">{{ $miniGame->title }}</span>
            </nav>
        </header>
        <div class="play-game-container">
            <div id="game-mount"></div>
        </div>
    </div>
    <script>
        window.EnviroEduGame = {
            template: @json($miniGame->gameTemplate->slug),
            config: @json($miniGame->config),
        };
    </script>
    @vite(['resources/js/game-runner.js'])
@endsection
