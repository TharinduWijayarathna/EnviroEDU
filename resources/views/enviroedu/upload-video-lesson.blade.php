@extends('layouts.enviroedu')

@section('title', 'Upload Video Lesson - EnviroEdu')

@section('backUrl', route('teacher.dashboard'))

@section('content')
    <header class="text-center border-b border-slate-200 pb-6 mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Upload Video Lesson</h1>
    </header>

    <form action="{{ route('teacher.upload_video.post') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <section>
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Lesson details</h2>
            <div class="space-y-4">
                <div>
                    <label for="topicName" class="block text-sm font-medium text-slate-700 mb-1.5">Topic / Title</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="title" id="topicName" value="{{ old('title') }}" placeholder="Enter lesson title" required
                            class="flex-1 px-4 py-3 border border-slate-300 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        <button type="button" onclick="document.getElementById('fileInput').click()" class="px-5 py-3 bg-slate-200 hover:bg-slate-300 text-slate-800 font-medium rounded-xl transition whitespace-nowrap">
                            Browse video...
                        </button>
                        <input type="file" id="fileInput" name="video" accept="video/*" class="hidden" onchange="handleFileSelect(event)">
                    </div>
                    @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div onclick="document.getElementById('fileInput').click()" class="border-2 border-dashed border-emerald-500 rounded-xl p-8 text-center bg-slate-50 cursor-pointer hover:bg-slate-100 transition">
                    <span class="text-4xl block mb-2">📤</span>
                    <p class="text-slate-500">Drag & drop video file or click to browse</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Target grade</h2>
            <div class="space-y-3">
                <label class="flex items-center gap-3 p-4 bg-slate-50 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-emerald-500 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                    <input type="radio" name="grade_level" value="grade4" {{ old('grade_level') === 'grade4' ? 'checked' : '' }} required class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-slate-800 font-medium">Grade 4 (Habitats)</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-slate-50 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-emerald-500 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                    <input type="radio" name="grade_level" value="grade5" {{ old('grade_level') === 'grade5' ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-slate-800 font-medium">Grade 5 (Water Cycle)</span>
                </label>
            </div>
        </section>

        <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/25 transition">
            Upload lesson
        </button>
    </form>

    <p class="text-center mt-6">
        <a href="{{ route('teacher.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">← Back to Dashboard</a>
    </p>
@endsection

@push('scripts')
<script>
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        const topicInput = document.getElementById('topicName');
        if (!topicInput.value) topicInput.value = file.name.replace(/\.[^/.]+$/, '');
    }
}
</script>
@endpush
