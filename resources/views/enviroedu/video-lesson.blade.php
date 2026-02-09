@extends('layouts.enviroedu')

@section('title', $currentLesson ? 'Video Lesson - ' . $currentLesson->title : 'Video Lessons - EnviroEdu')

@section('backUrl', route('student.dashboard'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Video Lessons</h1>
    </header>

    @if($currentLesson)
    <h2 class="text-xl font-semibold text-slate-800 mb-6">{{ $currentLesson->title }}</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-slate-50 rounded-xl p-4">
            <div class="aspect-video bg-gradient-to-br from-emerald-600 to-teal-600 rounded-xl flex items-center justify-center cursor-pointer hover:scale-[1.01] transition-transform" onclick="playVideo()">
                @if($currentLesson->video_path)
                <video id="lessonVideo" src="{{ asset('storage/' . $currentLesson->video_path) }}" controls class="hidden w-full h-full object-contain rounded-xl"></video>
                @endif
                <div id="playBtn" class="w-16 h-16 sm:w-20 sm:h-20 bg-white/90 rounded-full flex items-center justify-center text-2xl text-emerald-600">▶</div>
            </div>
            <div class="flex items-center gap-3 mt-3">
                <button type="button" id="playPauseBtn" onclick="playVideo()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">▶</button>
            </div>
            <form action="{{ route('lesson.complete') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="video_lesson_id" value="{{ $currentLesson->id }}">
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">Mark as completed</button>
            </form>
        </div>
        <div class="bg-slate-50 rounded-xl p-5">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Key Points</h3>
            @if(!empty($currentLesson->key_points))
            <ul class="space-y-2">
                @foreach($currentLesson->key_points as $point)
                <li class="text-slate-600 pl-4 border-l-2 border-emerald-500">{{ $point }}</li>
                @endforeach
            </ul>
            @else
            <p class="text-slate-500">No key points for this lesson.</p>
            @endif
            @if($videoLessons->count() > 1)
            <h3 class="text-base font-semibold text-slate-800 mt-6 mb-2">Other lessons</h3>
            <ul class="space-y-1">
                @foreach($videoLessons as $lesson)
                <li>
                    <a href="{{ route('video.lesson.show', $lesson) }}" class="block py-2 px-3 rounded-lg text-slate-700 hover:bg-slate-200 {{ $lesson->id === $currentLesson->id ? 'bg-slate-200 font-semibold text-emerald-700' : '' }}">{{ $lesson->title }}</a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('games.quiz') }}" class="inline-block px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/25 transition">Start Quiz</a>
    </div>
    @else
    <p class="text-slate-500 mb-6">No video lessons available yet. Check back later!</p>
    <a href="{{ route('student.dashboard') }}" class="inline-block w-full max-w-xs mx-auto text-center px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition">← Back to Dashboard</a>
    @endif
@endsection

@push('scripts')
<script>
let isPlaying = false;
function playVideo() {
    const video = document.getElementById('lessonVideo');
    const playBtn = document.getElementById('playBtn');
    const playPauseBtn = document.getElementById('playPauseBtn');
    if (video) {
        if (video.classList.contains('hidden')) {
            video.classList.remove('hidden');
            if (playBtn) playBtn.classList.add('hidden');
        }
        isPlaying = !isPlaying;
        isPlaying ? video.play() : video.pause();
        if (playPauseBtn) playPauseBtn.textContent = isPlaying ? '❚❚' : '▶';
    } else {
        if (playPauseBtn) { isPlaying = !isPlaying; playPauseBtn.textContent = isPlaying ? '❚❚' : '▶'; }
    }
}
</script>
@endpush
