@extends('layouts.app')

@section('title', __('messages.parent.dashboard'))

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .eco-dash-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .eco-dash-card { padding: 1.5rem; }
        .eco-dash-card h3 { font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-primary); margin-bottom: 0.75rem; }
    </style>
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ route('dashboard.parent') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.common.app_name') }}" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <nav class="eco-dashboard-nav">
                @include('components.language-switcher')
                <a href="{{ route('dashboard.parent') }}" class="{{ request()->routeIs('dashboard.parent') ? 'eco-dashboard-link-active' : '' }}">{{ __('messages.nav.dashboard') }}</a>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">{{ __('messages.nav.logout') }}</button>
                </form>
            </nav>
        </header>

        <main style="flex: 1; padding: 2rem; max-width: 1200px; margin: 0 auto; width: 100%;">
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ __('messages.parent.dashboard') }}</h1>
            <p style="margin-bottom: 2rem; font-size: 1.1rem; color: #555;">{{ __('messages.parent.dashboard_desc') }}</p>

            @if (session('status'))
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="eco-card eco-dash-card" style="margin-bottom: 2rem;">
                <h3>👤 {{ __('messages.parent.link_child') }}</h3>
                <p style="color: #666; margin-bottom: 1rem;">{{ __('messages.parent.link_child_desc') }}</p>
                <form method="POST" action="{{ route('parent.children.store') }}" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: flex-end;">
                    @csrf
                    <div>
                        <label for="email" style="display: block; font-weight: 600; margin-bottom: 0.25rem;">{{ __('messages.parent.child_email') }}</label>
                        <input id="email" type="email" name="email" class="eco-input" value="{{ old('email') }}" required placeholder="student@example.com" style="min-width: 220px;">
                    </div>
                    <button type="submit" class="eco-btn">{{ __('messages.parent.link_account') }}</button>
                </form>
            </div>

            <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.35rem; color: var(--eco-primary); margin-bottom: 1rem;">{{ __('messages.parent.my_children') }}</h2>
            @if ($children->isEmpty())
                <div class="eco-card" style="padding: 1.5rem; color: #666;">
                    <p>{{ __('messages.parent.no_children') }}</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach ($children as $child)
                        <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <strong>{{ $child->name }}</strong>
                                <span style="color: #666;">{{ $child->email }}</span>
                                @if ($child->badges_count > 0 || $child->quiz_attempts_count > 0)
                                    <div style="margin-top: 0.5rem; display: flex; gap: 1rem; font-size: 0.9rem;">
                                        <span>🏆 {{ $child->badges_count }} {{ __('messages.parent.badges') }}</span>
                                        <span>📋 {{ $child->quiz_attempts_count }} {{ __('messages.parent.quiz_attempts') }}</span>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('parent.children.show', $child) }}" class="eco-btn" style="padding: 0.5rem 1rem;">{{ __('messages.parent.view_badges_progress') }}</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
@endsection
