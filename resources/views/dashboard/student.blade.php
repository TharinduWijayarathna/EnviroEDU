@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('student-main')
    <h2 class="eco-game-header" id="ecoGameHeader">Select a topic to start!</h2>
    <div class="eco-game-content" id="ecoGameContent">
        <div style="text-align: center; color: #666;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
            <p style="font-size: 1.2rem;">Pick a topic from the left, watch the video lesson (if any), then play the quiz or game!</p>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/eco-student.js'])
@endpush
