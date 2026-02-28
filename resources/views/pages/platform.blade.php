@extends('layouts.landing')

@section('title', __('messages.platform_page.title'))

@section('landing')
    <section class="eco-landing-section eco-landing-features">
        <h2 class="eco-landing-h2">{{ __('messages.platform_page.headline') }}</h2>
        <p class="eco-landing-desc">{{ __('messages.platform_page.desc') }}</p>

        <div class="eco-landing-prose">
            <p>{{ __('messages.platform_page.prose') }}</p>
        </div>

        <div class="eco-landing-feature-grid">
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📚</span>
                <h3>{{ __('messages.platform_page.topics_title') }}</h3>
                <p>{{ __('messages.platform_page.topics_desc') }}</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📝</span>
                <h3>{{ __('messages.platform_page.quizzes_title') }}</h3>
                <p>{{ __('messages.platform_page.quizzes_desc') }}</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">🎮</span>
                <h3>{{ __('messages.platform_page.mini_games_title') }}</h3>
                <p>{{ __('messages.platform_page.mini_games_desc') }}</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">🏆</span>
                <h3>{{ __('messages.platform_page.badges_title') }}</h3>
                <p>{{ __('messages.platform_page.badges_desc') }}</p>
            </div>
            <div class="eco-card eco-landing-feature-card">
                <span class="eco-landing-feature-icon">📊</span>
                <h3>{{ __('messages.platform_page.progress_title') }}</h3>
                <p>{{ __('messages.platform_page.progress_desc') }}</p>
            </div>
        </div>

        <div class="eco-platform-summary eco-card">
            <h3 class="eco-landing-h3">{{ __('messages.platform_page.for_teachers_title') }}</h3>
            <p>{{ __('messages.platform_page.for_teachers_desc') }}</p>
            <h3 class="eco-landing-h3">{{ __('messages.platform_page.for_students_title') }}</h3>
            <p>{{ __('messages.platform_page.for_students_desc') }}</p>
        </div>

        <div class="eco-home-cta eco-landing-cta">
            <p class="eco-home-cta-text">{{ __('messages.platform_page.ready_cta') }}</p>
            <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-hero">{{ __('messages.home.join_school') }}</a>
        </div>
    </section>
@endsection
