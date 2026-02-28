@extends('layouts.app')

@section('title', __('messages.auth.login'))

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f0f4f3;">
        <div style="position: fixed; top: 1rem; right: 1rem; z-index: 1000;">@include('components.language-switcher')</div>
        <div style="width: 100%; max-width: 720px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <a href="{{ route('home') }}" class="eco-logo" style="justify-content: center;">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.common.app_name') }}" style="height: 56px; width: auto; object-fit: contain;">
                </a>
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: var(--eco-primary); margin-top: 1rem;">
                    {{ __('messages.auth.choose_role') }}
                </h2>
            </div>

            <div class="eco-landing-role-cards" style="justify-content: center;">
                <a href="{{ route('login.role', ['role' => 'admin']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">🏫</span>
                    <h3>{{ __('messages.auth.school_admin') }}</h3>
                    <p>{{ __('messages.auth.school_admin_desc') }}</p>
                </a>
                <a href="{{ route('login.role', ['role' => 'teacher']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👩‍🏫</span>
                    <h3>{{ __('messages.auth.teacher') }}</h3>
                    <p>{{ __('messages.auth.teacher_desc') }}</p>
                </a>
                <a href="{{ route('login.role', ['role' => 'student']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">🎒</span>
                    <h3>{{ __('messages.auth.student') }}</h3>
                    <p>{{ __('messages.auth.student_desc') }}</p>
                </a>
                <a href="{{ route('login.role', ['role' => 'parent']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👨‍👩‍👧</span>
                    <h3>{{ __('messages.auth.parent') }}</h3>
                    <p>{{ __('messages.auth.parent_desc') }}</p>
                </a>
            </div>

            <p style="text-align: center; margin-top: 1.5rem;">
                <a href="{{ route('home') }}" style="color: var(--eco-dark); font-size: 0.9rem;">{{ __('messages.nav.back_to_home') }}</a>
            </p>
        </div>
    </div>
@endsection
