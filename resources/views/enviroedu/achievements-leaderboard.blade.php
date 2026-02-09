@extends('layouts.enviroedu')

@section('title', 'Achievements & Leaderboard - EnviroEdu')

@section('backUrl', route('student.dashboard'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Achievements & Leaderboard</h1>
    </header>

    <div class="flex gap-2 border-b border-slate-200 mb-8" role="tablist">
        <button type="button" data-tab="badges" class="ach-tab px-5 py-3 font-medium border-b-2 -mb-px transition border-emerald-600 text-emerald-600">Badges Earned</button>
        <button type="button" data-tab="leaderboard" class="ach-tab px-5 py-3 font-medium text-slate-500 hover:text-emerald-600 border-b-2 border-transparent -mb-px transition">Weekly Leaderboard</button>
    </div>

    <div id="badges" class="ach-panel">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($allAchievements ?? collect() as $achievement)
            <div class="flex flex-col items-center p-6 bg-slate-50 border border-slate-200 rounded-xl hover:shadow-md hover:border-emerald-500 transition">
                <span class="text-4xl mb-2">{{ $achievement->icon ?? '🏅' }}</span>
                <span class="text-sm font-semibold text-slate-700">{{ $achievement->name }}</span>
            </div>
            @empty
            <p class="col-span-full text-slate-500 text-center py-6">No badges defined yet.</p>
            @endforelse
        </div>
    </div>

    <div id="leaderboard" class="ach-panel hidden">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-slate-800">Weekly Leaderboard</h2>
            <span class="text-3xl">🏆</span>
        </div>
        <div class="space-y-3">
            @forelse($leaderboard ?? collect() as $row)
            <div class="flex justify-between items-center p-4 rounded-xl border-2 {{ ($row['rank'] ?? 0) === 1 ? 'bg-amber-50 border-amber-300' : ($row['user']->id === auth()->id() ? 'bg-emerald-50 border-emerald-300' : 'bg-white border-slate-200') }}">
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
    </div>

    <p class="text-center mt-8">
        <a href="{{ route('student.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">← Back to Dashboard</a>
    </p>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.ach-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        document.querySelectorAll('.ach-tab').forEach(b => {
            b.classList.remove('border-emerald-600', 'text-emerald-600');
            b.classList.add('border-transparent', 'text-slate-500');
        });
        this.classList.remove('border-transparent', 'text-slate-500');
        this.classList.add('border-emerald-600', 'text-emerald-600');
        document.querySelectorAll('.ach-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById(tab).classList.remove('hidden');
    });
});
</script>
@endpush
