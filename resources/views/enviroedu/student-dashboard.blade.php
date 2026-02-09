@extends('layouts.enviroedu')

@section('title', 'Student Dashboard - EnviroEdu')

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Student Dashboard</h1>
    </header>

    <h2 class="text-2xl font-semibold text-slate-800 mb-8">Welcome, {{ $user->name ?? 'Student' }}!</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10">
        <a href="{{ route('video.lesson') }}" class="group flex flex-col items-center justify-center p-8 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:border-emerald-500 hover:shadow-lg transition-all duration-200">
            <span class="text-4xl mb-3" aria-hidden="true">▶️</span>
            <h3 class="text-lg font-semibold text-slate-800 group-hover:text-emerald-700">Watch Video Lesson</h3>
        </a>
        <a href="{{ route('games.hub') }}" class="group flex flex-col items-center justify-center p-8 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:border-emerald-500 hover:shadow-lg transition-all duration-200">
            <span class="text-4xl mb-3" aria-hidden="true">🎮</span>
            <h3 class="text-lg font-semibold text-slate-800 group-hover:text-emerald-700">Play Games & Quizzes</h3>
        </a>
    </div>

    <section class="mb-10">
        <h2 class="text-lg font-semibold text-slate-800 mb-3">Your Progress: {{ $progressPercent ?? 0 }}%</h2>
        <div class="h-4 bg-slate-200 rounded-full overflow-hidden">
            <div class="h-full bg-emerald-600 rounded-full flex items-center justify-center text-white text-sm font-semibold transition-all duration-500" style="width: {{ min($progressPercent ?? 0, 100) }}%">
                @if(($progressPercent ?? 0) >= 15){{ $progressPercent ?? 0 }}%@endif
            </div>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Achievements</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @forelse($achievements ?? collect() as $achievement)
            <div class="flex flex-col items-center justify-center p-6 bg-slate-50 border border-slate-200 rounded-xl hover:shadow-md hover:scale-105 transition-all">
                <span class="text-3xl mb-2">{{ $achievement->icon ?? '🏅' }}</span>
                <span class="text-sm font-semibold text-slate-700">{{ $achievement->name }}</span>
            </div>
            @empty
            <p class="col-span-full text-slate-500 text-center py-4">No achievements yet. Play games and complete lessons to earn badges!</p>
            @endforelse
        </div>
    </section>
@endsection
