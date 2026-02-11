@extends('layouts.app')

@section('title', 'Register as ' . $roleLabel)

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f0f4f3;">
        <div class="eco-card" style="width: 100%; max-width: 420px;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <a href="{{ route('home') }}" class="eco-logo" style="justify-content: center;">
                    <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 56px; width: auto; object-fit: contain;">
                </a>
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: var(--eco-primary); margin-top: 1rem;">
                    Register as {{ $roleLabel }}
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

                <div style="margin-bottom: 1rem;">
                    <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Name</label>
                    <input id="name" type="text" name="name" class="eco-input" value="{{ old('name') }}" required autofocus autocomplete="name">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="email" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Email</label>
                    <input id="email" type="email" name="email" class="eco-input" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="password" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Password</label>
                    <input id="password" type="password" name="password" class="eco-input" required autocomplete="new-password">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="password_confirmation" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="eco-input" required autocomplete="new-password">
                </div>

                <button type="submit" class="eco-btn" style="width: 100%;">Create account</button>
            </form>

            <p style="text-align: center; margin-top: 1.25rem; font-size: 0.9rem;">
                Already have an account?
                <a href="{{ route('login', ['role' => $role]) }}" style="color: var(--eco-primary); font-weight: 700;">Login as {{ $roleLabel }}</a>
            </p>

            <p style="text-align: center; margin-top: 0.75rem;">
                <a href="{{ route('home') }}" style="color: var(--eco-dark); font-size: 0.9rem;">← Back to home</a>
            </p>
        </div>
    </div>
@endsection
