@extends('layouts.app')

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div class="eco-landing">
        <div class="eco-landing-wrap">
            <header class="eco-landing-header">
                <a href="{{ route('home') }}" class="eco-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" class="eco-landing-logo-img">
                    <span>EnviroEdu</span>
                </a>
                <nav class="eco-landing-nav">
                    <a href="{{ route('home') }}" class="eco-landing-nav-link">Home</a>
                    <a href="{{ route('landing.join') }}" class="eco-landing-nav-link">Join</a>
                    <a href="{{ route('landing.platform') }}" class="eco-landing-nav-link">Platform</a>
                    <a href="{{ route('landing.how-it-works') }}" class="eco-landing-nav-link">How it works</a>
                    <a href="{{ route('login') }}" class="eco-btn eco-landing-nav-btn">Login</a>
                </nav>
            </header>
            @yield('landing')
            <footer class="eco-landing-footer">
                <p>&copy; {{ date('Y') }} EnviroEdu. Environmental Science Adventure.</p>
            </footer>
        </div>
    </div>
@endsection
