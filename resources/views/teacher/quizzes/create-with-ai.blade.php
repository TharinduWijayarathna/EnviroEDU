@extends('layouts.teacher')

@section('title', 'Create quiz with AI')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.quizzes.index') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Quizzes</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">Create a quiz with AI</h1>
    <p style="color: #555; margin-bottom: 1.5rem;">Describe the environmental topic and AI will generate multiple-choice questions. You can tweak them before publishing.</p>

    <form method="POST" action="{{ route('teacher.quizzes.generate') }}" style="max-width: 600px;">
        @csrf
        <div style="margin-bottom: 1.25rem;">
            <label for="prompt" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">What should the quiz be about?</label>
            <textarea id="prompt" name="prompt" class="eco-input" rows="4" required placeholder="e.g. Recycling and waste sorting for grade 4, or Ocean ecosystems and marine life for grade 5">{{ old('prompt') }}</textarea>
            @error('prompt')
                <span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>
            @enderror
            <p style="font-size: 0.85rem; color: #666; margin-top: 0.35rem;">Keep it to environmental topics: nature, recycling, climate, habitats, conservation, etc.</p>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="topic_id" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Topic (optional)</label>
            <select id="topic_id" name="topic_id" class="eco-input">
                <option value="">None</option>
                @foreach ($topics ?? [] as $t)
                    <option value="{{ $t->id }}" {{ old('topic_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom: 1.25rem;">
            <label for="grade_level" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Grade level (optional)</label>
            <select id="grade_level" name="grade_level" class="eco-input">
                <option value="">Any</option>
                @foreach (config('app.grade_levels', [4, 5]) as $g)
                    <option value="{{ $g }}" {{ old('grade_level') == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="eco-btn">Generate quiz with AI</button>
        <a href="{{ route('teacher.quizzes.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>
@endsection
