@extends('layouts.teacher')

@section('title', 'My Mini Games')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.teacher') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Dashboard</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">My Mini Games</h1>
    <p style="margin-bottom: 1.5rem; color: #555;">Create customizable mini games from templates.</p>

    <a href="{{ route('teacher.mini-games.create') }}" class="eco-btn" style="margin-bottom: 1.5rem;">+ New Mini Game</a>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse ($miniGames as $game)
            <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $game->title }}</h3>
                    <p style="color: #666; font-size: 0.9rem;">{{ $game->gameTemplate->name }} · {{ $game->is_published ? 'Published' : 'Draft' }}</p>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('play.mini-game', $game) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;" target="_blank">Preview</a>
                    <a href="{{ route('teacher.mini-games.edit', $game) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #2C3E50;">Edit</a>
                    <form method="POST" action="{{ route('teacher.mini-games.destroy', $game) }}" style="display: inline;" onsubmit="return confirm('Delete this game?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="eco-logout-btn" style="padding: 0.5rem 1rem;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="eco-card" style="text-align: center; color: #666;">
                <p>No mini games yet. Create one from a template!</p>
                <a href="{{ route('teacher.mini-games.create') }}" class="eco-btn" style="margin-top: 1rem;">Create Mini Game</a>
            </div>
        @endforelse
    </div>

    @if ($miniGames->hasPages())
        <div style="margin-top: 1.5rem;">{{ $miniGames->links() }}</div>
    @endif
@endsection
