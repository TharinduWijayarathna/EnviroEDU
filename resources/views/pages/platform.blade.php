@extends('layouts.landing')

@section('title', 'Platform')

@section('landing')
    <section class="eco-landing-section eco-landing-features">
        <h2 class="eco-landing-h2">What you get</h2>
        <p class="eco-landing-desc">One workspace per school with everything in one place.</p>

        <div class="eco-landing-prose">
            <p>Each school has a single workspace. Teachers create and manage all content; students access it through their classes. Here’s what’s available on the platform.</p>
        </div>

        <div class="eco-landing-feature-grid">
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📚</span>
                <h3>Topics & content</h3>
                <p>Teachers organize lessons by topic. Each topic can have multiple quizzes and mini games attached, so students work through one theme at a time.</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📝</span>
                <h3>Quizzes</h3>
                <p>Multiple-choice quizzes to check understanding. Teachers set questions and options; students take quizzes and their results count toward progress and badges.</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">🎮</span>
                <h3>Mini games</h3>
                <p>Drag-and-drop, matching, and other interactive activities. Games are tied to topics and help reinforce learning in a fun way.</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">🏆</span>
                <h3>Badges</h3>
                <p>Teachers create badges for topics. Students earn them by completing quizzes or games. Badges show up on the student dashboard and motivate progress.</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📊</span>
                <h3>Progress</h3>
                <p>Teachers see how students are doing across quizzes and games. Parents can link to a child and view the same progress and badges from the parent dashboard.</p>
            </div>
        </div>

        <div class="eco-platform-summary eco-card">
            <h3 class="eco-landing-h3">For teachers</h3>
            <p>Create classes and invite students by email. Build topics, then add quizzes and mini games from templates. Track who has completed what and award badges when criteria are met.</p>
            <h3 class="eco-landing-h3">For students</h3>
            <p>Join a class, browse topics, and complete quizzes and games. Earn badges and see them on your dashboard. The interface is designed to be clear and engaging for all grade levels.</p>
        </div>

        <div class="eco-home-cta eco-landing-cta">
            <p class="eco-home-cta-text">Ready to use the platform?</p>
            <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-hero">Join your school</a>
        </div>
    </section>
@endsection
