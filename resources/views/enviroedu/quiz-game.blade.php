@extends('layouts.enviroedu')

@section('title', 'Quiz Game - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Quiz Game</h1>
    </header>

    <div class="question-section mb-8">
        <p class="question text-center text-xl font-semibold text-slate-800 mb-8">Which part of the plant takes in water?</p>
        <div class="options flex flex-col gap-4">
            <button type="button" class="option text-left px-6 py-4 rounded-xl font-medium text-white bg-slate-600 hover:bg-slate-500 border-2 border-transparent hover:border-emerald-500 transition" data-answer="correct" onclick="selectOption(this)">A. Roots</button>
            <button type="button" class="option text-left px-6 py-4 rounded-xl font-medium text-white bg-slate-600 hover:bg-slate-500 border-2 border-transparent hover:border-emerald-500 transition" data-answer="incorrect" onclick="selectOption(this)">B. Stem</button>
            <button type="button" class="option text-left px-6 py-4 rounded-xl font-medium text-white bg-slate-600 hover:bg-slate-500 border-2 border-transparent hover:border-emerald-500 transition" data-answer="incorrect" onclick="selectOption(this)">C. Leaves</button>
        </div>
    </div>

    <div id="feedback" class="hidden text-center p-5 rounded-xl mb-6">
        <h3 id="feedbackTitle" class="text-lg font-semibold mb-2"></h3>
        <p id="feedbackText"></p>
    </div>

    <div class="text-center p-5 bg-slate-50 rounded-xl mb-6">
        <h3 class="text-slate-800 font-semibold mb-2">Current Score</h3>
        <div id="score" class="text-2xl font-bold text-emerald-600">0 pts</div>
    </div>

    <button type="button" class="w-full max-w-xs mx-auto block py-3.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition" onclick="nextQuestion()">Next Question</button>
@endsection

@push('scripts')
<script>
let currentScore = 0;
let answered = false;
const progressLeaderboardUrl = @json(route('leaderboard.progress'));
function selectOption(element) {
    if (answered) return;
    answered = true;
    const options = document.querySelectorAll('.option');
    const feedback = document.getElementById('feedback');
    const feedbackTitle = document.getElementById('feedbackTitle');
    const feedbackText = document.getElementById('feedbackText');
    options.forEach(opt => {
        opt.style.pointerEvents = 'none';
        if (opt.dataset.answer === 'correct') {
            opt.classList.remove('bg-slate-600', 'bg-slate-500');
            opt.classList.add('bg-green-600');
        }
    });
    if (element.dataset.answer === 'correct') {
        element.classList.remove('bg-slate-600', 'bg-slate-500');
        element.classList.add('bg-green-600');
        currentScore += 10;
        document.getElementById('score').textContent = currentScore + ' pts';
        feedbackTitle.textContent = 'Correct! +10 Points';
        feedbackText.textContent = 'Great job! Roots absorb water and nutrients from the soil.';
        feedback.className = 'block text-center p-5 rounded-xl mb-6 bg-green-50 text-green-800';
    } else {
        element.classList.remove('bg-slate-600', 'bg-slate-500');
        element.classList.add('bg-red-600');
        feedbackTitle.textContent = 'Incorrect';
        feedbackText.textContent = 'The correct answer is A. Roots. They absorb water from the soil.';
        feedback.className = 'block text-center p-5 rounded-xl mb-6 bg-red-50 text-red-800';
    }
}
function nextQuestion() {
    window.location.href = progressLeaderboardUrl;
}
</script>
@endpush
