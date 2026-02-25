@extends('layouts.landing')

@section('title', 'EnviroEdu for Schools')

@section('landing')
    <section class="eco-home-hero">
        <h1 class="eco-home-headline">Environmental learning in one workspace</h1>
        <p class="eco-home-sub">One platform per school. Topics, quizzes, games, and badges—with clear progress for teachers and students.</p>

        <div class="eco-home-stats" role="region" aria-label="Platform stats">
            <div class="eco-home-stat eco-card">
                <span class="eco-home-stat-value" aria-hidden="true">{{ $schoolCount }}</span>
                <span class="eco-home-stat-label">Schools</span>
            </div>
            <div class="eco-home-stat eco-card">
                <span class="eco-home-stat-value" aria-hidden="true">{{ $userCount }}</span>
                <span class="eco-home-stat-label">Teachers & students</span>
            </div>
            <div class="eco-home-stat eco-card">
                <span class="eco-home-stat-value" aria-hidden="true">1</span>
                <span class="eco-home-stat-label">Workspace per school</span>
            </div>
        </div>

        <div class="eco-home-actions">
            <a href="{{ route('register', ['role' => 'admin']) }}" class="eco-btn eco-btn-hero">Register your school</a>
            <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-outline">Join your school</a>
            <a href="{{ route('login', ['role' => 'admin']) }}" class="eco-landing-nav-link eco-home-link">School login</a>
        </div>
    </section>

    <section class="eco-home-about">
        <h2 class="eco-home-h2">Built for schools</h2>
        <div class="eco-home-prose-block">
            <p class="eco-home-prose">EnviroEdu gives each school its own secure workspace. School admins register once and receive a unique <strong>school code</strong>. Teachers and students join using that code and wait for admin approval, so only your people get access. No mixing between schools—your data stays in your workspace.</p>
            <p class="eco-home-prose">Teachers create <strong>topics</strong>, add <strong>quizzes</strong> and <strong>mini games</strong>, and award <strong>badges</strong> when students complete activities. Students see their progress and earned badges; parents can link to a child and view the same. Everything is focused on environmental science and built to be simple for all ages.</p>
        </div>
    </section>

    <section class="eco-home-who">
        <h2 class="eco-home-h2">Who uses EnviroEdu</h2>
        <ul class="eco-home-who-list">
            <li><strong>School admins</strong> — Register the school, get the code, and approve teachers and students.</li>
            <li><strong>Teachers</strong> — Create classes, topics, quizzes, and games; track student progress.</li>
            <li><strong>Students</strong> — Join with the school code, complete quizzes and games, earn badges.</li>
            <li><strong>Parents</strong> — Link to a child’s account and view their progress and badges.</li>
        </ul>
    </section>

    <section class="eco-home-links">
        <h2 class="eco-home-h2">Learn more</h2>
        <div class="eco-home-links-grid">
            <a href="{{ route('landing.platform') }}" class="eco-home-card eco-card">
                <span class="eco-home-card-title">What’s on the platform</span>
                <p>Topics, quizzes, mini games, badges, and progress tracking. See what teachers and students can do in your workspace.</p>
                <span class="eco-home-card-arrow" aria-hidden="true">→</span>
            </a>
            <a href="{{ route('landing.how-it-works') }}" class="eco-home-card eco-card">
                <span class="eco-home-card-title">How it works</span>
                <p>From school registration to sharing the code and approving members—the full flow in three steps.</p>
                <span class="eco-home-card-arrow" aria-hidden="true">→</span>
            </a>
        </div>
    </section>

    <section class="eco-home-cta">
        <p class="eco-home-cta-text">Ready to get started?</p>
        <div class="eco-home-actions">
            <a href="{{ route('register', ['role' => 'admin']) }}" class="eco-btn eco-btn-hero">Register your school</a>
            <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-outline">Join your school</a>
        </div>
    </section>
@endsection
