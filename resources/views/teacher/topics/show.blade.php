@extends('layouts.teacher')

@section('title', $topic->title)

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $topic->title }}</h1>
    <p style="margin-bottom: 1.5rem; color: #555;">{{ $topic->description }}</p>

    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <a href="{{ route('teacher.topics.edit', $topic) }}" class="eco-btn" style="background: #2C3E50;">Edit Topic</a>
        <form method="POST" action="{{ route('teacher.topics.destroy', $topic) }}" style="display: inline;" onsubmit="return confirm('Delete this topic?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="eco-logout-btn">Delete</button>
        </form>
    </div>

    <div class="eco-card" style="margin-bottom: 1rem; padding: 1rem;">
        <h2 style="font-size: 1.1rem; margin-bottom: 0.75rem;">Quizzes in this topic</h2>
        @forelse ($topic->quizzes as $quiz)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                <span>{{ $quiz->title }}</span>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="eco-btn" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">Edit</a>
                    <a href="{{ route('play.quiz', $quiz) }}" target="_blank" class="eco-btn" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">Preview</a>
                </div>
            </div>
        @empty
            <p style="color: #666; font-size: 0.9rem;">No quizzes linked. Edit a quiz and set this topic.</p>
        @endforelse
    </div>

    <div class="eco-card" style="padding: 1rem;">
        <h2 style="font-size: 1.1rem; margin-bottom: 0.75rem;">Mini games in this topic</h2>
        @forelse ($topic->miniGames as $game)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                <span>{{ $game->title }}</span>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('teacher.mini-games.edit', $game) }}" class="eco-btn" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">Edit</a>
                    <a href="{{ route('play.mini-game', $game) }}" target="_blank" class="eco-btn" style="padding: 0.25rem 0.5rem; font-size: 0.85rem;">Preview</a>
                </div>
            </div>
        @empty
            <p style="color: #666; font-size: 0.9rem;">No mini games linked. Edit a mini game and set this topic.</p>
        @endforelse
    </div>
@endsection
