@extends('layouts.enviroedu')

@section('title', 'Water Saver Challenge - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Water Saver Challenge</h1>
    </header>

    <div class="text-center text-4xl mb-4">💧🚿</div>
    <p class="text-center text-slate-600 mb-6">Choose the action that saves water.</p>

    <div class="space-y-4 mb-6">
        <button type="button" class="option-card w-full flex items-center gap-4 p-5 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 transition text-left" onclick="selectOption(this, true)">
            <span class="text-2xl">✓</span>
            <span class="font-medium text-slate-800">Turn off the tap while brushing</span>
        </button>
        <button type="button" class="option-card w-full flex items-center gap-4 p-5 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 transition text-left" onclick="selectOption(this, false)">
            <span class="text-2xl">✗</span>
            <span class="font-medium text-slate-800">Leave the tap running while brushing</span>
        </button>
    </div>

    <div id="feedback" class="hidden text-center p-4 rounded-xl mb-4"></div>
    <button type="button" onclick="nextQuestion()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition">Next Challenge</button>
@endsection

@push('scripts')
<script>
let answered = false;
function selectOption(card, isCorrect) {
    if (answered) return;
    answered = true;
    document.querySelectorAll('.option-card').forEach(c => { c.style.pointerEvents = 'none'; });
    const feedbackDiv = document.getElementById('feedback');
    if (isCorrect) {
        card.classList.add('!border-green-500', '!bg-green-50');
        feedbackDiv.className = 'show correct text-center p-4 rounded-xl mb-4 bg-green-50 text-green-800';
        feedbackDiv.innerHTML = '🎉 Excellent! You saved water! +10 Points';
    } else {
        card.classList.add('!border-red-500', '!bg-red-50');
        document.querySelectorAll('.option-card').forEach(c => { if (c.onclick && c.onclick.toString().includes('true')) c.classList.add('!border-green-500', '!bg-green-50'); });
        feedbackDiv.className = 'show incorrect text-center p-4 rounded-xl mb-4 bg-red-50 text-red-800';
        feedbackDiv.innerHTML = '💧 Not quite! Turning off the tap saves water.';
    }
}
function nextQuestion() { window.location.href = "{{ route('games.hub') }}"; }
</script>
@endpush
