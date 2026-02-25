@extends('layouts.teacher')

@section('title', $miniGame->title)

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.mini-games.index') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Mini Games</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $miniGame->title }}</h1>
    <p style="color: #555; margin-bottom: 0.5rem;">{{ $miniGame->gameTemplate->name }}</p>
    @if ($miniGame->description)
        <p style="color: #555; margin-bottom: 1rem;">{{ $miniGame->description }}</p>
    @endif
    <p style="margin-bottom: 1.5rem; font-size: 0.9rem;">{{ $miniGame->is_published ? 'Published' : 'Draft' }}</p>

    <a href="{{ route('play.mini-game', $miniGame) }}" class="eco-btn" style="margin-bottom: 1rem;" target="_blank">Preview Game</a>
    <a href="{{ route('teacher.mini-games.edit', $miniGame) }}" class="eco-btn" style="margin-bottom: 1rem; margin-left: 0.5rem; background: #2C3E50;">Edit</a>
@endsection
