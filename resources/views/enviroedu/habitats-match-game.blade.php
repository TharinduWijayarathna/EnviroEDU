@extends('layouts.enviroedu')

@section('title', 'Habitats Match Game - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Habitats Match Game</h1>
    </header>

    <p class="text-center text-slate-600 mb-6">Match the animals to their correct habitats!</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="font-semibold text-slate-800 mb-2">Jungle</div>
            <div class="text-2xl mb-2">🌴🌿</div>
            <div class="drop-zone habitat-animals min-h-[80px] border-2 border-dashed border-slate-300 rounded-lg p-2" id="jungle" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)"></div>
        </div>
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
            <div class="font-semibold text-slate-800 mb-2">🎵 Farm</div>
            <div class="text-2xl mb-2">🌾🏡</div>
            <div class="drop-zone habitat-animals min-h-[80px] border-2 border-dashed border-amber-300 rounded-lg p-2" id="farm" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)"></div>
        </div>
        <div class="p-4 bg-cyan-50 border border-cyan-200 rounded-xl">
            <div class="font-semibold text-slate-800 mb-2">Ocean</div>
            <div class="text-2xl mb-2">🌊🐚</div>
            <div class="drop-zone habitat-animals min-h-[80px] border-2 border-dashed border-cyan-300 rounded-lg p-2" id="ocean" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)"></div>
        </div>
    </div>

    <p class="text-sm font-semibold text-slate-700 mb-2">Drag animals to habitats:</p>
    <div class="animals-grid flex flex-wrap gap-3 mb-6">
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true" ondragstart="drag(event)" data-habitat="jungle">🐘</div>
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true" ondragstart="drag(event)" data-habitat="farm">🐄</div>
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true" ondragstart="drag(event)" data-habitat="ocean">🐢</div>
        <div class="animal-item w-14 h-14 flex items-center justify-center text-2xl bg-white border-2 border-slate-200 rounded-xl cursor-grab" draggable="true" ondragstart="drag(event)" data-habitat="jungle">🦜</div>
    </div>

    <div class="flex justify-center gap-4 py-3 bg-slate-50 rounded-xl text-slate-700 font-semibold">Points: 0 | Level 3</div>
@endsection

@push('scripts')
<script>
function drag(e){ e.dataTransfer.setData('text', e.target.outerHTML); e.dataTransfer.setData('habitat', e.target.dataset.habitat); e.target.classList.add('dragging'); setTimeout(() => { const d = document.querySelector('.dragging'); if(d && d.parentElement && d.parentElement.classList.contains('animals-grid')) d.style.display = 'none'; }, 0); }
function allowDrop(e){ e.preventDefault(); e.currentTarget.classList.add('drag-over'); }
function dragLeave(e){ if(!e.currentTarget.contains(e.relatedTarget)) e.currentTarget.classList.remove('drag-over'); }
function drop(e){
    e.preventDefault(); e.currentTarget.classList.remove('drag-over');
    const temp = document.createElement('div'); temp.innerHTML = e.dataTransfer.getData('text');
    const el = temp.firstChild; el.classList.remove('dragging'); el.style.display = '';
    const orig = document.querySelector('.dragging'); if(orig) orig.remove();
    e.currentTarget.appendChild(el);
}
</script>
@endpush
