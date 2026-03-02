@extends('layouts.app')

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ route('dashboard.teacher') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.common.app_name') }}" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <nav class="eco-dashboard-nav">
                @include('components.language-switcher')
                <a href="{{ route('dashboard.teacher') }}" class="{{ request()->routeIs('dashboard.teacher') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.nav.dashboard') }}</a>
                <a href="{{ route('teacher.class-rooms.index') }}" class="{{ request()->routeIs('teacher.class-rooms.*') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.teacher.classes') }}</a>
                <a href="{{ route('teacher.topics.index') }}" class="{{ request()->routeIs('teacher.topics.*') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.teacher.topics') }}</a>
                <a href="{{ route('teacher.quizzes.index') }}" class="{{ request()->routeIs('teacher.quizzes.*') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.teacher.quizzes') }}</a>
                <a href="{{ route('teacher.mini-games.index') }}" class="{{ request()->routeIs('teacher.mini-games.*') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.teacher.mini_games') }}</a>
                <a href="{{ route('teacher.badges.index') }}" class="{{ request()->routeIs('teacher.badges.*') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.teacher.badges') }}</a>
                <a href="{{ route('teacher.progress.index') }}" class="{{ request()->routeIs('teacher.progress.*') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.teacher.student_progress') }}</a>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">{{ __('messages.nav.logout') }}</button>
                </form>
            </nav>
        </header>
        <main style="flex: 1; padding: 2rem; max-width: 1200px; margin: 0 auto; width: 100%;">
            @if (session('status'))
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    {{ session('status') }}
                </div>
            @endif
            @yield('teacher')
        </main>
    </div>
@endsection
