@extends('layouts.admin')

@section('title', __('messages.admin.school_dashboard'))

@section('admin')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ __('messages.admin.school_dashboard') }}</h1>
    <p style="margin-bottom: 1.5rem; font-size: 1rem; color: #555;">{{ __('messages.admin.dashboard_desc') }}</p>

    @if ($school)
        <div class="eco-card" style="max-width: 560px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 0.75rem;">{{ $school->name }}</h2>
            <p style="color: #666; margin-bottom: 0.5rem;">{{ __('messages.admin.school_code') }} <strong style="font-size: 1.1rem; color: var(--eco-primary);">{{ $school->slug }}</strong></p>
            <p style="font-size: 0.9rem; color: #888;">{{ __('messages.admin.share_code') }}</p>
        </div>

        <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 1rem;">{{ __('messages.admin.analytics') }}</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.teachers.index') }}" class="eco-card" style="padding: 1rem; text-decoration: none; color: inherit;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--eco-primary);">{{ $teachersCount ?? 0 }}</div>
                <div style="font-size: 0.9rem; color: #666;">{{ __('messages.admin.teachers') }}</div>
            </a>
            <a href="{{ route('admin.students.index') }}" class="eco-card" style="padding: 1rem; text-decoration: none; color: inherit;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--eco-primary);">{{ $studentsCount ?? 0 }}</div>
                <div style="font-size: 0.9rem; color: #666;">{{ __('messages.admin.students') }}</div>
            </a>
            <div class="eco-card" style="padding: 1rem;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--eco-primary);">{{ $classesCount ?? 0 }}</div>
                <div style="font-size: 0.9rem; color: #666;">{{ __('messages.admin.classes') }}</div>
            </div>
            <div class="eco-card" style="padding: 1rem;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--eco-primary);">{{ $topicsCount ?? 0 }}</div>
                <div style="font-size: 0.9rem; color: #666;">{{ __('messages.admin.topics') }}</div>
            </div>
            <div class="eco-card" style="padding: 1rem;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--eco-primary);">{{ $quizzesCount ?? 0 }}</div>
                <div style="font-size: 0.9rem; color: #666;">{{ __('messages.admin.quizzes') }}</div>
            </div>
            <div class="eco-card" style="padding: 1rem;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--eco-primary);">{{ $miniGamesCount ?? 0 }}</div>
                <div style="font-size: 0.9rem; color: #666;">{{ __('messages.admin.games') }}</div>
            </div>
            <div class="eco-card" style="padding: 1rem;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--eco-primary);">{{ $quizAttemptsCount ?? 0 }}</div>
                <div style="font-size: 0.9rem; color: #666;">{{ __('messages.admin.quiz_attempts') }}</div>
            </div>
            <div class="eco-card" style="padding: 1rem;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--eco-primary);">{{ $badgesEarnedCount ?? 0 }}</div>
                <div style="font-size: 0.9rem; color: #666;">{{ __('messages.admin.badges_earned') }}</div>
            </div>
        </div>

        @if ($pendingTeachersCount > 0 || $pendingStudentsCount > 0)
            <div class="eco-card" style="max-width: 560px; padding: 1.5rem;">
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 0.75rem;">{{ __('messages.admin.pending_approvals') }}</h2>
                <p style="color: #666; margin-bottom: 1rem;">
                    {{ __('messages.admin.pending_count', ['teachers' => $pendingTeachersCount, 'students' => $pendingStudentsCount]) }}
                </p>
                <a href="{{ route('admin.approvals.index') }}" class="eco-btn">{{ __('messages.admin.review_pending') }}</a>
            </div>
        @endif
    @else
        <div class="eco-card" style="max-width: 560px; padding: 1.5rem;">
            <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 0.75rem;">{{ __('messages.admin.welcome', ['name' => auth()->user()->name]) }}</h2>
            <p style="color: #666;">{{ __('messages.admin.school_not_setup') }}</p>
        </div>
    @endif
@endsection
