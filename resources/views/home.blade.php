@extends('layouts.landing')

@section('title', __('messages.home.title'))

@section('landing')
    <section class="eco-home-hero">
        <h1 class="eco-home-headline">{{ __('messages.home.headline') }}</h1>
        <p class="eco-home-sub">{{ __('messages.home.subheadline') }}</p>

        <div class="eco-home-stats" role="region" aria-label="Platform stats">
            <div class="eco-home-stat eco-card">
                <span class="eco-home-stat-value" aria-hidden="true">{{ $schoolCount }}</span>
                <span class="eco-home-stat-label">{{ __('messages.home.schools') }}</span>
            </div>
            <div class="eco-home-stat eco-card">
                <span class="eco-home-stat-value" aria-hidden="true">{{ $userCount }}</span>
                <span class="eco-home-stat-label">{{ __('messages.home.teachers_students') }}</span>
            </div>
            <div class="eco-home-stat eco-card">
                <span class="eco-home-stat-value" aria-hidden="true">1</span>
                <span class="eco-home-stat-label">{{ __('messages.home.workspace_per_school') }}</span>
            </div>
        </div>

        <div class="eco-home-actions">
            <a href="{{ route('register', ['role' => 'admin']) }}" class="eco-btn eco-btn-hero">{{ __('messages.home.register_school') }}</a>
            <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-outline">{{ __('messages.home.join_school') }}</a>
        </div>
    </section>

    <section class="eco-home-about">
        <h2 class="eco-home-h2">{{ __('messages.home.built_for_schools') }}</h2>
        <div class="eco-home-prose-block">
            <p class="eco-home-prose">{!! __('messages.home.built_for_schools_p1') !!}</p>
            <p class="eco-home-prose">{!! __('messages.home.built_for_schools_p2') !!}</p>
        </div>
    </section>

    <section class="eco-home-who">
        <h2 class="eco-home-h2">{{ __('messages.home.who_uses') }}</h2>
        <ul class="eco-home-who-list">
            <li>{!! __('messages.home.who_admin') !!}</li>
            <li>{!! __('messages.home.who_teacher') !!}</li>
            <li>{!! __('messages.home.who_student') !!}</li>
            <li>{!! __('messages.home.who_parent') !!}</li>
        </ul>
    </section>

    <section class="eco-home-links">
        <h2 class="eco-home-h2">{{ __('messages.home.learn_more') }}</h2>
        <div class="eco-home-links-grid">
            <a href="{{ route('landing.platform') }}" class="eco-home-card eco-card">
                <span class="eco-home-card-title">{{ __('messages.home.platform_card_title') }}</span>
                <p>{{ __('messages.home.platform_card_desc') }}</p>
                <span class="eco-home-card-arrow" aria-hidden="true">→</span>
            </a>
            <a href="{{ route('landing.how-it-works') }}" class="eco-home-card eco-card">
                <span class="eco-home-card-title">{{ __('messages.home.how_it_works_card_title') }}</span>
                <p>{{ __('messages.home.how_it_works_card_desc') }}</p>
                <span class="eco-home-card-arrow" aria-hidden="true">→</span>
            </a>
        </div>
    </section>

    <section class="eco-home-cta">
        <p class="eco-home-cta-text">{{ __('messages.home.ready_to_start') }}</p>
        <div class="eco-home-actions">
            <a href="{{ route('register', ['role' => 'admin']) }}" class="eco-btn eco-btn-hero">{{ __('messages.home.register_school') }}</a>
            <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-outline">{{ __('messages.home.join_school') }}</a>
        </div>
    </section>
@endsection
