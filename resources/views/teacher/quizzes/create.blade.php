@extends('layouts.teacher')

@section('title', 'Create Quiz')

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Create Quiz</h1>

    <form method="POST" action="{{ route('teacher.quizzes.store') }}" id="quiz-form" style="max-width: 700px;">
        @csrf
        <div style="margin-bottom: 1rem;">
            <label for="title" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Title</label>
            <input id="title" type="text" name="title" class="eco-input" value="{{ old('title') }}" required>
            @error('title')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="2">{{ old('description') }}</textarea>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="grade_level" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Grade level (optional)</label>
            <input id="grade_level" type="number" name="grade_level" class="eco-input" value="{{ old('grade_level') }}" min="1" max="12" placeholder="e.g. 4">
        </div>
        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                <span>Publish (visible to students)</span>
            </label>
        </div>

        <h2 style="font-size: 1.25rem; margin-bottom: 0.75rem;">Questions</h2>
        <div id="questions-container"></div>
        <button type="button" id="add-question" class="eco-btn" style="margin-top: 1rem; background: #2C3E50;">+ Add Question</button>

        <div style="margin-top: 2rem;">
            <button type="submit" class="eco-btn">Save Quiz</button>
            <a href="{{ route('teacher.quizzes.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
        </div>
    </form>

    <script>
        let questionIndex = 0;
        function addQuestion() {
            const container = document.getElementById('questions-container');
            const idx = questionIndex++;
            const block = document.createElement('div');
            block.className = 'quiz-question-block eco-card';
            block.style.marginBottom = '1.5rem';
            block.style.padding = '1rem';
            block.dataset.index = idx;
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
        }
        document.getElementById('add-question')?.addEventListener('click', addQuestion);
        addQuestion();
    </script>
@endsection
