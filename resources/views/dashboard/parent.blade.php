@extends('layouts.app')

@section('title', 'Parent Dashboard')

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .eco-dash-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .eco-dash-card { padding: 1.5rem; }
        .eco-dash-card h3 { font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-primary); margin-bottom: 0.75rem; }
        .eco-badge-list { display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .eco-badge-pill { background: var(--eco-secondary); padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem; }
    </style>
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ route('dashboard.parent') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <nav class="eco-dashboard-nav">
                <a href="{{ route('dashboard.parent') }}">Dashboard</a>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">Logout</button>
                </form>
            </nav>
        </header>

        <main style="flex: 1; padding: 2rem; max-width: 1200px; margin: 0 auto; width: 100%;">
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">Parent Dashboard</h1>
            <p style="margin-bottom: 2rem; font-size: 1.1rem; color: #555;">View your child's progress and celebrate their eco-learning journey.</p>

            <div class="eco-dash-grid">
                <div class="eco-card eco-dash-card">
                    <h3>👤 My Children</h3>
                    <p style="color: #666;">Link to your child's account to see their activity and progress.</p>
                    <button type="button" class="eco-btn" style="margin-top: 1rem;" disabled>Coming soon</button>
                </div>
                <div class="eco-card eco-dash-card">
                    <h3>🏆 Badges</h3>
                    <p style="color: #666;">Badges earned by your child will appear here.</p>
                    <div class="eco-badge-list" style="margin-top: 1rem;">
                        <span class="eco-badge-pill">No badges yet</span>
                    </div>
                </div>
                <div class="eco-card eco-dash-card">
                    <h3>📈 Progress</h3>
                    <p style="color: #666;">Topic completion and quiz scores.</p>
                    <button type="button" class="eco-btn" style="margin-top: 1rem;" disabled>Coming soon</button>
                </div>
            </div>
        </main>
    </div>
@endsection

