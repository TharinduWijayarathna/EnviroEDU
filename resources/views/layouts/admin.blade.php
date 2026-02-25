@extends('layouts.app')

@push('styles')
    @vite(['resources/css/eco.css'])
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ route('dashboard.admin') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <nav class="eco-dashboard-nav">
                <a href="{{ route('dashboard.admin') }}">Dashboard</a>
                <a href="{{ route('admin.approvals.index') }}">Pending approvals</a>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">Logout</button>
                </form>
            </nav>
        </header>
        <main style="flex: 1; padding: 2rem; max-width: 1200px; margin: 0 auto; width: 100%;">
            @if (session('status'))
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 0.75rem 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    {{ session('status') }}
                </div>
            @endif
            @yield('admin')
        </main>
    </div>
@endsection
