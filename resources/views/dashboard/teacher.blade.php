@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .eco-dash-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .eco-dash-card { padding: 1.5rem; }
        .eco-dash-card h3 { font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-primary); margin-bottom: 0.75rem; }
        .eco-dash-stats { display: flex; gap: 1rem; flex-wrap: wrap; }
        .eco-stat { background: var(--eco-primary); color: white; padding: 1rem 1.5rem; border-radius: 16px; font-weight: 700; min-width: 120px; text-align: center; }
        .eco-stat span { display: block; font-size: 1.75rem; }
    </style>
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ route('dashboard.teacher') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <nav class="eco-dashboard-nav">
                <a href="{{ route('dashboard.teacher') }}">Dashboard</a>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">Logout</button>
                </form>
            </nav>
        </header>

        <main style="flex: 1; padding: 2rem; max-width: 1200px; margin: 0 auto; width: 100%;">
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">Teacher Dashboard</h1>
            <p style="margin-bottom: 2rem; font-size: 1.1rem; color: #555;">Manage your classes and track student progress.</p>

            <div class="eco-dash-stats" style="margin-bottom: 2rem;">
                <div class="eco-stat"><span>0</span> Classes</div>
                <div class="eco-stat"><span>0</span> Students</div>
                <div class="eco-stat"><span>{{ $topicCount ?? 0 }}</span> My Topics</div>
            </div>

            <div class="eco-dash-grid">
                <a href="{{ route('teacher.topics.index') }}" class="eco-card eco-dash-card" style="text-decoration: none; color: inherit;">
                    <h3>📚 Topics</h3>
                    <p style="color: #666;">Organize content by topic. Attach quizzes and mini games to topics so students see them in their dashboard.</p>
                    <span class="eco-btn" style="margin-top: 1rem; display: inline-block;">Manage Topics</span>
                </a>
                <a href="{{ route('teacher.quizzes.index') }}" class="eco-card eco-dash-card" style="text-decoration: none; color: inherit;">
                    <h3>📝 Quizzes</h3>
                    <p style="color: #666;">Create quizzes with multiple choice questions. Students can take them from their dashboard.</p>
                    <span class="eco-btn" style="margin-top: 1rem; display: inline-block;">Manage Quizzes</span>
                </a>
                <a href="{{ route('teacher.mini-games.index') }}" class="eco-card eco-dash-card" style="text-decoration: none; color: inherit;">
                    <h3>🎮 Mini Games</h3>
                    <p style="color: #666;">Create drag-and-drop, multiple choice, or matching games from templates.</p>
                    <span class="eco-btn" style="margin-top: 1rem; display: inline-block;">Manage Mini Games</span>
                </a>
                <a href="{{ route('teacher.progress.index') }}" class="eco-card eco-dash-card" style="text-decoration: none; color: inherit;">
                    <h3>📊 Student Progress</h3>
                    <p style="color: #666;">View student progress, quiz scores, and game attempts.</p>
                    <span class="eco-btn" style="margin-top: 1rem; display: inline-block;">View Progress</span>
                </a>
            </div>
        </main>
    </div>
@endsection

