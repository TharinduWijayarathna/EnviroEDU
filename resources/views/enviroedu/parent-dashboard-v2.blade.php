@extends('layouts.enviroedu')

@section('title', 'Parent Dashboard - EnviroEdu')

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Parent Dashboard</h1>
    </header>

    <section class="mb-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-200">Child's Progress Overview</h2>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
            <h3 class="text-base font-semibold text-slate-800 mb-4">Recent Activity</h3>
            <ul class="space-y-3">
                <li class="flex items-center gap-3 py-2 border-b border-slate-200 last:border-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-700">Completed: Water Quiz</span>
                </li>
                <li class="flex items-center gap-3 py-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-700">Watched: Plant Parts Video</span>
                </li>
            </ul>
        </div>
    </section>

    <section class="mb-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-200">Performance Report</h2>
        <button type="button" onclick="document.getElementById('performanceModal').classList.remove('hidden')" class="w-full text-left bg-slate-50 border-2 border-slate-200 rounded-xl p-6 hover:border-emerald-500 hover:shadow-md transition">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <span class="font-semibold text-slate-800">Score</span>
                <span class="text-2xl font-bold text-emerald-600">85%</span>
            </div>
            <p class="text-sm font-semibold text-slate-700 mb-3">Badges Earned</p>
            <div class="grid grid-cols-3 gap-3">
                <div class="flex flex-col items-center p-3 bg-white rounded-lg border border-slate-200">
                    <span class="text-2xl mb-1">♻️</span>
                    <span class="text-xs font-semibold text-slate-700">Eco Hero</span>
                </div>
                <div class="flex flex-col items-center p-3 bg-white rounded-lg border border-slate-200">
                    <span class="text-2xl mb-1">💧</span>
                    <span class="text-xs font-semibold text-slate-700">Water Saver</span>
                </div>
                <div class="flex flex-col items-center p-3 bg-white rounded-lg border border-slate-200">
                    <span class="text-2xl mb-1">🐾</span>
                    <span class="text-xs font-semibold text-slate-700">Animal Friend</span>
                </div>
            </div>
        </button>
    </section>

    <div class="text-center">
        <a href="{{ route('logout.confirmation') }}" class="inline-block px-8 py-3.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold rounded-xl transition">Close</a>
    </div>

    <div id="performanceModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl" onclick="event.stopPropagation()">
            <h2 class="text-xl font-bold text-slate-800 text-center mb-6">Performance Report</h2>
            <div class="w-36 h-36 mx-auto mb-6 rounded-full bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center">
                <div class="w-28 h-28 rounded-full bg-white flex flex-col items-center justify-center">
                    <span class="text-3xl mb-0.5">😊</span>
                    <span class="text-sm font-semibold text-slate-700">60% Progress</span>
                </div>
            </div>
            <p class="text-center text-lg font-semibold text-slate-800 mb-6">Score: 85%</p>
            <div class="flex justify-center gap-4 mb-6">
                <div class="text-center"><span class="text-2xl block">♻️</span><span class="text-xs font-medium text-slate-600">Eco Hero</span></div>
                <div class="text-center"><span class="text-2xl block">💧</span><span class="text-xs font-medium text-slate-600">Water</span></div>
                <div class="text-center"><span class="text-2xl block">🐾</span><span class="text-xs font-medium text-slate-600">Animal</span></div>
                <div class="text-center"><span class="text-2xl block">🌱</span><span class="text-xs font-medium text-slate-600">Friend</span></div>
            </div>
            <button type="button" onclick="document.getElementById('performanceModal').classList.add('hidden')" class="w-full py-3 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold rounded-xl transition">Close</button>
        </div>
    </div>
@endsection
