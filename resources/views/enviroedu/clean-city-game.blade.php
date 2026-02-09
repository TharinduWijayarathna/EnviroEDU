@extends('layouts.enviroedu')

@section('title', 'Clean the City Game - EnviroEdu')

@section('backUrl', route('games.hub'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Clean the City Game</h1>
    </header>

    <p class="text-center text-slate-600 mb-4">Click on the garbage to clean the city! <span class="text-xl">🗑️</span></p>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-sm text-slate-500">Items Collected</p>
            <p id="collected" class="text-xl font-bold text-emerald-600">0 / 12</p>
        </div>
        <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-sm text-slate-500">Points</p>
            <p id="points" class="text-xl font-bold text-slate-800">0</p>
        </div>
    </div>

    <div class="h-4 bg-slate-200 rounded-full overflow-hidden mb-6">
        <div id="progress" class="h-full bg-emerald-600 rounded-full flex items-center justify-center text-white text-sm font-semibold transition-all duration-300" style="width: 0%">0%</div>
    </div>

    <div class="flex flex-wrap justify-center gap-4 mb-4 text-4xl">
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🗑️</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">📦</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🥤</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🍔</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🧃</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🛒</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">📄</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🧻</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🥡</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🧴</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🎒</div>
        <div class="garbage-item cursor-pointer hover:scale-110 transition-transform" onclick="collectGarbage(this)">🔋</div>
    </div>

    <div id="completeMessage" class="hidden text-center p-5 bg-green-50 text-green-800 rounded-xl font-semibold">
        🎉 Amazing! You cleaned the entire city! +100 Bonus Points!
    </div>
@endsection

@push('scripts')
<script>
let collected = 0;
const totalItems = 12;
let points = 0;
function collectGarbage(element) {
    if (element.classList.contains('collected')) return;
    element.classList.add('collected');
    collected++;
    points += 10;
    document.getElementById('collected').textContent = collected + ' / ' + totalItems;
    document.getElementById('points').textContent = points;
    const pct = Math.round((collected / totalItems) * 100);
    const progressBar = document.getElementById('progress');
    progressBar.style.width = pct + '%';
    progressBar.textContent = pct + '%';
    setTimeout(() => { element.style.display = 'none'; }, 500);
    if (collected === totalItems) {
        points += 100;
        document.getElementById('points').textContent = points;
        document.getElementById('completeMessage').classList.remove('hidden');
    }
}
</script>
@endpush
