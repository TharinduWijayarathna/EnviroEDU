@extends('layouts.app')

@section('title', $role === 'admin' ? __('messages.auth.register_your_school') : __('messages.auth.register_as', ['role' => $roleLabel]))

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f0f4f3;">
        <div style="position: fixed; top: 1rem; right: 1rem; z-index: 1000;">@include('components.language-switcher')</div>
        <div class="eco-card" style="width: 100%; max-width: 420px;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <a href="{{ route('home') }}" class="eco-logo" style="justify-content: center;">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.common.app_name') }}" style="height: 56px; width: auto; object-fit: contain;">
                </a>
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: var(--eco-primary); margin-top: 1rem;">
                    {{ $role === 'admin' ? __('messages.auth.register_your_school') : __('messages.auth.register_as', ['role' => $roleLabel]) }}
                </h2>
            </div>

            @if ($errors->any())
                <div style="background: #fff2f2; border: 2px solid var(--eco-accent); border-radius: 12px; padding: 0.75rem; margin-bottom: 1rem; font-size: 0.9rem;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                @if ($role === 'admin')
                    <div style="margin-bottom: 1rem;">
                        <label for="school_name" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">{{ __('messages.auth.school_name') }}</label>
                        <input id="school_name" type="text" name="school_name" class="eco-input" value="{{ old('school_name') }}" required autofocus>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label for="school_code" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">{{ __('messages.auth.school_code') }}</label>
                        <input id="school_code" type="text" name="school_code" class="eco-input" value="{{ old('school_code') }}" required placeholder="{{ __('messages.auth.school_code_placeholder') }}" maxlength="60">
                        <p style="font-size: 0.8rem; color: #666; margin-top: 0.35rem;">{{ __('messages.auth.school_code_hint') }}</p>
                    </div>
                @endif

                @if (in_array($role, ['teacher', 'student']))
                    <div style="margin-bottom: 1rem;">
                        <label for="school_code" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">{{ __('messages.auth.school_code') }}</label>
                        <input id="school_code" type="text" name="school_code" class="eco-input" value="{{ old('school_code', request('school_code')) }}" required placeholder="{{ __('messages.auth.school_code_ask_admin') }}">
                    </div>
                @endif

                <div style="margin-bottom: 1rem;">
                    <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">{{ $role === 'admin' ? __('messages.auth.your_name') : __('messages.auth.name') }}</label>
                    <input id="name" type="text" name="name" class="eco-input" value="{{ old('name') }}" required {{ $role !== 'admin' && !in_array($role, ['teacher', 'student']) ? 'autofocus' : '' }} autocomplete="name">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="email" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">{{ __('messages.auth.email') }}</label>
                    <input id="email" type="email" name="email" class="eco-input" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="password" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">{{ __('messages.auth.password') }}</label>
                    <input id="password" type="password" name="password" class="eco-input" required autocomplete="new-password">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="password_confirmation" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">{{ __('messages.auth.confirm_password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="eco-input" required autocomplete="new-password">
                </div>

                <button type="submit" class="eco-btn" style="width: 100%;">{{ __('messages.auth.create_account') }}</button>
            </form>

            <p style="text-align: center; margin-top: 1.25rem; font-size: 0.9rem;">
                {{ __('messages.auth.already_have_account') }}
                <a href="{{ route('login.role', ['role' => $role]) }}" style="color: var(--eco-primary); font-weight: 700;">{{ __('messages.auth.login_as', ['role' => $role === 'admin' ? __('messages.auth.school_admin') : $roleLabel]) }}</a>
            </p>

            <p style="text-align: center; margin-top: 0.75rem;">
                <a href="{{ route('home') }}" style="color: var(--eco-dark); font-size: 0.9rem;">{{ __('messages.nav.back_to_home') }}</a>
            </p>
        </div>
    </div>
@endsection
