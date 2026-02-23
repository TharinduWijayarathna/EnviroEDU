@extends('layouts.app')

@section('title', $studentLayoutTitle ?? config('app.name'))

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        :root { --eco-student-bg-image: url('{{ asset("images/student-dashboard-bg.jpeg") }}'); }
        .eco-feedback { position: fixed; top: 100px; right: 2rem; background: white; padding: 1rem 1.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: translateX(400px); transition: transform 0.3s; z-index: 100; }
        .eco-feedback.show { transform: translateX(0); }
        .eco-feedback.success { border-left: 5px solid var(--eco-green); }
        .eco-feedback.error { border-left: 5px solid var(--eco-accent); }
        .eco-video-embed { max-width: 100%; border-radius: 15px; overflow: hidden; background: #2C3E50; aspect-ratio: 16/9; }
    </style>
@endpush

@section('content')
    <div class="eco-student-wrap has-bg-image">
        <header class="eco-student-header">
            <div class="eco-student-logo-wrap" style="display: flex; align-items: center;">
                <div class="">
                    <img src="{{ asset('images/logo.png') }}" alt="EnviroEdu" style="height:60px;width:auto;object-fit:contain;display:block;">
                </div>
                @auth
                    <span class="eco-student-user-name" style="margin-left: 1rem;">{{ auth()->user()->name }}</span>
                @endauth
            </div>
            <div class="eco-dashboard-nav" style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('dashboard.student') }}#badges" class="eco-badge-btn">🏆 <span id="ecoBadgeCount">{{ $badgeCount ?? 0 }}</span> Badges</a>
                <form method="GET" action="{{ route('dashboard.student') }}" id="ecoGradeForm" style="display: inline;">
                    <select id="ecoGradeSelector" name="grade" class="eco-grade-select" style="background:#b3e5fc; border:2px solid #4dd0e1; padding:0.5rem 1rem; border-radius:25px; font-weight:700; cursor:pointer;">
                        <option value="">All grades</option>
                        @foreach (config('app.grade_levels', [4, 5]) as $g)
                            <option value="{{ $g }}" {{ (isset($grade) && $grade == $g) ? 'selected' : '' }}>Grade {{ $g }}</option>
                        @endforeach
                    </select>
                </form>
                @auth
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="eco-logout-btn">Logout</button>
                    </form>
                @endauth
            </div>
        </header>

        <div class="eco-student-main">
            <aside class="eco-sidebar-nav">
                <a href="{{ route('dashboard.student') }}" class="eco-nav-signpost topics">
                    <span class="nav-icon">💡</span>
                    <span>Topics</span>
                    <span class="nav-arrow">→</span>
                </a>
                <a href="{{ route('dashboard.student') }}#quizzes" class="eco-nav-signpost quizzes-games">
                    <span class="nav-icon">🎮</span>
                    <span>Quizzes & Games</span>
                    <span class="nav-arrow">→</span>
                </a>
                <div class="eco-topics-panel">
                    <h2>🏆 Topics</h2>
                    <div id="ecoTopicsList">
                        @php $studentPage = $studentPage ?? 'dashboard'; @endphp
                        @foreach ($topics ?? [] as $i => $t)
                            @if ($studentPage === 'play')
                                <a href="{{ route('dashboard.student') }}" class="eco-topic-card">
                                    <div class="eco-topic-icon">📚</div>
                                    <div class="eco-topic-title">{{ $t->title }}</div>
                                </a>
                            @else
                                <div class="eco-topic-card" data-index="{{ $i }}" style="cursor: pointer;">
                                    <div class="eco-topic-icon">📚</div>
                                    <div class="eco-topic-title">{{ $t->title }}</div>
                                </div>
                            @endif
                        @endforeach
                        @if (empty($topics) || $topics->isEmpty())
                            <p style="font-size: 0.9rem; color: #666;">No topics yet. Check back later or try another grade!</p>
                        @endif
                    </div>
                    <h2 style="margin-top: 1rem;">Quizzes & Games</h2>
                    @if (isset($standaloneQuizzes) && $standaloneQuizzes->isNotEmpty())
                        <p style="font-size: 0.9rem; margin-bottom: 0.5rem;">Quizzes</p>
                        @foreach ($standaloneQuizzes as $quiz)
                            <a href="{{ route('play.quiz', $quiz) }}" class="eco-topic-card">
                                <div class="eco-topic-icon">📝</div>
                                <div class="eco-topic-title">{{ $quiz->title }}</div>
                            </a>
                        @endforeach
                    @endif
                    @if (isset($standaloneMiniGames) && $standaloneMiniGames->isNotEmpty())
                        <p style="font-size: 0.9rem; margin-bottom: 0.5rem; margin-top: 0.75rem;">Mini Games</p>
                        @foreach ($standaloneMiniGames as $game)
                            <div class="eco-topic-card" style="display: block;">
                                <div class="eco-topic-icon">🎮</div>
                                <div class="eco-topic-title">{{ $game->title }}</div>
                                <a href="{{ route('play.mini-game', $game) }}" class="eco-play-game-btn">Play Game →</a>
                            </div>
                        @endforeach
                    @endif
                    @if ((!isset($standaloneQuizzes) || $standaloneQuizzes->isEmpty()) && (!isset($standaloneMiniGames) || $standaloneMiniGames->isEmpty()) && (!isset($topics) || $topics->isEmpty()))
                        <p style="font-size: 0.9rem; color: #666;">No quizzes or games yet.</p>
                    @elseif ((!isset($standaloneQuizzes) || $standaloneQuizzes->isEmpty()) && (!isset($standaloneMiniGames) || $standaloneMiniGames->isEmpty()))
                        <p style="font-size: 0.9rem; color: #666;">No standalone quizzes or games.</p>
                    @endif
                </div>
            </aside>
            <main class="eco-game-area" style="position: relative;">
                @yield('student-main')
            </main>
        </div>
    </div>

    @if (($studentPage ?? '') === 'dashboard')
        <button type="button" class="eco-edubuddy-toggle" id="ecoEdubuddyToggle" aria-label="Open EduBuddy chat">🤖</button>
        <div class="eco-edubuddy-widget" id="ecoEdubuddy" aria-hidden="true">
            <div class="eco-edubuddy-header">
                <span class="eco-edubuddy-title">🤖 EduBuddy</span>
                <button type="button" class="eco-edubuddy-close" id="ecoEdubuddyClose" aria-label="Close chat">×</button>
            </div>
            <div class="eco-edubuddy-body">
                <p class="eco-edubuddy-greeting">Hi {{ auth()->user()->name ?? 'there' }}! How can I help you today?</p>
                <div class="eco-edubuddy-suggestions">
                    <a href="{{ route('dashboard.student') }}#quizzes" class="eco-edubuddy-suggestion">What is living vs non-living? 😕 →</a>
                    <a href="{{ route('dashboard.student') }}#quizzes" class="eco-edubuddy-suggestion">Explain the water cycle 🌧️ →</a>
                    <a href="{{ route('dashboard.student') }}#quizzes" class="eco-edubuddy-suggestion">I need a hint for my quiz! 📝 →</a>
                </div>
            </div>
            <div class="eco-edubuddy-footer">
                <input type="text" class="eco-edubuddy-input" placeholder="Type a message..." id="ecoEdubuddyInput">
                <button type="button" class="eco-edubuddy-send" aria-label="Send">🤖</button>
            </div>
        </div>
        <script>
            (function() {
                var toggle = document.getElementById('ecoEdubuddyToggle');
                var widget = document.getElementById('ecoEdubuddy');
                var closeBtn = document.getElementById('ecoEdubuddyClose');
                if (!toggle || !widget) return;
                function openChat() {
                    widget.classList.add('is-open');
                    widget.setAttribute('aria-hidden', 'false');
                    toggle.classList.add('is-open');
                }
                function closeChat() {
                    widget.classList.remove('is-open');
                    widget.setAttribute('aria-hidden', 'true');
                    toggle.classList.remove('is-open');
                }
                toggle.addEventListener('click', openChat);
                closeBtn && closeBtn.addEventListener('click', closeChat);
            })();
        </script>
    @endif
    @if (($studentPage ?? '') === 'dashboard')
        <script>window.ecoStudentData = { topics: @json($topicsPayload ?? []) };</script>
    @endif

    <div class="eco-badge-modal" id="ecoBadgeModal">
        <div class="eco-badge-modal-ribbon" id="ecoBadgeRibbon">Badge Achieved!</div>
        <div style="font-size: 4rem; margin-bottom: 0.8rem;" id="ecoBadgeIcon">🏆</div>
        <div class="eco-badge-modal-badge-title" id="ecoBadgeTitle">Forest Guardian</div>
        <p id="ecoBadgeDescription" style="color:#333; margin:0.75rem 0;"></p>
        <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
            <button type="button" class="eco-btn" id="ecoCloseBadgeBtn" style="background: #fff8e1; color: #558b2f; border: 2px solid #81c784;">Close</button>
            <a href="{{ route('dashboard.student') }}#badges" class="eco-btn" id="ecoViewAllBadgesBtn">View All Badges</a>
        </div>
    </div>
    <script>
        window.ecoShowBadgeModal = function(badge) {
            var modal = document.getElementById('ecoBadgeModal');
            var icon = document.getElementById('ecoBadgeIcon');
            var title = document.getElementById('ecoBadgeTitle');
            var desc = document.getElementById('ecoBadgeDescription');
            if (modal && icon && title && desc) {
                icon.textContent = badge.icon || '🏆';
                title.textContent = badge.name || 'Forest Guardian';
                desc.textContent = badge.description || 'Congratulations! You\'ve earned this badge.';
                modal.classList.add('show');
                var countEl = document.getElementById('ecoBadgeCount');
                if (countEl) { countEl.textContent = parseInt(countEl.textContent, 10) + 1; }
            }
        };
    </script>

    <div class="eco-feedback" id="ecoFeedback">
        <p id="ecoFeedbackText"></p>
    </div>
@endsection
