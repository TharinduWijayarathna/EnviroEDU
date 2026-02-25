@extends('layouts.teacher')

@section('title', 'My Topics')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.teacher') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Dashboard</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">My Topics</h1>
    <p style="margin-bottom: 1.5rem; color: #555;">Create topics (lessons) with optional video and attach quizzes or games. Students see topics first, then video and activities.</p>

    <a href="{{ route('teacher.topics.create') }}" class="eco-btn" style="margin-bottom: 1.5rem;">+ New Topic</a>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse ($topics as $topic)
            <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $topic->title }}</h3>
                    <p style="color: #666; font-size: 0.9rem;">
                        @if ($topic->grade_level) Grade {{ $topic->grade_level }} · @endif
                        {{ $topic->quizzes_count }} quiz(zes), {{ $topic->mini_games_count }} game(s)
                        {{ $topic->video_url ? ' · 📺 Video' : '' }}
                        · {{ $topic->is_published ? 'Published' : 'Draft' }}
                    </p>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('teacher.topics.show', $topic) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">View</a>
                    <a href="{{ route('teacher.topics.edit', $topic) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #2C3E50;">Edit</a>
                    <form method="POST" action="{{ route('teacher.topics.destroy', $topic) }}" style="display: inline;" onsubmit="return confirm('Delete this topic? Quizzes and games will be unlinked.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="eco-logout-btn" style="padding: 0.5rem 1rem;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="eco-card" style="text-align: center; color: #666;">
                <p>No topics yet. Create a topic, then assign quizzes and mini games to it from their edit pages.</p>
                <a href="{{ route('teacher.topics.create') }}" class="eco-btn" style="margin-top: 1rem;">Create Topic</a>
            </div>
        @endforelse
    </div>

    @if ($topics->hasPages())
        <div style="margin-top: 1.5rem;">{{ $topics->links() }}</div>
    @endif
@endsection
