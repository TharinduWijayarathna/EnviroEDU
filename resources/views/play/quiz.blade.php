@extends('layouts.app')

@section('title', $quiz->title)

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .play-container { max-width: 700px; margin: 0 auto; padding: 2rem; }
        .play-question { background: #fff; border: 2px solid var(--eco-primary); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .play-option { background: #fff; border: 2px solid var(--eco-secondary); border-radius: 12px; padding: 0.8rem 1rem; margin-bottom: 0.5rem; cursor: pointer; transition: all 0.2s; }
        .play-option:hover { background: var(--eco-secondary); }
        .play-option.correct { background: var(--eco-green); border-color: var(--eco-green); }
        .play-option.incorrect { background: var(--eco-accent); border-color: var(--eco-accent); }
        .play-result { text-align: center; padding: 2rem; }
    </style>
@endpush

@section('content')
    <div style="min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ auth()->check() ? route('dashboard.student') : route('home') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <nav class="eco-dashboard-nav">
                <span style="font-weight: 600;">{{ $quiz->title }}</span>
            </nav>
        </header>
        <div class="play-container">
            <div id="quiz-root">
                <div id="quiz-questions">
                    @foreach ($quiz->questions as $i => $q)
                        <div class="play-question" data-question-index="{{ $i }}" style="display: {{ $i === 0 ? 'block' : 'none' }};">
                            <p style="font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem;">{{ $i + 1 }}. {{ $q->question_text }}</p>
                            <div class="options">
                                @foreach ($q->options as $oi => $opt)
                                    <div class="play-option" data-question="{{ $i }}" data-option-index="{{ $oi }}" data-correct="{{ $opt->is_correct ? '1' : '0' }}">
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
                    <a href="{{ auth()->check() ? route('dashboard.student') : route('home') }}" class="eco-btn" style="margin-top: 1rem;">Back</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        const questions = document.querySelectorAll('.play-question[data-question-index]');
        const resultEl = document.getElementById('quiz-result');
        const scoreEl = document.getElementById('quiz-score');
        let currentIndex = 0;
        let score = 0;

        function showQuestion(index) {
            questions.forEach((q, i) => { q.style.display = i === index ? 'block' : 'none'; });
        }

        function showResult() {
            document.getElementById('quiz-questions').style.display = 'none';
            resultEl.style.display = 'block';
            scoreEl.textContent = score;
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
