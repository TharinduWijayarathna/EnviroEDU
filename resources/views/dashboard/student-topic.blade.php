@extends('layouts.student')

@section('title', $topic->title)

@section('student-main')
    <div class="eco-student-back-bar">
        <a href="{{ route('dashboard.student') }}{{ request()->has('grade') ? '?grade=' . request('grade') : '' }}" class="eco-student-back-link">← Back to My Learning</a>
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
                        <iframe width="100%" height="100%" src="{{ $embedUrl }}" title="Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @else
                    <a href="{{ $topic->video_url }}" target="_blank" rel="noopener" class="eco-btn">📺 Watch video</a>
                @endif
            </div>
        @endif

        <div class="eco-kid-topic-actions">
            <h2 class="eco-kid-topic-actions-title">Play a quiz or game 🎮</h2>
            <div class="eco-kid-topic-buttons">
                @foreach ($topic->quizzes as $quiz)
                    <a href="{{ route('play.quiz', ['quiz' => $quiz->id]) }}" class="eco-btn eco-kid-topic-btn eco-kid-topic-btn-quiz">📝 {{ $quiz->title }}</a>
                @endforeach
                @foreach ($topic->miniGames as $game)
                    <a href="{{ route('play.mini-game', ['miniGame' => $game->id]) }}" class="eco-btn eco-kid-topic-btn eco-kid-topic-btn-game">🎮 {{ $game->title }}</a>
                @endforeach
            </div>
            @if ($topic->quizzes->isEmpty() && $topic->miniGames->isEmpty())
                <p class="eco-kid-no-games">No quizzes or games in this topic yet. Check back later! 🌟</p>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .eco-kid-topic-page { max-width: 720px; margin: 0 auto; }
        .eco-kid-topic-page-title { font-family: 'Bubblegum Sans', cursive; font-size: 1.6rem; color: var(--eco-dark); margin: 0 0 0.5rem; }
        .eco-kid-topic-desc { font-size: 1.1rem; color: #555; margin: 0 0 1.5rem; line-height: 1.5; }
        .eco-kid-topic-video { margin-bottom: 1.5rem; }
        .eco-kid-topic-actions-title { font-family: 'Bubblegum Sans', cursive; font-size: 1.3rem; color: var(--eco-dark); margin: 0 0 1rem; }
        .eco-kid-topic-buttons { display: flex; flex-direction: column; gap: 0.75rem; }
        .eco-kid-topic-btn { width: 100%; max-width: 400px; text-align: center; font-size: 1.1rem; padding: 0.9rem 1.25rem; }
        .eco-kid-topic-btn-game { background: #2C3E50; }
        .eco-kid-topic-btn-game:hover { background: #37474f; }
        .eco-kid-no-games { font-size: 1.05rem; color: #666; margin: 0; }
    </style>
@endpush
