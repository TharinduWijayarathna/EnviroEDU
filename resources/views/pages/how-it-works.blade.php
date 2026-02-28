@extends('layouts.landing')

@section('title', __('messages.how_it_works.title'))

@section('landing')
    <section class="eco-landing-section eco-landing-how">
        <h2 class="eco-landing-h2">{{ __('messages.how_it_works.headline') }}</h2>
        <p class="eco-landing-desc">{{ __('messages.how_it_works.desc') }}</p>

        <div class="eco-landing-prose">
            <p>{!! __('messages.how_it_works.prose') !!}</p>
        </div>

        <div class="eco-landing-how-grid">
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">1</span>
                <h3>{{ __('messages.how_it_works.step1_title') }}</h3>
                <p>{!! __('messages.how_it_works.step1_p1') !!}</p>
                <p class="eco-landing-how-note eco-callout">{{ __('messages.how_it_works.step1_note') }}</p>
            </div>
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">2</span>
                <h3>{{ __('messages.how_it_works.step2_title') }}</h3>
                <p>{!! __('messages.how_it_works.step2_p1', ['url' => route('landing.join')]) !!}</p>
                <p class="eco-landing-how-note eco-callout">{{ __('messages.how_it_works.step2_note') }}</p>
            </div>
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">3</span>
                <h3>{{ __('messages.how_it_works.step3_title') }}</h3>
                <p>{!! __('messages.how_it_works.step3_p1') !!}</p>
                <p class="eco-landing-how-note eco-callout">{{ __('messages.how_it_works.step3_note') }}</p>
            </div>
        </div>

        <div class="eco-landing-prose eco-landing-prose-end">
            <h3 class="eco-landing-h3">{{ __('messages.how_it_works.what_next_title') }}</h3>
            <p>{!! __('messages.how_it_works.what_next_p', ['url' => route('register', ['role' => 'admin'])]) !!}</p>
        </div>

        <div class="eco-home-cta eco-landing-cta">
            <p class="eco-home-cta-text">{{ __('messages.how_it_works.ready_to_start') }}</p>
            <div class="eco-home-actions">
                <a href="{{ route('register', ['role' => 'admin']) }}" class="eco-btn eco-btn-hero">{{ __('messages.home.register_school') }}</a>
                <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-outline">{{ __('messages.home.join_school') }}</a>
            </div>
        </div>
    </section>
@endsection
