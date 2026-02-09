@extends('layouts.enviroedu')

@section('title', 'Plant Builder Game - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Plant Builder Game</h1>
    </header>

    <p class="text-center text-slate-600 mb-6">Match the animals to their correct habitats!</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="text-3xl mb-2">🌿</div>
            <div class="font-semibold text-slate-800 mb-2">Pond</div>
            <div class="drop-zone min-h-[80px] border-2 border-dashed border-slate-300 rounded-lg p-2" id="pond"></div>
        </div>
        <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="text-3xl mb-2">🌳</div>
            <div class="font-semibold text-slate-800 mb-2">Forest</div>
            <div class="drop-zone min-h-[80px] border-2 border-dashed border-slate-300 rounded-lg p-2" id="forest"></div>
        </div>
        <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="text-3xl mb-2">🏞️</div>
            <div class="font-semibold text-slate-800 mb-2">Desert</div>
            <div class="drop-zone min-h-[80px] border-2 border-dashed border-slate-300 rounded-lg p-2" id="desert"></div>
        </div>
    </div>

    <p class="text-sm font-semibold text-slate-700 mb-2">Animals: 🐘 🦆 💎 💧 🏖️ 🪨</p>
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true">🐘</div>
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true">🦆</div>
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true">💎</div>
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true">💧</div>
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true">🏖️</div>
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true">🪨</div>
    </div>

    <button type="button" onclick="checkAnswers()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition mb-4">Submit</button>
    <div class="flex justify-center gap-4 py-3 bg-slate-50 rounded-xl text-slate-700 font-semibold">
        <span>💧 10 Points</span>
        <span>|</span>
        <span>Level 2</span>
    </div>
@endsection

@push('scripts')
<script>
const animals = document.querySelectorAll('.animal-item');
const dropZones = document.querySelectorAll('.drop-zone');
animals.forEach(animal => { animal.addEventListener('dragstart', dragStart); });
dropZones.forEach(zone => { zone.addEventListener('dragover', dragOver); zone.addEventListener('drop', drop); });
function dragStart(e) { e.dataTransfer.setData('text/plain', e.target.textContent); }
function dragOver(e) { e.preventDefault(); }
function drop(e) {
    e.preventDefault();
    const data = e.dataTransfer.getData('text/plain');
    const animal = Array.from(animals).find(a => a.textContent.trim() === data);
    if (animal) e.target.appendChild(animal.cloneNode(true));
}
function checkAnswers() {
    alert('Great effort! Your habitat matches have been recorded.');
}
</script>
@endpush
