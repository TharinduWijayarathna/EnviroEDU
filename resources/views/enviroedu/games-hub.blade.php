@extends('layouts.enviroedu')

@section('title', 'Games & Quizzes - EnviroEdu')

@section('backUrl', route('student.dashboard'))

@section('content')
    <header class="flex flex-wrap items-center gap-4 mb-8 pb-6 border-b border-slate-200">
        <h1 class="text-2xl font-bold text-slate-800 flex-1 text-center">Games & Quizzes</h1>
    </header>

    <section class="mb-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-200">Grade 4 – Living and Non-Living</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <a href="{{ route('games.living_non_living') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">🌳</span>
                <h3 class="text-sm font-semibold">Living Lab</h3>
            </a>
            <a href="{{ route('games.who_am_i') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">❓</span>
                <h3 class="text-sm font-semibold">Who Am I?</h3>
            </a>
            <a href="{{ route('games.quiz') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">✅</span>
                <h3 class="text-sm font-semibold">Alive Check Quiz</h3>
            </a>
        </div>
    </section>

    <section class="mb-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-200">Grade 4 – Plants Around Us</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <a href="{{ route('games.plant_builder') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">🌱</span>
                <h3 class="text-sm font-semibold">Plant Builder</h3>
            </a>
            <a href="{{ route('games.plant_matching') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">🔗</span>
                <h3 class="text-sm font-semibold">Plant Match</h3>
            </a>
        </div>
    </section>

    <section class="mb-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-200">Grade 4 – Animals and Habitats</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <a href="{{ route('games.habitats_match') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">🏠</span>
                <h3 class="text-sm font-semibold">Habitat Hero</h3>
            </a>
            <a href="{{ route('games.mini_safari') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">🦁</span>
                <h3 class="text-sm font-semibold">Mini Safari Quiz</h3>
            </a>
        </div>
    </section>

    <section class="mb-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-200">Grade 4 – My Environment</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <a href="{{ route('games.clean_city') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">🧹</span>
                <h3 class="text-sm font-semibold">Clean the City</h3>
            </a>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-200">Grade 5 – Water Resources</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <a href="{{ route('games.water_saver') }}" class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all no-underline text-slate-800">
                <span class="text-3xl mb-2">💧</span>
                <h3 class="text-sm font-semibold">Water Saver Challenge</h3>
            </a>
        </div>
    </section>
@endsection
