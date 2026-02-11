@extends('layouts.teacher')

@section('title', $quiz->title)

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $quiz->title }}</h1>
    @if ($quiz->description)
        <p style="color: #555; margin-bottom: 1rem;">{{ $quiz->description }}</p>
    @endif
    <p style="margin-bottom: 1.5rem; font-size: 0.9rem;">{{ $quiz->questions->count() }} questions · {{ $quiz->is_published ? 'Published' : 'Draft' }}</p>

    <a href="{{ route('play.quiz', $quiz) }}" class="eco-btn" style="margin-bottom: 1rem;" target="_blank">Preview Quiz</a>
    <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="eco-btn" style="margin-bottom: 1rem; margin-left: 0.5rem; background: #2C3E50;">Edit</a>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @foreach ($quiz->questions as $i => $q)
            <div class="eco-card" style="padding: 1rem;">
                <p style="font-weight: 700; margin-bottom: 0.5rem;">{{ $i + 1 }}. {{ $q->question_text }}</p>
                <ul style="list-style: none; padding: 0;">
                    @foreach ($q->options as $opt)
                        <li style="padding: 0.25rem 0;">{{ $opt->is_correct ? '✓ ' : '' }}{{ $opt->option_text }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
@endsection
