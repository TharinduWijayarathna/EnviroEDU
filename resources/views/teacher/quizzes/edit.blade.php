@extends('layouts.teacher')

@section('title', 'Edit Quiz')

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Edit Quiz</h1>

    <form method="POST" action="{{ route('teacher.quizzes.update', $quiz) }}" id="quiz-form" style="max-width: 700px;">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 1rem;">
            <label for="title" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Title</label>
            <input id="title" type="text" name="title" class="eco-input" value="{{ old('title', $quiz->title) }}" required>
            @error('title')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="topic_id" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Topic (optional)</label>
            <select id="topic_id" name="topic_id" class="eco-input">
                <option value="">None – standalone quiz</option>
                @foreach ($topics ?? [] as $t)
                    <option value="{{ $t->id }}" {{ old('topic_id', $quiz->topic_id) == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="2">{{ old('description', $quiz->description) }}</textarea>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="grade_level" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Grade level (optional)</label>
            <select id="grade_level" name="grade_level" class="eco-input">
                <option value="">Any</option>
                @foreach (config('app.grade_levels', [4, 5]) as $g)
                    <option value="{{ $g }}" {{ old('grade_level', $quiz->grade_level) == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                <span>Publish (visible to students)</span>
            </label>
        </div>

        <h2 style="font-size: 1.25rem; margin-bottom: 0.75rem;">Questions</h2>
        <div id="questions-container">
            @foreach ($quiz->questions as $qIndex => $question)
                <div class="quiz-question-block eco-card" style="margin-bottom: 1.5rem; padding: 1rem;" data-index="{{ $qIndex }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <strong>Question {{ $qIndex + 1 }}</strong>
                        <button type="button" class="remove-question eco-logout-btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Remove</button>
                    </div>
                    <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $question->id }}">
                    <input type="text" name="questions[{{ $qIndex }}][question_text]" class="eco-input" value="{{ old('questions.'.$qIndex.'.question_text', $question->question_text) }}" required style="margin-bottom: 0.75rem;">
                    <input type="hidden" name="questions[{{ $qIndex }}][order]" value="{{ $qIndex }}">
                    <p style="font-size: 0.9rem; margin-bottom: 0.5rem;">Options (check the correct one):</p>
                    @foreach ($question->options as $oIndex => $option)
                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <input type="radio" name="questions[{{ $qIndex }}][correct]" value="{{ $oIndex }}" {{ $option->is_correct ? 'checked' : '' }} required>
                            <input type="hidden" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][id]" value="{{ $option->id }}">
                            <input type="text" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][option_text]" class="eco-input" value="{{ old('questions.'.$qIndex.'.options.'.$oIndex.'.option_text', $option->option_text) }}" required style="flex:1;">
                        </label>
                        <input type="hidden" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][is_correct]" value="{{ $option->is_correct ? '1' : '0' }}" class="is-correct-input">
                        <input type="hidden" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][order]" value="{{ $oIndex }}">
                    @endforeach
                </div>
            @endforeach
        </div>
        <button type="button" id="add-question" class="eco-btn" style="margin-top: 1rem; background: #2C3E50;">+ Add Question</button>

        <div style="margin-top: 2rem;">
            <button type="submit" class="eco-btn">Update Quiz</button>
            <a href="{{ route('teacher.quizzes.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
        </div>
    </form>

    <script>
        let questionIndex = {{ $quiz->questions->count() }};
        document.querySelectorAll('.quiz-question-block').forEach(block => {
            block.querySelector('.remove-question')?.addEventListener('click', () => block.remove());
            block.querySelectorAll('input[type="radio"]').forEach((radio) => {
                radio.addEventListener('change', function () {
                    const val = this.value;
                    block.querySelectorAll('.is-correct-input').forEach((h, i) => { h.value = i === parseInt(val, 10) ? '1' : '0'; });
                });
            });
        });
        document.getElementById('add-question')?.addEventListener('click', function () {
            const container = document.getElementById('questions-container');
            const idx = questionIndex++;
            const block = document.createElement('div');
            block.className = 'quiz-question-block eco-card';
            block.style.marginBottom = '1.5rem';
            block.style.padding = '1rem';
            block.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <strong>Question ${idx + 1}</strong>
                    <button type="button" class="remove-question eco-logout-btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Remove</button>
                </div>
                <input type="text" name="questions[${idx}][question_text]" class="eco-input" placeholder="Question text" required style="margin-bottom: 0.75rem;">
                <input type="hidden" name="questions[${idx}][order]" value="${idx}">
                <p style="font-size: 0.9rem; margin-bottom: 0.5rem;">Options (check the correct one):</p>
                <div class="options-container">
                    <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <input type="radio" name="questions[${idx}][correct]" value="0" required>
                        <input type="text" name="questions[${idx}][options][0][option_text]" class="eco-input" placeholder="Option 1" required style="flex:1;">
                    </label>
                    <input type="hidden" name="questions[${idx}][options][0][is_correct]" value="0" class="is-correct-input">
                    <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <input type="radio" name="questions[${idx}][correct]" value="1">
                        <input type="text" name="questions[${idx}][options][1][option_text]" class="eco-input" placeholder="Option 2" required style="flex:1;">
                    </label>
                    <input type="hidden" name="questions[${idx}][options][1][is_correct]" value="0" class="is-correct-input">
                </div>
            `;
            container.appendChild(block);
            block.querySelector('.remove-question')?.addEventListener('click', () => block.remove());
            block.querySelectorAll('input[type="radio"]').forEach((radio) => {
                radio.addEventListener('change', function () {
                    const val = this.value;
                    block.querySelectorAll('.is-correct-input').forEach((h, i) => { h.value = i === parseInt(val, 10) ? '1' : '0'; });
                });
            });
        });
    </script>
@endsection
