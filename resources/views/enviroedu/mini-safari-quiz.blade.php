@extends('layouts.enviroedu')

@section('title', 'Mini Safari Quiz - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Mini Safari Quiz</h1>
    </header>

    <p class="text-center text-xl font-semibold text-slate-800 mb-6">What is a tiger?</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="text-center p-6 bg-slate-50 rounded-xl">
            <div class="flex justify-center gap-4 mb-4 text-4xl">🐅 🏙️</div>
            <div class="text-5xl">🐅</div>
        </div>
        <div class="space-y-3">
            <button type="button" class="answer-btn w-full py-4 px-6 bg-slate-600 hover:bg-slate-500 text-white font-medium rounded-xl transition text-left" onclick="selectAnswer(this, 'wild')">A. Wild Animal</button>
            <button type="button" class="answer-btn w-full py-4 px-6 bg-slate-600 hover:bg-slate-500 text-white font-medium rounded-xl transition text-left" onclick="selectAnswer(this, 'pet')">B. Pet</button>
            <button type="button" class="answer-btn w-full py-4 px-6 bg-slate-600 hover:bg-slate-500 text-white font-medium rounded-xl transition text-left" onclick="selectAnswer(this, 'bird')">C. Bird</button>
        </div>
    </div>

    <div id="feedback" class="feedback-section hidden text-center p-4 rounded-xl mb-4"></div>
    <button type="button" onclick="nextQuestion()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition">Next Question</button>
@endsection

@push('scripts')
<script>
let answered = false;
function selectAnswer(button, answer) {
    if (answered) return;
    answered = true;
    document.querySelectorAll('.answer-btn').forEach(btn => { btn.style.pointerEvents = 'none'; });
    const feedbackDiv = document.getElementById('feedback');
    if (answer === 'wild') {
        button.classList.add('!bg-green-600');
        feedbackDiv.className = 'feedback-section show correct text-center p-4 rounded-xl mb-4 bg-green-50 text-green-800';
        feedbackDiv.textContent = '✓ Correct! +10 Points';
    } else {
        button.classList.add('!bg-red-600');
        document.querySelectorAll('.answer-btn').forEach(btn => { if (btn.textContent.includes('Wild Animal')) btn.classList.add('!bg-green-600'); });
        feedbackDiv.className = 'feedback-section show incorrect text-center p-4 rounded-xl mb-4 bg-red-50 text-red-800';
        feedbackDiv.textContent = '✗ Incorrect. The correct answer is: Wild Animal';
    }
}
function nextQuestion() { window.location.href = "{{ route('games.hub') }}"; }
</script>
@endpush
