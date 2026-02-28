@extends('layouts.app')

@section('title', __('messages.auth.account_pending'))

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f0f4f3;">
        <div style="position: fixed; top: 1rem; right: 1rem; z-index: 1000;">@include('components.language-switcher')</div>
        <div class="eco-card" style="width: 100%; max-width: 420px; text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">⏳</div>
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: var(--eco-primary); margin-bottom: 0.75rem;">{{ __('messages.auth.account_pending') }}</h1>
            <p style="color: var(--eco-dark); margin-bottom: 1.5rem;">{{ __('messages.auth.account_pending_desc') }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="eco-btn">{{ __('messages.auth.sign_out') }}</button>
            </form>
        </div>
    </div>
@endsection
