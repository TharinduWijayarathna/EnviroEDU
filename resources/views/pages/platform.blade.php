@extends('layouts.landing')

@section('title', 'Platform')

@section('landing')
    <section class="eco-landing-section eco-landing-features">
        <h2 class="eco-landing-h2">What you get</h2>
        <p class="eco-landing-desc">One workspace per school with everything in one place.</p>
        <div class="eco-landing-feature-grid">
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📚</span>
                <h3>Topics & content</h3>
                <p>Teachers organize lessons by topic and attach quizzes and games.</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📝</span>
                <h3>Quizzes</h3>
                <p>Multiple-choice quizzes to check understanding and earn progress.</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">🎮</span>
                <h3>Mini games</h3>
                <p>Drag-and-drop, matching, and other fun activities for learning.</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">🏆</span>
                <h3>Badges</h3>
                <p>Students earn badges for completing quizzes and games.</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📊</span>
                <h3>Progress</h3>
                <p>Teachers and parents can see student progress and engagement.</p>
            </div>
        </div>
    </section>
@endsection
