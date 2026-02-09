@extends('layouts.enviroedu')

@section('title', 'Who Am I? - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Who Am I?</h1>
    </header>

    <div class="flex flex-wrap justify-center gap-3 mb-6">
        <span class="px-4 py-2 bg-slate-100 rounded-full text-slate-700">I can fly</span>
        <span class="px-4 py-2 bg-slate-100 rounded-full text-slate-700">I build a nest</span>
        <span class="px-4 py-2 bg-slate-100 rounded-full text-slate-700">I chirp</span>
    </div>
    <div class="text-center text-5xl mb-8">🦜<br>❓</div>

    <div class="grid grid-cols-2 gap-3 mb-6">
        <button type="button" class="answer-btn flex items-center justify-center gap-2 px-6 py-4 bg-slate-600 hover:bg-slate-500 text-white rounded-xl font-medium transition" onclick="selectAnswer(this, 'car')"><span>❌</span> Car</button>
        <button type="button" class="answer-btn flex items-center justify-center gap-2 px-6 py-4 bg-slate-600 hover:bg-slate-500 text-white rounded-xl font-medium transition" onclick="selectAnswer(this, 'bird')"><span>✓</span> Bird</button>
        <button type="button" class="answer-btn flex items-center justify-center gap-2 px-6 py-4 bg-slate-600 hover:bg-slate-500 text-white rounded-xl font-medium transition" onclick="selectAnswer(this, 'rock')"><span>❌</span> Rock</button>
        <button type="button" class="answer-btn flex items-center justify-center gap-2 px-6 py-4 bg-slate-600 hover:bg-slate-500 text-white rounded-xl font-medium transition" onclick="selectAnswer(this, 'clock')"><span>🕐</span> Clock</button>
    </div>

    <p class="text-center text-slate-500 mb-4">Hint: I live in trees</p>
    <div id="result" class="result-message hidden text-center p-4 rounded-xl mb-4"></div>
    <button type="button" onclick="nextQuestion()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition">Next Question</button>
@endsection

@push('scripts')
<script>
let answered = false;
function selectAnswer(button, answer) {
    if (answered) return;
    answered = true;
    document.querySelectorAll('.answer-btn').forEach(btn => { btn.style.pointerEvents = 'none'; });
    const resultDiv = document.getElementById('result');
    if (answer === 'bird') {
        button.classList.add('!bg-green-600');
        resultDiv.className = 'result-message show correct text-center p-4 rounded-xl mb-4';
        resultDiv.textContent = '🎉 Correct! I am a bird!';
    } else {
        button.classList.add('!bg-red-600');
        document.querySelectorAll('.answer-btn').forEach(btn => { if (btn.textContent.includes('Bird')) btn.classList.add('!bg-green-600'); });
        resultDiv.className = 'result-message show incorrect text-center p-4 rounded-xl mb-4';
        resultDiv.textContent = '❌ Not quite! The correct answer is Bird.';
    }
}
function nextQuestion() {
    window.location.href = "{{ route('games.hub') }}";
}
</script>
@endpush
