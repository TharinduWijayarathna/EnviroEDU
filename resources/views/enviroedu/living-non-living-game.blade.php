@extends('layouts.enviroedu')

@section('title', 'Living and Non-Living Things - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Living and Non-Living Things</h1>
    </header>

    <p class="text-center text-slate-600 mb-6">Drag the items into the correct categories!</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="min-h-[200px] p-6 bg-slate-50 border-2 border-slate-200 rounded-xl transition-colors" id="living" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
            <div class="font-semibold text-slate-800 mb-3">Living</div>
            <div class="items-area min-h-[120px] flex flex-wrap gap-2" id="living-items"></div>
        </div>
        <div class="min-h-[200px] p-6 bg-slate-50 border-2 border-slate-200 rounded-xl transition-colors" id="non-living" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
            <div class="font-semibold text-slate-800 mb-3">Non-Living</div>
            <div class="items-area min-h-[120px] flex flex-wrap gap-2" id="non-living-items"></div>
        </div>
    </div>

    <button type="button" onclick="checkAnswers()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition mb-4">Submit</button>
    <div id="result" class="result-message hidden text-center p-4 rounded-xl"></div>
@endsection

@push('scripts')
<script>
const items = [
    { emoji: '🌳', category: 'living', id: 'tree' },
    { emoji: '🐱', category: 'living', id: 'cat' },
    { emoji: '🪨', category: 'non-living', id: 'rock1' },
    { emoji: '🪨', category: 'non-living', id: 'rock2' }
];
function initGame() {
    const livingArea = document.getElementById('living-items');
    const nonLivingArea = document.getElementById('non-living-items');
    items.filter(item => item.category === 'living').forEach(item => { livingArea.appendChild(createItemElement(item)); });
    items.filter(item => item.category === 'non-living').forEach(item => { nonLivingArea.appendChild(createItemElement(item)); });
}
function createItemElement(item) {
    const div = document.createElement('div');
    div.className = 'item cursor-grab active:cursor-grabbing w-16 h-16 flex items-center justify-center text-3xl bg-white border-2 border-slate-200 rounded-xl shadow-sm hover:border-emerald-500';
    div.draggable = true;
    div.textContent = item.emoji;
    div.dataset.id = item.id;
    div.dataset.category = item.category;
    div.ondragstart = drag;
    return div;
}
function drag(e) { e.dataTransfer.setData('text', e.target.dataset.id); e.target.classList.add('dragging'); }
function allowDrop(e) { e.preventDefault(); e.currentTarget.classList.add('drag-over'); }
function dragLeave(e) { e.currentTarget.classList.remove('drag-over'); }
function drop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    const itemId = e.dataTransfer.getData('text');
    const el = document.querySelector('[data-id="' + itemId + '"]');
    if (el) { el.classList.remove('dragging'); e.currentTarget.querySelector('.items-area').appendChild(el); }
}
function checkAnswers() {
    const livingArea = document.getElementById('living-items');
    const nonLivingArea = document.getElementById('non-living-items');
    const resultDiv = document.getElementById('result');
    const livingItems = Array.from(livingArea.children);
    const nonLivingItems = Array.from(nonLivingArea.children);
    let correct = true;
    livingItems.forEach(item => { if (item.dataset.category !== 'living') correct = false; });
    nonLivingItems.forEach(item => { if (item.dataset.category !== 'non-living') correct = false; });
    if (correct && livingItems.length === 2 && nonLivingItems.length === 2) {
        resultDiv.className = 'result-message show correct text-center p-4 rounded-xl';
        resultDiv.textContent = '🎉 Excellent! All items are in the correct categories!';
    } else {
        resultDiv.className = 'result-message show incorrect text-center p-4 rounded-xl';
        resultDiv.textContent = '❌ Not quite right. Try again!';
    }
}
initGame();
</script>
@endpush
