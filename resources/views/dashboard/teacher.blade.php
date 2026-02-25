@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@push('styles')
    <style>
        .teacher-dash-grid { display: grid; grid-template-columns: repeat(3, 1fr) 280px; gap: 1.5rem; align-items: start; }
        .teacher-dash-card { background: #fff; border-radius: 20px; padding: 1.25rem; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 2px solid rgba(78, 205, 196, 0.25); text-decoration: none; color: inherit; display: block; transition: border-color 0.2s, box-shadow 0.2s; }
        .teacher-dash-card:hover { border-color: var(--eco-primary); box-shadow: 0 4px 16px rgba(78, 205, 196, 0.2); }
        .teacher-dash-card h3 { font-family: 'Bubblegum Sans', cursive; font-size: 1.15rem; color: #333; margin-bottom: 0.5rem; }
        .teacher-dash-stat { font-size: 2rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; }
        .teacher-dash-card p.sub { color: #888; font-size: 0.9rem; margin-bottom: 0.75rem; }
        .teacher-dash-card .eco-btn, .teacher-dash-card .link-btn { margin-top: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem; display: inline-block; }
        .teacher-quick-actions { list-style: none; padding: 0; margin: 0; }
        .teacher-quick-actions a { display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0; color: #333; text-decoration: none; border-radius: 8px; transition: background 0.2s; }
        .teacher-quick-actions a:hover { background: #e0f7f5; }
        .teacher-dash-row2 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 1.5rem; }
        .teacher-dash-row3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 1.5rem; }
        .teacher-tip-banner { margin-top: 1.5rem; padding: 0.75rem 1rem; background: #fffde7; border-radius: 12px; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
        @media (max-width: 1024px) { .teacher-dash-grid { grid-template-columns: 1fr 1fr; } .teacher-dash-row2, .teacher-dash-row3 { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .teacher-dash-grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">Dashboard</h1>
    <p style="margin-bottom: 1.5rem; font-size: 1rem; color: #555;">Manage your classes, content, and track student progress. Use the links below or the header to go to any section.</p>

    <div class="teacher-dash-grid">
        <a href="{{ route('teacher.class-rooms.index') }}" class="teacher-dash-card">
            <span style="font-size: 2rem;">🖥️</span>
            <div class="teacher-dash-stat">{{ $classCount ?? 0 }}</div>
            <h3>Classes</h3>
            <p class="sub">Create classes and add students by email.</p>
            <span class="eco-btn">Manage Classes →</span>
        </a>
        <a href="{{ route('teacher.topics.index') }}" class="teacher-dash-card">
            <span style="font-size: 2rem;">📚</span>
            <div class="teacher-dash-stat">{{ $topicCount ?? 0 }}</div>
            <h3>Topics</h3>
            <p class="sub">Organize content by topic. Add quizzes and mini games.</p>
            <span class="eco-btn">Manage Topics →</span>
        </a>
        <a href="{{ route('teacher.quizzes.index') }}" class="teacher-dash-card">
            <span style="font-size: 2rem;">📝</span>
            <div class="teacher-dash-stat">{{ $quizCount ?? 0 }}</div>
            <h3>Quizzes</h3>
            <p class="sub">Create quizzes with multiple choice questions.</p>
            <span class="eco-btn">Manage Quizzes →</span>
        </a>
        <div class="teacher-dash-card" style="cursor: default;">
            <h3 style="display: flex; align-items: center; justify-content: space-between;">Quick Actions</h3>
            <ul class="teacher-quick-actions">
                <li><a href="{{ route('teacher.topics.create') }}">➕ Create Topic</a></li>
                <li><a href="{{ route('teacher.quizzes.create') }}">📝 Create Quiz</a></li>
                <li><a href="{{ route('teacher.mini-games.create') }}">🎮 Create Mini Game</a></li>
                <li><a href="{{ route('teacher.badges.index') }}">🏆 Badges</a></li>
                <li><a href="{{ route('teacher.class-rooms.index') }}">👥 Classes</a></li>
                <li><a href="{{ route('teacher.progress.index') }}">📊 Student Progress</a></li>
            </ul>
        </div>
    </div>

    <div class="teacher-dash-row2">
        <a href="{{ route('teacher.mini-games.index') }}" class="teacher-dash-card">
            <span style="font-size: 1.5rem;">🎮</span>
            <div class="teacher-dash-stat">{{ $miniGameCount ?? 0 }}</div>
            <h3>Mini Games</h3>
            <p class="sub">Create drag-and-drop, multiple choice, or matching games from templates.</p>
            <span class="eco-btn">Manage Mini Games →</span>
        </a>
        <a href="{{ route('teacher.badges.index') }}" class="teacher-dash-card">
            <span style="font-size: 1.5rem;">🏆</span>
            <div class="teacher-dash-stat">{{ $badgeCount ?? 0 }}</div>
            <h3>Badges</h3>
            <p class="sub">Create badges for topics. Students earn them by completing quizzes or games.</p>
            <span class="eco-btn">Manage Badges →</span>
        </a>
        <a href="{{ route('teacher.progress.index') }}" class="teacher-dash-card">
            <span style="font-size: 1.5rem;">📊</span>
            <div class="teacher-dash-stat">{{ $studentCount ?? 0 }}</div>
            <h3>Student Progress</h3>
            <p class="sub">View quiz and game attempts, scores, and badges for each student.</p>
            <span class="eco-btn">View Progress →</span>
        </a>
    </div>

    <div class="teacher-tip-banner">
        <span style="font-size: 1.5rem;">💡</span>
        <p style="margin: 0; font-size: 0.95rem;"><strong>Tip:</strong> Create topics first, then add quizzes and games to them. Create badges so students can earn rewards. Use <strong>← Back to Dashboard</strong> (above) or the header links to move between sections.</p>
    </div>
@endsection
