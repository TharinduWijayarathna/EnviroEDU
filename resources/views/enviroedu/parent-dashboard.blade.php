@extends('layouts.enviroedu')

@section('title', 'Parent Dashboard - EnviroEdu')

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Parent Dashboard</h1>
    </header>

    @forelse($children ?? collect() as $child)
    <section class="mb-10">
        <h2 class="text-xl font-semibold text-slate-800 mb-4">{{ $child['user']->name }}'s Progress</h2>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
            <div class="flex flex-wrap gap-6 mb-4">
                <div>
                    <span class="text-sm text-slate-500">Progress</span>
                    <p class="text-2xl font-bold text-emerald-600">{{ $child['progressPercent'] ?? 0 }}%</p>
                </div>
                <div>
                    <span class="text-sm text-slate-500">Total Points</span>
                    <p class="text-2xl font-bold text-slate-800">{{ $child['totalPoints'] ?? 0 }} pts</p>
                </div>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-700 mb-3">Badges Earned</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($child['achievements'] ?? [] as $achievement)
                    <div class="flex flex-col items-center p-4 bg-white border border-slate-200 rounded-xl">
                        <span class="text-2xl mb-1">{{ $achievement->icon ?? '🏅' }}</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $achievement->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @empty
    <section>
        <h2 class="text-xl font-semibold text-slate-800 mb-4">Child's Progress Overview</h2>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
            <p class="text-slate-500">No linked student accounts. Link a student account to see progress here.</p>
        </div>
    </section>
    @endforelse

    <div class="mt-8 pt-6 border-t border-slate-200 text-center">
        <a href="{{ route('logout.confirmation') }}" class="inline-block px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/25 transition">
            Logout
        </a>
    </div>
@endsection
