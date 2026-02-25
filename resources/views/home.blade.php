@extends('layouts.landing')

@section('title', 'EnviroEdu for Schools')

@section('landing')
    <section class="eco-home-hero">
        <h1 class="eco-home-headline">Environmental learning in one workspace</h1>
        <p class="eco-home-sub">One platform per school. Topics, quizzes, games, and badges—with clear progress for teachers and students.</p>

        <div class="eco-home-stats">
            <div class="eco-home-stat">
                <span class="eco-home-stat-value">{{ $schoolCount }}</span>
                <span class="eco-home-stat-label">Schools</span>
            </div>
            <div class="eco-home-stat">
                <span class="eco-home-stat-value">{{ $userCount }}</span>
                <span class="eco-home-stat-label">Teachers & students</span>
            </div>
            <div class="eco-home-stat">
                <span class="eco-home-stat-value">1</span>
                <span class="eco-home-stat-label">Workspace per school</span>
            </div>
        </div>

        <div class="eco-home-actions">
            <a href="{{ route('register', ['role' => 'admin']) }}" class="eco-btn eco-btn-hero">Register your school</a>
            <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-outline">Join your school</a>
            <a href="{{ route('login', ['role' => 'admin']) }}" class="eco-landing-nav-link eco-home-link">School login</a>
        </div>
    </section>

    <section class="eco-home-links">
        <a href="{{ route('landing.platform') }}" class="eco-home-card eco-card">
            <span class="eco-home-card-title">What’s on the platform</span>
            <p>Topics, quizzes, mini games, badges, and progress tracking.</p>
        </a>
        <a href="{{ route('landing.how-it-works') }}" class="eco-home-card eco-card">
            <span class="eco-home-card-title">How it works</span>
            <p>School registers → share code → teachers & students join → admin approves.</p>
        </a>
    </section>
@endsection
