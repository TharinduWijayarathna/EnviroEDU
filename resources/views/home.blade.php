@extends('layouts.app')

@section('title', 'Welcome')

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; background: #f5f7f6;">
        <div class="eco-card" style="max-width: 520px; width: 100%; text-align: center;">
            <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 120px; width: auto; margin: 0 auto 1rem; display: block; object-fit: contain;">
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2.5rem; color: var(--eco-primary); margin-bottom: 0.5rem;">
                EnviroEdu
            </h1>
            <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.95;">
                Environmental Science Adventure — Choose your role to continue
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: center;">
                <a href="{{ route('login', ['role' => 'student']) }}" class="eco-card eco-role-card" style="flex: 1; min-width: 160px;">
                    <span class="eco-role-icon">🎒</span>
                    <h3>Student</h3>
                    <p>Learn & play with topics, games & badges</p>
                </a>
                <a href="{{ route('login', ['role' => 'teacher']) }}" class="eco-card eco-role-card" style="flex: 1; min-width: 160px;">
                    <span class="eco-role-icon">👩‍🏫</span>
                    <h3>Teacher</h3>
                    <p>Manage classes & track progress</p>
                </a>
                <a href="{{ route('login', ['role' => 'parent']) }}" class="eco-card eco-role-card" style="flex: 1; min-width: 160px;">
                    <span class="eco-role-icon">👨‍👩‍👧</span>
                    <h3>Parent</h3>
                    <p>View your child's progress & badges</p>
                </a>
            </div>
        </div>
    </div>
@endsection
