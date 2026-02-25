@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('student-main')
    <div id="badges" class="eco-card" style="padding: 1rem 1.25rem; margin-bottom: 1rem;">
        <h3 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-primary); margin-bottom: 0.75rem;">🏆 Earned badges</h3>
        @if (isset($earnedBadges) && $earnedBadges->isNotEmpty())
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                @foreach ($earnedBadges as $badge)
                    <span style="background: var(--eco-secondary); padding: 0.4rem 0.9rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem;">{{ $badge->icon ?? '🏆' }} {{ $badge->name }}</span>
                @endforeach
            </div>
        @else
            <p style="color: #666; margin: 0;">No badges earned yet. Complete quizzes and games in topics to earn badges!</p>
        @endif
    </div>
    <div class="eco-butterfly" aria-hidden="true">🦋</div>
    <h2 class="eco-game-header" id="ecoGameHeader">Select a topic to start!</h2>
    <div class="eco-game-content" id="ecoGameContent">
        <div style="text-align: center; color: #555;">
            <div class="eco-books-stack">
                <div class="eco-book green"></div>
                <div class="eco-book blue"></div>
                <div class="eco-book orange"></div>
            </div>
            <p style="font-size: 1.15rem;">Pick a topic from the left, watch the video lesson (if any), then play the quiz or game!</p>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/eco-student.js'])
@endpush
