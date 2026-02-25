@extends('layouts.app')

@section('title', 'Pending approval')

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f0f4f3;">
        <div class="eco-card" style="width: 100%; max-width: 420px; text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">⏳</div>
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: var(--eco-primary); margin-bottom: 0.75rem;">Account pending approval</h1>
            <p style="color: var(--eco-dark); margin-bottom: 1.5rem;">Your school admin has not approved your account yet. You’ll be able to use the platform once they approve you.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="eco-btn">Sign out</button>
            </form>
        </div>
    </div>
@endsection
