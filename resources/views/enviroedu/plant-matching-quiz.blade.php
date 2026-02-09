@extends('layouts.enviroedu')

@section('title', 'Plant Matching Quiz - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Plant Matching Quiz</h1>
    </header>

    <p class="text-center text-slate-600 mb-6">Match the plant with its proper use! <span class="text-2xl">🌾📋</span></p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-slate-50 rounded-xl p-4">
            <div class="font-semibold text-slate-800 mb-2">Food</div>
            <div class="drop-zone min-h-[140px] space-y-2 p-2 border-2 border-dashed border-slate-300 rounded-lg" id="food" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
                <div class="match-item flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg cursor-grab" draggable="true" ondragstart="drag(event)" data-category="food"><span>🥔</span> Potato</div>
                <div class="match-item flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg cursor-grab" draggable="true" ondragstart="drag(event)" data-category="food"><span>🍎</span> Apple</div>
                <div class="match-item flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg cursor-grab" draggable="true" ondragstart="drag(event)" data-category="food"><span>🌾</span> Wheat</div>
            </div>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
            <div class="font-semibold text-slate-800 mb-2">Medicine</div>
            <div class="drop-zone min-h-[140px] space-y-2 p-2 border-2 border-dashed border-slate-300 rounded-lg" id="medicine" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
                <div class="match-item flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg cursor-grab" draggable="true" ondragstart="drag(event)" data-category="medicine"><span>🪴</span> Aloe</div>
                <div class="match-item flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg cursor-grab" draggable="true" ondragstart="drag(event)" data-category="medicine"><span>💊</span> Herbs</div>
            </div>
        </div>
        <div class="bg-slate-50 rounded-xl p-4">
            <div class="font-semibold text-slate-800 mb-2">Building</div>
            <div class="drop-zone min-h-[140px] space-y-2 p-2 border-2 border-dashed border-slate-300 rounded-lg" id="building" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
                <div class="match-item flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg cursor-grab" draggable="true" ondragstart="drag(event)" data-category="building"><span>🪵</span> Wood</div>
                <div class="match-item flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg cursor-grab" draggable="true" ondragstart="drag(event)" data-category="building"><span>🏗️</span> Building</div>
            </div>
        </div>
    </div>

    <button type="button" onclick="checkAnswers()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition mb-4">Submit</button>
    <div class="flex justify-center gap-4 py-3 bg-slate-50 rounded-xl text-slate-700 font-semibold">
        <span>+10 Points</span><span>|</span><span>Level 2</span>
    </div>
@endsection

@push('scripts')
<script>
function drag(e){ e.dataTransfer.setData('text', e.target.outerHTML); e.dataTransfer.setData('category', e.target.dataset.category); e.target.classList.add('dragging'); }
function allowDrop(e){ e.preventDefault(); e.currentTarget.classList.add('drag-over'); }
function dragLeave(e){ e.currentTarget.classList.remove('drag-over'); }
function drop(e){
    e.preventDefault(); e.currentTarget.classList.remove('drag-over');
    const data = e.dataTransfer.getData('text');
    const temp = document.createElement('div'); temp.innerHTML = data;
    const el = temp.firstChild; el.classList.remove('dragging');
    const orig = document.querySelector('.dragging'); if(orig) orig.remove();
    e.currentTarget.appendChild(el);
}
function checkAnswers(){ alert('Great job matching the plants to their uses!'); }
</script>
@endpush
