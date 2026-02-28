@extends('layouts.student')

@section('title', $platformGame->title)

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .play-platform-game-container { max-width: 100%; margin: 0 auto; width: 100%; min-height: 70vh; position: relative; }
        #platform-game-mount { width: 100%; min-height: 700px; border-radius: 16px; overflow: hidden; background: linear-gradient(180deg, #87ceeb 0%, #e0f7fa 100%); }
    </style>
@endpush

@section('student-main')
    <div class="eco-student-back-bar">
        <a href="{{ route('dashboard.student.games') }}" class="eco-student-back-link">{{ __('messages.play.back_to_games') }}</a>
    </div>
    <div class="play-platform-game-container">
        <div id="platform-game-mount"></div>
    </div>
    <script>
        window.EnviroEduPlatformGame = {
            slug: @json($platformGame->slug),
            title: @json($platformGame->title),
            progressUrl: @json(route('progress.platform-game')),
            csrfToken: @json(csrf_token()),
        };
    </script>
    @switch($platformGame->slug)
        @case('photosynthesis')
            @vite(['resources/js/games/photosynthesis.js'])
            @break
        @case('seed-grow')
            @vite(['resources/js/games/seed-grow.js'])
            @break
        @case('vine-growth')
            @vite(['resources/js/games/vine-growth.js'])
            @break
        @case('star-patterns')
            @vite(['resources/js/games/star-patterns.js'])
            @break
        @case('rainbow')
            @vite(['resources/js/games/rainbow.js'])
            @break
        @case('mosquito-lifecycle')
            @vite(['resources/js/games/mosquito-lifecycle.js'])
            @break
        @case('water-cycle')
            @vite(['resources/js/games/water-cycle.js'])
            @break
        @case('day-night')
            @vite(['resources/js/games/day-night.js'])
            @break
        @case('solar-eclipse')
            @vite(['resources/js/games/solar-eclipse.js'])
            @break
        @case('lunar-eclipse')
            @vite(['resources/js/games/lunar-eclipse.js'])
            @break
        @default
            <p class="eco-env-empty">This game is not available yet.</p>
    @endswitch
@endsection
