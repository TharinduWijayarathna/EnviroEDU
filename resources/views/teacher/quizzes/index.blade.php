@extends('layouts.teacher')

@section('title', 'My Quizzes')

@section('teacher')
        <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">My Quizzes</h1>
        <p style="margin-bottom: 1.5rem; color: #555;">Create and manage quizzes for your students.</p>

        <a href="{{ route('teacher.quizzes.create') }}" class="eco-btn" style="margin-bottom: 1.5rem;">+ New Quiz</a>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse ($quizzes as $quiz)
                <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $quiz->title }}</h3>
                        <p style="color: #666; font-size: 0.9rem;">{{ $quiz->questions_count }} questions · {{ $quiz->is_published ? 'Published' : 'Draft' }}</p>
                    </div>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <a href="{{ route('play.quiz', $quiz) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;" target="_blank">Preview</a>
                        <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="eco-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #2C3E50;">Edit</a>
                        <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}" style="display: inline;" onsubmit="return confirm('Delete this quiz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="eco-logout-btn" style="padding: 0.5rem 1rem;">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="eco-card" style="text-align: center; color: #666;">
                    <p>No quizzes yet. Create your first quiz!</p>
                    <a href="{{ route('teacher.quizzes.create') }}" class="eco-btn" style="margin-top: 1rem;">Create Quiz</a>
                </div>
            @endforelse
        </div>

        @if ($quizzes->hasPages())
            <div style="margin-top: 1.5rem;">{{ $quizzes->links() }}</div>
        @endif
@endsection
