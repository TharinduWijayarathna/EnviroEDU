@extends('layouts.student')

@section('title', 'Games')

@section('student-main')
    <div class="eco-env-wrap eco-env-wrap-list">
        <div id="eco-env-container" class="eco-env-canvas" aria-hidden="true"></div>
        <div class="eco-env-overlay eco-env-overlay-list">
            <a href="{{ url('/dashboard/student') }}{{ request()->has('grade') ? '?grade=' . request('grade') : '' }}" class="eco-env-back">← Back to My Learning</a>
            <div class="eco-env-panel">
                <h1 class="eco-env-panel-title">🎮 Games</h1>
                <p class="eco-env-panel-desc">Platform games and standalone games. More games are inside each topic.</p>

                @php $platformGames = $platformGames ?? collect(); @endphp
                @if ($platformGames->isNotEmpty())
                    <h2 class="eco-env-section-title">🌟 Platform Games</h2>
                    <ul class="eco-env-list">
                        @foreach ($platformGames as $game)
                            <li>
                                <a href="{{ route('play.platform-game', $game->slug) }}" class="eco-env-list-item eco-env-list-item-platform">
                                    <span class="eco-env-list-icon">🌟</span>
                                    <span class="eco-env-list-text">{{ $game->title }}</span>
                                    <span class="eco-env-list-go">Play →</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if (isset($standaloneMiniGames) && $standaloneMiniGames->isNotEmpty())
                    <h2 class="eco-env-section-title">🎮 Teacher Games</h2>
                    <ul class="eco-env-list">
                        @foreach ($standaloneMiniGames as $game)
                            <li>
                                <a href="{{ url('/play/game/'.$game->id) }}" class="eco-env-list-item eco-env-list-item-game">
                                    <span class="eco-env-list-icon">🎮</span>
                                    <span class="eco-env-list-text">{{ $game->title }}</span>
                                    <span class="eco-env-list-go">Play →</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($platformGames->isEmpty() && (!isset($standaloneMiniGames) || $standaloneMiniGames->isEmpty()))
                    <p class="eco-env-empty">No games yet. Open a topic to play its games! 🎮</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .eco-env-wrap-list { position: relative; min-height: calc(100vh - 160px); width: 100%; }
        .eco-env-overlay-list { align-items: flex-start; justify-content: flex-start; padding: 1.25rem 1.5rem; overflow-y: auto; }
        .eco-env-back { font-weight: 700; font-size: 1rem; color: var(--eco-primary); text-decoration: none; margin-bottom: 1rem; display: inline-block; position: relative; z-index: 10; pointer-events: auto; }
        .eco-env-back:hover { text-decoration: underline; }
        .eco-env-panel { background: rgba(255,255,255,0.95); border-radius: 20px; padding: 1.5rem; max-width: 560px; border: 2px solid rgba(78, 205, 196, 0.4); box-shadow: 0 8px 32px rgba(0,0,0,0.1); position: relative; z-index: 10; pointer-events: auto; }
        .eco-env-panel-title { font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: #1a3c34; margin: 0 0 0.35rem; }
        .eco-env-panel-desc { font-size: 0.95rem; color: #5a6c64; margin: 0 0 1rem; line-height: 1.4; }
        .eco-env-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.5rem; }
        .eco-env-list-item { display: flex; align-items: center; gap: 0.6rem; padding: 0.9rem 1.1rem; border-radius: 14px; text-decoration: none; color: #1a3c34; font-weight: 600; background: #f8fcfb; border: 2px solid transparent; transition: all 0.2s; }
        .eco-env-list-item:hover { background: #e8f7f5; border-color: var(--eco-primary); }
        .eco-env-section-title { font-size: 1.1rem; color: #1a3c34; margin: 1rem 0 0.5rem; }
        .eco-env-section-title:first-of-type { margin-top: 0; }
        .eco-env-list-item-platform { background: #fff8e1; }
        .eco-env-list-item-platform:hover { border-color: #ffb74d; background: #ffecb3; }
        .eco-env-list-item-game { background: #f5f8fa; }
        .eco-env-list-item-game:hover { border-color: #5a8ab0; background: #e3eef5; }
        .eco-env-list-icon { font-size: 1.3rem; }
        .eco-env-list-text { flex: 1; }
        .eco-env-list-go { font-size: 0.9rem; color: var(--eco-primary); }
        .eco-env-empty { margin: 0; color: #5a6c64; font-size: 1rem; }
    </style>
@endpush

@push('scripts')
    @vite(['resources/js/eco-student-env.js'])
@endpush
