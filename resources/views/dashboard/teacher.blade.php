@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .teacher-dash-grid { display: grid; grid-template-columns: repeat(3, 1fr) 280px; gap: 1.5rem; align-items: start; }
        .teacher-dash-card { background: #fff; border-radius: 20px; padding: 1.25rem; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 2px solid rgba(78, 205, 196, 0.25); }
        .teacher-dash-card h3 { font-family: 'Bubblegum Sans', cursive; font-size: 1.15rem; color: #333; margin-bottom: 0.5rem; }
        .teacher-dash-stat { font-size: 2rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; }
        .teacher-dash-card p.sub { color: #888; font-size: 0.9rem; margin-bottom: 0.75rem; }
        .teacher-dash-card .eco-btn { margin-top: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem; }
        .teacher-quick-actions { list-style: none; padding: 0; margin: 0; }
        .teacher-quick-actions a { display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0; color: #333; text-decoration: none; border-radius: 8px; transition: background 0.2s; }
        .teacher-quick-actions a:hover { background: #e0f7f5; }
        .teacher-dash-row2 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
        .teacher-dash-row3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 1.5rem; }
        .teacher-tip-banner { margin-top: 1.5rem; padding: 0.75rem 1rem; background: #fffde7; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem; }
        @media (max-width: 1024px) { .teacher-dash-grid { grid-template-columns: 1fr 1fr; } .teacher-dash-row2, .teacher-dash-row3 { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .teacher-dash-grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #fff;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ route('dashboard.teacher') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="SmartEdu" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: #333; margin: 0 auto;">Teacher Dashboard</h1>
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
            <p style="margin-bottom: 1.5rem; font-size: 1rem; color: #555;">Manage your classes and track student progress.</p>

            <div class="teacher-dash-grid">
                <div class="teacher-dash-card">
                    <span style="font-size: 2rem;">🖥️</span>
                    <div class="teacher-dash-stat">{{ $classCount ?? 0 }}</div>
                    <h3>{{ ($classCount ?? 0) > 0 ? 'Classes' : 'No classes yet' }}</h3>
                    <p class="sub">{{ ($classCount ?? 0) > 0 ? 'Manage your classes →' : 'Create your first class →' }}</p>
                    <a href="{{ ($classCount ?? 0) > 0 ? route('teacher.class-rooms.index') : route('teacher.class-rooms.create') }}" class="eco-btn">Add Class</a>
                </div>
                <div class="teacher-dash-card">
                    <span style="font-size: 2rem;">👥</span>
                    <div class="teacher-dash-stat">{{ $studentCount ?? 0 }}</div>
                    <h3>{{ ($studentCount ?? 0) > 0 ? 'Students' : 'No students yet' }}</h3>
                    <p class="sub">{{ ($studentCount ?? 0) > 0 ? 'View students in your classes →' : 'Invite students to join →' }}</p>
                    <a href="{{ route('teacher.class-rooms.index') }}" class="eco-btn">Invite Students</a>
                </div>
                <div class="teacher-dash-card">
                    <span style="font-size: 2rem;">📚</span>
                    <div class="teacher-dash-stat">{{ $topicCount ?? 0 }}</div>
                    <h3>{{ ($topicCount ?? 0) > 0 ? 'Topics' : 'No topics added' }}</h3>
                    <p class="sub">{{ ($topicCount ?? 0) > 0 ? 'Manage topics →' : 'Start with a sample topic →' }}</p>
                    <a href="{{ ($topicCount ?? 0) > 0 ? route('teacher.topics.index') : route('teacher.topics.create') }}" class="eco-btn">Create Topic</a>
                </div>
                <div class="teacher-dash-card">
                    <h3 style="display: flex; align-items: center; justify-content: space-between;">Quick Actions <span style="font-size: 1rem; cursor: pointer;">⋮</span></h3>
                    <ul class="teacher-quick-actions">
                        <li><a href="{{ route('teacher.topics.create') }}">➕ Create Topic</a></li>
                        <li><a href="{{ route('teacher.quizzes.create') }}">📝 Create Quiz</a></li>
                        <li><a href="{{ route('teacher.mini-games.create') }}">🎮 Create Mini Game</a></li>
                        <li><a href="{{ route('teacher.class-rooms.index') }}">➕ Add Students</a></li>
                    </ul>
                </div>
            </div>

            <div class="teacher-dash-row3">
                <a href="{{ route('teacher.topics.index') }}" class="teacher-dash-card" style="text-decoration: none; color: inherit;">
                    <span style="font-size: 1.5rem;">📚</span>
                    <div class="teacher-dash-stat">{{ $topicCount ?? 0 }} total</div>
                    <p class="sub">Organize content by topic. Add quizzes and mini games to topics.</p>
                    <span class="eco-btn">Manage Topics →</span>
                </a>
                <a href="{{ route('teacher.quizzes.index') }}" class="teacher-dash-card" style="text-decoration: none; color: inherit;">
                    <span style="font-size: 1.5rem;">📝</span>
                    <div class="teacher-dash-stat">{{ $quizCount ?? 0 }} Total quizzes</div>
                    <p class="sub">Create quizzes with multiple choice questions. Students can take them from their dashboard.</p>
                    <span class="eco-btn">Manage Quizzes →</span>
                </a>
                <a href="{{ route('teacher.mini-games.index') }}" class="teacher-dash-card" style="text-decoration: none; color: inherit;">
                    <span style="font-size: 1.5rem;">🎮</span>
                    <div class="teacher-dash-stat">{{ $miniGameCount ?? 0 }} Total Games</div>
                    <p class="sub">Create drag-and-drop, multiple choice, or matching games from templates.</p>
                    <span class="eco-btn">Manage Mini Games →</span>
                </a>
            </div>

            <div class="teacher-tip-banner">
                <span style="font-size: 1.5rem;">💡</span>
                <p style="margin: 0; font-size: 0.95rem;"><strong>Tips:</strong> You've created topics but no quizzes. Add at least one quiz to engage your students!</p>
                <a href="{{ route('teacher.quizzes.index') }}" style="color: var(--eco-primary); font-weight: 600; margin-left: auto;">View All →</a>
            </div>
        </main>
    </div>
@endsection

