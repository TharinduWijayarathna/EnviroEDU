@extends('layouts.enviroedu')

@section('title', 'Teacher Dashboard - EnviroEdu')

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">EnviroEdu</h1>
        <p class="text-slate-500 mt-1">Engaging Environmental Learning</p>
    </header>

    <h2 class="text-2xl font-semibold text-slate-800 mb-8">Teacher Dashboard@if(isset($user)) – {{ $user->name }}@endif</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
        <a href="{{ route('teacher.upload_video') }}" class="flex flex-col items-center justify-center p-8 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all">
            <span class="text-3xl mb-3">📤</span>
            <h3 class="text-base font-semibold text-slate-800">Upload Video Lesson</h3>
        </a>
        <button type="button" onclick="alert('Feature coming soon!')" class="flex flex-col items-center justify-center p-8 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all cursor-pointer text-left">
            <span class="text-3xl mb-3">🎮</span>
            <h3 class="text-base font-semibold text-slate-800">Assign Games & Quizzes</h3>
        </button>
        <button type="button" onclick="alert('Feature coming soon!')" class="flex flex-col items-center justify-center p-8 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all cursor-pointer text-left">
            <span class="text-3xl mb-3">📋</span>
            <h3 class="text-base font-semibold text-slate-800">Manage Students</h3>
        </button>
        <a href="{{ route('student.progress_report') }}" class="flex flex-col items-center justify-center p-8 bg-slate-50 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-lg transition-all">
            <span class="text-3xl mb-3">📊</span>
            <h3 class="text-base font-semibold text-slate-800">View Progress Reports</h3>
        </a>
    </div>

    <section>
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Class Overview</h2>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4">
            @forelse($students ?? [] as $row)
            <div class="flex flex-wrap items-center justify-between gap-3 py-3 border-b border-slate-200 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-medium text-slate-800 min-w-[6rem]">{{ $row['user']->name }}</span>
                    <span class="text-emerald-600 font-semibold">{{ $row['progressPercent'] ?? 0 }}%</span>
                </div>
                <div class="flex items-center gap-3 flex-1 min-w-[120px]">
                    <div class="flex-1 h-2.5 bg-slate-200 rounded-full overflow-hidden max-w-[140px]">
                        <div class="h-full bg-emerald-600 rounded-full transition-all" style="width: {{ min($row['progressPercent'] ?? 0, 100) }}%"></div>
                    </div>
                    <a href="{{ route('student.progress_report', ['student' => $row['user']->id]) }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">View report</a>
                </div>
            </div>
            @empty
            <p class="text-slate-500 py-2">No students yet.</p>
            @endforelse
        </div>
    </section>

    <div class="flex flex-wrap justify-between items-center gap-4 mt-8 pt-6 border-t border-slate-200">
        <div class="flex gap-3">
            <button type="button" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-lg transition">⚙️ Settings</button>
            <a href="{{ route('logout.confirmation') }}" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-lg transition">Logout</a>
        </div>
        <a href="{{ route('student.progress_report') }}" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-600/25 transition">View Full Report</a>
    </div>
@endsection
