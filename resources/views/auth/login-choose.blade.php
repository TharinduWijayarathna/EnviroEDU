@extends('layouts.app')

@section('title', 'Login')

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f0f4f3;">
        <div style="width: 100%; max-width: 720px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <a href="{{ route('home') }}" class="eco-logo" style="justify-content: center;">
                    <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 56px; width: auto; object-fit: contain;">
                </a>
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: var(--eco-primary); margin-top: 1rem;">
                    Choose your role to log in
                </h2>
            </div>

            <div class="eco-landing-role-cards" style="justify-content: center;">
                <a href="{{ route('login.role', ['role' => 'admin']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">🏫</span>
                    <h3>School admin</h3>
                    <p>Manage your school, approve teachers and students, and view reports.</p>
                </a>
                <a href="{{ route('login.role', ['role' => 'teacher']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👩‍🏫</span>
                    <h3>Teacher</h3>
                    <p>Manage classes, topics & track progress. Sign in with your school account.</p>
                </a>
                <a href="{{ route('login.role', ['role' => 'student']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">🎒</span>
                    <h3>Student</h3>
                    <p>Learn & play with quizzes, games & badges. Sign in to continue learning.</p>
                </a>
                <a href="{{ route('login.role', ['role' => 'parent']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👨‍👩‍👧</span>
                    <h3>Parent</h3>
                    <p>View your child's progress & badges. Sign in to track their learning.</p>
                </a>
            </div>

            <p style="text-align: center; margin-top: 1.5rem;">
                <a href="{{ route('home') }}" style="color: var(--eco-dark); font-size: 0.9rem;">← Back to home</a>
            </p>
        </div>
    </div>
@endsection
