@extends('layouts.app')

@section('title', 'Test Platform Games')

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .test-games-wrap { max-width: 900px; margin: 0 auto; padding: 2rem 1rem; }
        .test-games-title { font-family: 'Bubblegum Sans', cursive; font-size: 1.75rem; color: var(--eco-primary); margin: 0 0 0.5rem; }
        .test-games-subtitle { color: #666; margin-bottom: 2rem; }
        .test-games-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem; }
        .test-game-card { display: block; background: #fff; border: 3px solid var(--eco-primary); border-radius: 16px; padding: 1.25rem; text-decoration: none; color: var(--eco-dark); transition: transform 0.2s, box-shadow 0.2s; }
        .test-game-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(78, 205, 196, 0.3); }
        .test-game-emoji { font-size: 2rem; display: block; margin-bottom: 0.5rem; }
        .test-game-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 0.35rem; }
        .test-game-desc { font-size: 0.9rem; color: #666; line-height: 1.4; }
    </style>
@endpush

@section('content')
    <div class="test-games-wrap">
        <h1 class="test-games-title">🎮 Test Platform Games</h1>
        <p class="test-games-subtitle">Click a game to open and test it.</p>

        @if($platformGames->isEmpty())
            <p>No platform games found. Run <code>php artisan db:seed</code> to seed the games.</p>
        @else
            <div class="test-games-grid">
                @foreach($platformGames as $game)
                    @php
                        $icons = [
                            'photosynthesis' => '🌻',
                            'seed-grow' => '🌱',
                            'vine-growth' => '🌿',
                            'star-patterns' => '⭐',
                            'rainbow' => '🌈',
                            'water-cycle' => '💧',
                            'day-night' => '🌍',
                            'solar-eclipse' => '🌑',
                            'lunar-eclipse' => '🌒',
                        ];
                        $icon = $icons[$game->slug] ?? '🎮';
                    @endphp
                    <a href="{{ route('play.platform-game', $game->slug) }}" class="test-game-card">
                        <span class="test-game-emoji">{{ $icon }}</span>
                        <span class="test-game-title">{{ $game->title }}</span>
                        <span class="test-game-desc">{{ Str::limit($game->description, 80) }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
