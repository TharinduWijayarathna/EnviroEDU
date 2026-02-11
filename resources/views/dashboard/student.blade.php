@extends('layouts.app')

@section('title', 'Student Dashboard')

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .eco-student-main { display: flex; gap: 1.5rem; padding: 1.5rem; flex: 1; min-height: 0; max-width: 1400px; margin: 0 auto; width: 100%; align-items: flex-start; }
        .eco-topics-panel { background: #fff; border-radius: 20px; padding: 1.5rem; width: 300px; flex-shrink: 0; overflow-y: auto; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 2px solid var(--eco-primary); max-height: calc(100vh - 100px); }
        .eco-topics-panel h2 { font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: var(--eco-primary); margin-bottom: 1rem; text-align: center; }
        .eco-topic-card { background: #fff; border: 3px solid var(--eco-primary); border-radius: 15px; padding: 1rem; margin-bottom: 0.8rem; cursor: pointer; transition: all 0.3s; }
        .eco-topic-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(78, 205, 196, 0.3); }
        .eco-topic-icon { font-size: 2rem; margin-bottom: 0.3rem; }
        .eco-topic-title { font-weight: 700; font-size: 1.1rem; color: var(--eco-dark); margin-bottom: 0.5rem; }
        .eco-game-area { flex: 1; min-width: 0; background: #fff; border-radius: 20px; padding: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 2px solid var(--eco-primary); max-height: calc(100vh - 100px); overflow-y: auto; }
        .eco-game-header { font-family: 'Bubblegum Sans', cursive; font-size: 1.6rem; color: var(--eco-accent); margin-bottom: 1rem; text-align: center; }
        .eco-game-content { flex: 1; display: flex; flex-direction: column; justify-content: flex-start; align-items: center; gap: 1.5rem; overflow-y: auto; }
        .eco-badge-count { background: var(--eco-secondary); padding: 0.5rem 1.5rem; border-radius: 25px; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(255, 230, 109, 0.4); }
        .eco-grade-select { background: white; border: 3px solid var(--eco-primary); padding: 0.5rem 1rem; border-radius: 20px; font-family: 'Quicksand', sans-serif; font-weight: 700; font-size: 1rem; cursor: pointer; }
        .eco-feedback { position: fixed; top: 100px; right: 2rem; background: white; padding: 1rem 1.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: translateX(400px); transition: transform 0.3s; z-index: 100; }
        .eco-feedback.show { transform: translateX(0); }
        .eco-feedback.success { border-left: 5px solid var(--eco-green); }
        .eco-feedback.error { border-left: 5px solid var(--eco-accent); }
        .eco-badge-modal { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0); background: white; border-radius: 25px; padding: 2rem; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3); z-index: 1000; transition: transform 0.3s; max-width: 400px; border: 4px solid var(--eco-primary); }
        .eco-badge-modal.show { transform: translate(-50%, -50%) scale(1); }
        .eco-video-embed { max-width: 100%; border-radius: 15px; overflow: hidden; background: #2C3E50; aspect-ratio: 16/9; }
        @media (max-width: 968px) { .eco-student-main { flex-direction: column; } .eco-topics-panel { width: 100%; max-height: 280px; } }
    </style>
@endpush

@section('content')
    <div style="display: flex; flex-direction: column; min-height: 100vh; background: #f5f7f6;">
        <header class="eco-header" style="background: #fff; border-bottom: 2px solid var(--eco-primary);">
            <a href="{{ route('dashboard.student') }}" class="eco-logo">
                <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height: 48px; width: auto; object-fit: contain;">
            </a>
            <div class="eco-dashboard-nav" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div class="eco-badge-count">🏆 <span id="ecoBadgeCount">0</span> Badges</div>
                <form method="GET" action="{{ route('dashboard.student') }}" id="ecoGradeForm" style="display: inline;">
                    <select class="eco-grade-select" id="ecoGradeSelector" name="grade">
                        <option value="">All grades</option>
                        @for ($g = 1; $g <= 12; $g++)
                            <option value="{{ $g }}" {{ (isset($grade) && $grade == $g) ? 'selected' : '' }}>Grade {{ $g }}</option>
                        @endfor
                    </select>
                </form>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">Logout</button>
                </form>
            </div>
        </header>

        <div class="eco-student-main">
            <aside class="eco-topics-panel">
                <h2>Topics</h2>
                <div id="ecoTopicsList"></div>
                <h2 style="margin-top: 1.5rem;">Quizzes & Games (standalone)</h2>
                @if (isset($standaloneQuizzes) && $standaloneQuizzes->isNotEmpty())
                    <p style="font-size: 0.9rem; margin-bottom: 0.5rem;">Quizzes</p>
                    @foreach ($standaloneQuizzes as $quiz)
                        <a href="{{ route('play.quiz', $quiz) }}" class="eco-topic-card" style="display: block; text-decoration: none; color: inherit;" target="_blank">
                            <div class="eco-topic-icon">📝</div>
                            <div class="eco-topic-title">{{ $quiz->title }}</div>
                        </a>
                    @endforeach
                @endif
                @if (isset($standaloneMiniGames) && $standaloneMiniGames->isNotEmpty())
                    <p style="font-size: 0.9rem; margin-bottom: 0.5rem; margin-top: 1rem;">Mini Games</p>
                    @foreach ($standaloneMiniGames as $game)
                        <a href="{{ route('play.mini-game', $game) }}" class="eco-topic-card" style="display: block; text-decoration: none; color: inherit;" target="_blank">
                            <div class="eco-topic-icon">🎮</div>
                            <div class="eco-topic-title">{{ $game->title }}</div>
                        </a>
                    @endforeach
                @endif
                @if ((!isset($standaloneQuizzes) || $standaloneQuizzes->isEmpty()) && (!isset($standaloneMiniGames) || $standaloneMiniGames->isEmpty()) && (!isset($topics) || $topics->isEmpty()))
                    <p style="font-size: 0.9rem; color: #666;">No topics, quizzes or games yet. Your teacher will add some!</p>
                @elseif ((!isset($standaloneQuizzes) || $standaloneQuizzes->isEmpty()) && (!isset($standaloneMiniGames) || $standaloneMiniGames->isEmpty()))
                    <p style="font-size: 0.9rem; color: #666;">No standalone quizzes or games.</p>
                @endif
            </aside>
            <main class="eco-game-area">
                <h2 class="eco-game-header" id="ecoGameHeader">Select a topic to start!</h2>
                <div class="eco-game-content" id="ecoGameContent">
                    <div style="text-align: center; color: #666;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
                        <p style="font-size: 1.2rem;">Pick a topic from the left, watch the video lesson (if any), then play the quiz or game!</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="eco-badge-modal" id="ecoBadgeModal">
        <div style="font-size: 4rem; margin-bottom: 0.8rem;" id="ecoBadgeIcon">🏆</div>
        <div style="font-family: 'Bubblegum Sans', cursive; font-size: 1.6rem; color: var(--eco-primary); margin-bottom: 0.8rem;" id="ecoBadgeTitle">Badge Earned!</div>
        <p id="ecoBadgeDescription"></p>
        <button type="button" class="eco-btn" id="ecoCloseBadgeBtn">Awesome!</button>
    </div>

    <div class="eco-feedback" id="ecoFeedback">
        <p id="ecoFeedbackText"></p>
    </div>

    <script>
        window.ecoStudentData = { topics: @json($topicsPayload ?? []) };
    </script>
@endsection

@push('scripts')
    @vite(['resources/js/eco-student.js'])
@endpush
