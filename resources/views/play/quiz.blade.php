@extends('layouts.student')

@section('title', $quiz->title)

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .play-container { max-width: 700px; margin: 0 auto; width: 100%; }
        .play-question {
            background: linear-gradient(180deg, #fffef5 0%, #fffde7 100%);
            border: 2px solid #ffc107;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(255, 193, 7, 0.15);
        }
        .play-option {
            background: #f5f5f5;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .play-option:hover { border-color: #ffc107; background: #fffde7; }
        .play-option.correct { background: #fff59d; border-color: #ffc107; }
        .play-option.incorrect { background: #ffcdd2; border-color: var(--eco-accent); }
        .play-result { text-align: center; padding: 2rem; }
    </style>
@endpush

@section('student-main')
    <div class="play-container">
        <div id="quiz-root">
            <div id="quiz-questions">
                @foreach ($quiz->questions as $i => $q)
                    <div class="play-question" data-question-index="{{ $i }}" data-question-id="{{ $q->id }}" style="display: {{ $i === 0 ? 'block' : 'none' }};">
                        <p style="font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem;">{{ $i + 1 }}. {{ $q->question_text }}</p>
                        <div class="options">
                            @foreach ($q->options as $oi => $opt)
                                <div class="play-option" data-question="{{ $i }}" data-question-id="{{ $q->id }}" data-option-index="{{ $oi }}" data-correct="{{ $opt->is_correct ? '1' : '0' }}">
                                    {{ $opt->option_text }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div id="quiz-result" style="display: none;" class="play-result play-question">
                <h2 style="font-family: 'Bubblegum Sans', cursive; color: var(--eco-primary); margin-bottom: 1rem;">Quiz complete!</h2>
                <p style="font-size: 1.5rem;">Score: <strong id="quiz-score">0</strong> / {{ $quiz->questions->count() }}</p>
                <a href="{{ route('dashboard.student') }}" class="eco-btn" style="margin-top: 1rem;">Back to Dashboard</a>
            </div>
        </div>
    </div>
    <script>
        const questions = document.querySelectorAll('.play-question[data-question-index]');
        const resultEl = document.getElementById('quiz-result');
        const scoreEl = document.getElementById('quiz-score');
        const quizId = {{ $quiz->id }};
        const totalQuestions = {{ $quiz->questions->count() }};
        let currentIndex = 0;
        let score = 0;
        const answers = [];

        function showQuestion(index) {
            questions.forEach((q, i) => { q.style.display = i === index ? 'block' : 'none'; });
        }

        function buildAnswers() {
            questions.forEach((q) => {
                const selected = q.querySelector('.play-option.correct, .play-option.incorrect');
                if (selected) {
                    answers.push({
                        question_id: parseInt(selected.dataset.questionId, 10),
                        option_index: parseInt(selected.dataset.optionIndex, 10),
                        correct: selected.dataset.correct === '1'
                    });
                }
            });
        }

        function showResult() {
            document.getElementById('quiz-questions').style.display = 'none';
            resultEl.style.display = 'block';
            scoreEl.textContent = score;
            buildAnswers();
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                fetch('{{ route("progress.quiz") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify({ quiz_id: quizId, score, total_questions: totalQuestions, answers })
                }).then(r => r.json()).then((data) => {
                    if (data.new_badges && data.new_badges.length > 0 && window.ecoShowBadgeModal) {
                        data.new_badges.forEach((b) => window.ecoShowBadgeModal(b));
                    }
                }).catch(() => {});
            }
        }

        document.querySelectorAll('.play-option').forEach((el) => {
            el.addEventListener('click', function () {
                const questionIndex = parseInt(this.dataset.question, 10);
                const correct = this.dataset.correct === '1';
                const questionBlock = questions[questionIndex];
                questionBlock.querySelectorAll('.play-option').forEach((o) => o.style.pointerEvents = 'none');
                this.classList.add(correct ? 'correct' : 'incorrect');
                if (correct) score++;
                if (questionIndex < questions.length - 1) {
                    setTimeout(() => { currentIndex = questionIndex + 1; showQuestion(currentIndex); }, 800);
                } else {
                    setTimeout(showResult, 800);
                }
            });
        });
    </script>
@endsection
