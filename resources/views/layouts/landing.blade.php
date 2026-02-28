@extends('layouts.app')

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div class="eco-landing">
        <div class="eco-landing-wrap">
            <header class="eco-landing-header">
                <a href="{{ route('home') }}" class="eco-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.common.app_name') }}" class="eco-landing-logo-img">
                    <span>{{ __('messages.common.app_name') }}</span>
                </a>
                <nav class="eco-landing-nav">
                    <a href="{{ route('home') }}" class="eco-landing-nav-link">{{ __('messages.nav.home') }}</a>
                    <a href="{{ route('landing.join') }}" class="eco-landing-nav-link">{{ __('messages.nav.join') }}</a>
                    <a href="{{ route('landing.platform') }}" class="eco-landing-nav-link">{{ __('messages.nav.platform') }}</a>
                    <a href="{{ route('landing.how-it-works') }}" class="eco-landing-nav-link">{{ __('messages.nav.how_it_works') }}</a>
                    @include('components.language-switcher')
                    <a href="{{ route('login') }}" class="eco-btn eco-landing-nav-btn">{{ __('messages.nav.login') }}</a>
                </nav>
            </header>
            @yield('landing')
            <footer class="eco-landing-footer">
                <p>{{ __('messages.common.copyright', ['year' => date('Y')]) }}</p>
            </footer>
        </div>
    </div>
@endsection
