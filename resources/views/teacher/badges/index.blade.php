@extends('layouts.teacher')

@section('title', 'Badges')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.teacher') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Dashboard</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">🏆 Badges</h1>
    <p style="margin-bottom: 1.5rem; color: #555;">Create badges for topics. Students earn a badge when they complete a quiz or game in that topic, depending on the badge settings.</p>

    <a href="{{ route('teacher.badges.create') }}" class="eco-btn" style="margin-bottom: 1.5rem;">+ New Badge</a>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse ($badges as $badge)
            <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    @if($badge->image_path)
                        <img src="{{ asset('storage/'.$badge->image_path) }}" alt="" style="width: 48px; height: 48px; object-fit: contain; border-radius: 8px;">
                    @else
                        <span style="font-size: 2rem;">{{ $badge->icon ?? '🏆' }}</span>
                    @endif
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $badge->name }}</h3>
                        <p style="color: #666; font-size: 0.9rem;">
                            Topic: {{ $badge->topic?->title ?? '—' }}
                            · Award for: {{ $badge->award_for?->label() ?? '—' }}
                        </p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('teacher.badges.show', $badge) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View</a>
                    <a href="{{ route('teacher.badges.edit', $badge) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #2C3E50;">Edit</a>
                    <form method="POST" action="{{ route('teacher.badges.destroy', $badge) }}" style="display: inline;" onsubmit="return confirm('Delete this badge?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="eco-logout-btn" style="padding: 0.5rem 1rem;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="eco-card" style="text-align: center; color: #666;">
                <p>No badges yet. Create a topic first, then add badges that students can earn by completing quizzes or games in that topic.</p>
                <a href="{{ route('teacher.badges.create') }}" class="eco-btn" style="margin-top: 1rem;">Create Badge</a>
            </div>
        @endforelse
    </div>

    @if ($badges->hasPages())
        <div style="margin-top: 1.5rem;">{{ $badges->links() }}</div>
    @endif
@endsection
