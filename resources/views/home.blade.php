@extends('layouts.app')

@section('title', 'EnviroEdu for Schools')

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div class="eco-landing">
        <header class="eco-landing-header">
            <a href="{{ route('home') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" class="eco-landing-logo-img">
                <span>EnviroEdu</span>
            </a>
            <nav class="eco-landing-nav">
                <a href="{{ route('login', ['role' => 'admin']) }}">School login</a>
                <a href="#join">Join your school</a>
            </nav>
        </header>

        <section class="eco-landing-hero">
            <div class="eco-landing-hero-content">
                <h1 class="eco-landing-title">Environmental learning, one school at a time</h1>
                <p class="eco-landing-tagline">Give your school a workspace where teachers and students explore topics, quizzes, games, and badges—all in one place.</p>
                <div class="eco-landing-hero-actions">
                    <a href="{{ route('register', ['role' => 'admin']) }}" class="eco-btn eco-btn-hero">Register your school</a>
                    <a href="{{ route('login', ['role' => 'admin']) }}" class="eco-btn eco-btn-outline">School admin login</a>
                </div>
            </div>
            <div class="eco-landing-hero-visual" aria-hidden="true">
                <span class="eco-landing-emoji">🌱</span>
                <span class="eco-landing-emoji">📚</span>
                <span class="eco-landing-emoji">🏫</span>
            </div>
        </section>

        <section class="eco-landing-roles" id="join">
            <h2 class="eco-landing-section-title">Join your school</h2>
            <p class="eco-landing-section-desc">Teachers and students: sign in or register with your school.</p>
            <div class="eco-landing-role-cards">
                <a href="{{ route('login', ['role' => 'teacher']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👩‍🏫</span>
                    <h3>Teacher</h3>
                    <p>Manage classes, topics & track progress</p>
                </a>
                <a href="{{ route('login', ['role' => 'student']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">🎒</span>
                    <h3>Student</h3>
                    <p>Learn & play with quizzes, games & badges</p>
                </a>
                <a href="{{ route('login', ['role' => 'parent']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👨‍👩‍👧</span>
                    <h3>Parent</h3>
                    <p>View your child's progress & badges</p>
                </a>
            </div>
        </section>

        <footer class="eco-landing-footer">
            <p>&copy; {{ date('Y') }} EnviroEdu. Environmental Science Adventure.</p>
        </footer>
    </div>
@endsection
