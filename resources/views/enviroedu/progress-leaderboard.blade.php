@extends('layouts.enviroedu')

@section('title', 'Progress & Leaderboard - EnviroEdu')

@section('backUrl', route('student.dashboard'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Progress & Leaderboard</h1>
    </header>

    <section class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-800">My Progress</h2>
            <span class="text-2xl">📊</span>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4">
            <div class="flex justify-between items-center py-2 border-b border-slate-200 last:border-0">
                <span class="font-medium text-slate-700">Your Progress</span>
                <span class="text-emerald-600 font-semibold">{{ $progressPercent ?? 0 }}%</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="font-medium text-slate-700">Total Points</span>
                <span class="text-emerald-600 font-semibold">{{ $totalPoints ?? 0 }} pts</span>
            </div>
        </div>
    </section>

    <section>
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Weekly Leaderboard</h2>
        <div class="space-y-3">
            @forelse($leaderboard ?? collect() as $row)
            <div class="flex justify-between items-center p-4 rounded-xl border-2 {{ ($row['rank'] ?? 0) === 1 ? 'bg-amber-50 border-amber-300' : (isset($user) && $row['user']->id === $user->id ? 'bg-emerald-50 border-emerald-300' : 'bg-white border-slate-200') }}">
                <div class="flex items-center gap-3">
                    <span class="text-lg font-bold {{ ($row['rank'] ?? 0) === 1 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $row['rank'] }}.</span>
                    <span class="font-semibold text-slate-800">{{ $row['user']->name }}</span>
                </div>
                <span class="font-bold text-emerald-600">{{ $row['points'] }} pts</span>
            </div>
            @empty
            <p class="text-slate-500 py-6">No leaderboard data yet.</p>
            @endforelse
        </div>
    </section>

    <div class="mt-8 text-center">
        <a href="{{ route('student.dashboard') }}" class="inline-block px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/25 transition">Back to Dashboard</a>
    </div>
@endsection
