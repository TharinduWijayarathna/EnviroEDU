@extends('layouts.student')

@section('title', $topic->title)

@section('student-main')
    <div class="eco-student-back-bar">
        <a href="{{ route('dashboard.student') }}" class="eco-student-back-link">{{ __('messages.play.back_to_my_learning') }}</a>
    </div>
    <div class="eco-kid-topic-page">
        <h1 class="eco-kid-topic-page-title">📚 {{ $topic->title }}</h1>
        @if ($topic->description)
            <p class="eco-kid-topic-desc">{{ $topic->description }}</p>
        @endif

        @if ($topic->video_url)
            <div class="eco-kid-topic-video">
                @php
                    $url = $topic->video_url;
                    $embedUrl = null;
                    if (str_contains($url, 'youtube.com') && preg_match('/[?&]v=([^&]+)/', $url, $m)) {
                        $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
                    } elseif (preg_match('#youtu\.be/([^/?]+)#', $url, $m)) {
                        $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
                    }
                @endphp
                @if ($embedUrl)
                    <div class="eco-video-embed" style="max-width: 100%; border-radius: 16px; overflow: hidden; background: #2C3E50; aspect-ratio: 16/9;">
                        <iframe width="100%" height="100%" src="{{ $embedUrl }}" title="{{ __('messages.play.watch_video') }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @else
                    <a href="{{ $topic->video_url }}" target="_blank" rel="noopener" class="eco-btn">📺 {{ __('messages.play.watch_video') }}</a>
                @endif
            </div>
        @endif

        <div class="eco-kid-topic-actions">
            <h2 class="eco-kid-topic-actions-title">{{ __('messages.play.play_quiz_or_game') }} 🎮</h2>
            <div class="eco-kid-topic-buttons">
                @foreach ($topic->quizzes as $quiz)
                    <a href="{{ route('play.quiz', ['quiz' => $quiz->id]) }}" class="eco-btn eco-kid-topic-btn eco-kid-topic-btn-quiz">📝 {{ $quiz->title }}</a>
                @endforeach
                @foreach ($topic->miniGames as $game)
                    <a href="{{ route('play.mini-game', ['miniGame' => $game->id]) }}" class="eco-btn eco-kid-topic-btn eco-kid-topic-btn-game">🎮 {{ $game->title }}</a>
                @endforeach
            </div>
            @if ($topic->quizzes->isEmpty() && $topic->miniGames->isEmpty())
                <p class="eco-kid-no-games">{{ __('messages.play.no_quizzes_games') }} 🌟</p>
            @endif
        </div>
    </div>
@endsection
