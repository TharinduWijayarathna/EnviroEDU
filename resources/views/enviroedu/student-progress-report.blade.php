@extends('layouts.enviroedu')

@section('title', 'Student Progress Report - EnviroEdu')

@section('backUrl', auth()->user()->role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Student Progress Report</h1>
    </header>

    @if(!empty($teacherSelecting) && $teacherSelecting)
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Select a student</h2>
        <ul class="space-y-2">
            @foreach($students ?? [] as $s)
            <li><a href="{{ route('student.progress_report', ['student' => $s->id]) }}" class="text-emerald-600 hover:text-emerald-700 font-medium">{{ $s->name }}</a></li>
            @endforeach
        </ul>
    </div>
    @else
    <div class="flex items-center gap-5 mb-8">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center text-3xl text-white">👤</div>
        <div>
            <p class="text-xl font-semibold text-slate-800">{{ $student->name ?? 'Student' }}</p>
            <p class="text-emerald-600 font-medium">Total Points: {{ $totalPoints ?? 0 }} pts</p>
        </div>
    </div>

    <div class="flex gap-2 border-b border-slate-200 mb-6" role="tablist">
        <button type="button" data-tab="summary" class="tab-btn px-5 py-3 font-medium border-b-2 -mb-px transition border-emerald-600 text-emerald-600">Summary</button>
        <button type="button" data-tab="badges" class="tab-btn px-5 py-3 font-medium text-slate-500 hover:text-emerald-600 border-b-2 border-transparent -mb-px transition">Badges</button>
        <button type="button" data-tab="games" class="tab-btn px-5 py-3 font-medium text-slate-500 hover:text-emerald-600 border-b-2 border-transparent -mb-px transition">Games & Quizzes</button>
    </div>

    <div id="summary" class="tab-panel" role="tabpanel">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4">
            <div class="flex justify-between items-center py-3 border-b border-slate-200 last:border-0">
                <span class="font-medium text-slate-700">Lessons Completed</span>
                <span class="text-emerald-600 font-semibold">{{ $lessonsCompleted ?? 0 }} / {{ $totalLessons ?? 0 }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-slate-200 last:border-0">
                <span class="font-medium text-slate-700">Average Score</span>
                <span class="text-emerald-600 font-semibold">{{ $averageScore !== null ? $averageScore . '%' : '—' }}</span>
            </div>
            <div class="flex justify-between items-center py-3">
                <span class="font-medium text-slate-700">Weekly Rank</span>
                <span class="text-emerald-600 font-semibold">{{ $weeklyRank ? '#' . $weeklyRank : '—' }}</span>
            </div>
        </div>
        <div class="mt-6">
            <a href="{{ route('leaderboard.achievements') }}" class="inline-block w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-center shadow-lg shadow-emerald-600/25 transition">Class Leaderboard</a>
        </div>
    </div>

    <div id="badges" class="tab-panel hidden" role="tabpanel">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @forelse($achievements ?? collect() as $achievement)
            <div class="flex flex-col items-center p-5 bg-slate-50 border border-slate-200 rounded-xl">
                <span class="text-3xl mb-2">{{ $achievement->icon ?? '🏅' }}</span>
                <span class="text-sm font-semibold text-slate-700">{{ $achievement->name }}</span>
            </div>
            @empty
            <p class="col-span-full text-slate-500 text-center py-6">No badges earned yet.</p>
            @endforelse
        </div>
    </div>

    <div id="games" class="tab-panel hidden" role="tabpanel">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-3">
            @forelse($gameScores ?? [] as $game)
            <div class="flex justify-between items-center py-3 px-4 bg-white rounded-lg border border-slate-200">
                <span class="font-medium text-slate-800">{{ $game['name'] }}</span>
                <span class="text-emerald-600 font-semibold">{{ $game['score'] }}%</span>
            </div>
            @empty
            <p class="text-slate-500 py-4">No games played yet.</p>
            @endforelse
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-emerald-600', 'text-emerald-600');
            b.classList.add('border-transparent', 'text-slate-500');
        });
        this.classList.remove('border-transparent', 'text-slate-500');
        this.classList.add('border-emerald-600', 'text-emerald-600');
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById(tab).classList.remove('hidden');
    });
});
</script>
@endpush
