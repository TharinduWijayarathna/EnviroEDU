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
        /* Kid-friendly student layout */
        .eco-kid-header .eco-student-logo-wrap { display: flex; align-items: center; gap: 0.75rem; }
        .eco-student-logo-img { height: 52px; width: auto; object-fit: contain; display: block; }
        .eco-kid-header .eco-student-user-name { font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin: 0; }
        .eco-dashboard-nav { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
        .eco-grade-form { display: inline-flex; align-items: center; gap: 0.5rem; }
        .eco-grade-label { font-weight: 700; font-size: 0.95rem; color: var(--eco-dark); }
        .eco-grade-select { background: #b3e5fc; border: 2px solid #4dd0e1; padding: 0.5rem 1rem; border-radius: 25px; font-weight: 700; cursor: pointer; font-size: 1rem; }
        .eco-logout-form { display: inline; }
        .eco-logout-btn { background: transparent; border: 2px solid #b0bec5; color: #546e7a; padding: 0.4rem 0.9rem; border-radius: 16px; font-weight: 600; font-size: 0.9rem; cursor: pointer; }
        .eco-logout-btn:hover { background: #eceff1; }
        .eco-kid-panel-title { font-family: 'Bubblegum Sans', cursive; font-size: 1.2rem; color: var(--eco-primary); margin: 0 0 0.5rem; margin-top: 1rem; }
        .eco-kid-panel-title:first-child { margin-top: 0; }
        .eco-kid-subtitle { font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--eco-dark); }
        .eco-kid-empty { font-size: 1rem; color: #666; margin: 0; line-height: 1.4; }
        .eco-topic-card-block { display: block; }
        .eco-student-main.eco-student-fullwidth { max-width: none; width: 100%; }
        .eco-student-main.eco-student-fullwidth .eco-game-area { flex: 1; width: 100%; max-width: none; pointer-events: auto; isolation: isolate; position: relative; min-height: 0; }
        .eco-student-back-bar { margin-bottom: 1rem; }
        .eco-student-back-link { font-weight: 700; font-size: 1.1rem; color: var(--eco-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; }
        .eco-student-back-link:hover { text-decoration: underline; }
    </style>
@endpush

@section('content')
    <div class="eco-student-wrap has-bg-image">
        <header class="eco-student-header eco-kid-header">
            <div class="eco-student-logo-wrap">
                <img src="{{ asset('images/logo.png') }}" alt="{{ __('messages.common.app_name') }}" class="eco-student-logo-img">
                @auth
                    <span class="eco-student-user-name">Hi, {{ \Illuminate\Support\Str::before(auth()->user()->name ?? 'Friend', ' ') }}! 👋</span>
                @endauth
            </div>
            <div class="eco-dashboard-nav">
                @include('components.language-switcher')
                <a href="{{ route('dashboard.student.badges') }}" class="eco-badge-btn">🏆 <span id="ecoBadgeCount">{{ $badgeCount ?? 0 }}</span> {{ __('messages.dashboard.badges') }}</a>
                <form method="GET" action="{{ route('dashboard.student') }}" id="ecoGradeForm" class="eco-grade-form">
                    <label for="ecoGradeSelector" class="eco-grade-label">{{ __('messages.dashboard.my_grade') }}</label>
                    <select id="ecoGradeSelector" name="grade" class="eco-grade-select" aria-label="{{ __('messages.dashboard.my_grade') }}">
                        <option value="">{{ __('messages.dashboard.grade_all') }}</option>
                        @foreach (config('app.grade_levels', [4, 5]) as $g)
                            <option value="{{ $g }}" {{ (isset($grade) && $grade == $g) ? 'selected' : '' }}>{{ __('messages.dashboard.grade', ['level' => $g]) }}</option>
                        @endforeach
                    </select>
                </form>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="eco-logout-form">
                        @csrf
                        <button type="submit" class="eco-logout-btn">{{ __('messages.dashboard.leave') }}</button>
                    </form>
                @endauth
            </div>
        </header>

        <div class="eco-student-main eco-student-fullwidth">
            <main class="eco-game-area">
                @yield('student-main')
            </main>
        </div>
    </div>

    @if (($studentPage ?? '') === 'dashboard')
        <button type="button" class="eco-edubuddy-toggle" id="ecoEdubuddyToggle" aria-label="Open EduBuddy chat">🤖</button>
        <div class="eco-edubuddy-widget" id="ecoEdubuddy" aria-hidden="true">
            <div class="eco-edubuddy-header">
                <span class="eco-edubuddy-title">🤖 {{ __('messages.dashboard.edubuddy') }}</span>
                <button type="button" class="eco-edubuddy-close" id="ecoEdubuddyClose" aria-label="Close chat">×</button>
            </div>
            <div class="eco-edubuddy-body">
                <p class="eco-edubuddy-greeting" id="ecoEdubuddyGreeting">{{ __('messages.dashboard.edubuddy_greeting', ['name' => auth()->user()->name ?? 'there']) }}</p>
                <div class="eco-edubuddy-suggestions" id="ecoEdubuddySuggestions">
                    <button type="button" class="eco-edubuddy-suggestion" data-message="{{ __('messages.dashboard.edubuddy_suggestion_1') }} 😕">{{ __('messages.dashboard.edubuddy_suggestion_1') }} 😕 →</button>
                    <button type="button" class="eco-edubuddy-suggestion" data-message="{{ __('messages.dashboard.edubuddy_suggestion_2') }} 🌧️">{{ __('messages.dashboard.edubuddy_suggestion_2') }} 🌧️ →</button>
                    <button type="button" class="eco-edubuddy-suggestion" data-message="{{ __('messages.dashboard.edubuddy_suggestion_3') }} 📝">{{ __('messages.dashboard.edubuddy_suggestion_3') }} 📝 →</button>
                </div>
                <div class="eco-edubuddy-messages" id="ecoEdubuddyMessages"></div>
                <div class="eco-edubuddy-typing" id="ecoEdubuddyTyping" style="display: none;">{{ __('messages.dashboard.edubuddy_typing') }}</div>
            </div>
            <div class="eco-edubuddy-footer">
                <input type="text" class="eco-edubuddy-input" placeholder="{{ __('messages.dashboard.edubuddy_placeholder') }}" id="ecoEdubuddyInput" autocomplete="off">
                <button type="button" class="eco-edubuddy-send" id="ecoEdubuddySend" aria-label="Send">🤖</button>
            </div>
        </div>
        <script>
            (function() {
                var toggle = document.getElementById('ecoEdubuddyToggle');
                var widget = document.getElementById('ecoEdubuddy');
                var closeBtn = document.getElementById('ecoEdubuddyClose');
                var greeting = document.getElementById('ecoEdubuddyGreeting');
                var suggestions = document.getElementById('ecoEdubuddySuggestions');
                var messagesEl = document.getElementById('ecoEdubuddyMessages');
                var typingEl = document.getElementById('ecoEdubuddyTyping');
                var input = document.getElementById('ecoEdubuddyInput');
                var sendBtn = document.getElementById('ecoEdubuddySend');
                var chatUrl = @json(route('edubuddy.chat'));
                var csrf = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrf ? csrf.getAttribute('content') : '';

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

                function escapeHtml(text) {
                    var div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                function appendMessage(text, isUser) {
                    if (greeting) greeting.style.display = 'none';
                    if (suggestions) suggestions.style.display = 'none';
                    var wrap = document.createElement('div');
                    wrap.className = isUser ? 'eco-edubuddy-msg eco-edubuddy-msg-user' : 'eco-edubuddy-msg eco-edubuddy-msg-bot';
                    var p = document.createElement('p');
                    p.textContent = text;
                    wrap.appendChild(p);
                    messagesEl.appendChild(wrap);
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                }

                function setTyping(show) {
                    typingEl.style.display = show ? 'block' : 'none';
                    if (show) messagesEl.scrollTop = messagesEl.scrollHeight;
                }

                function sendMessage(messageText) {
                    var text = (messageText || (input && input.value)).trim();
                    if (!text) return;
                    if (input) input.value = '';
                    appendMessage(text, true);
                    setTyping(true);
                    fetch(chatUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ message: text })
                    })
                    .then(function(r) {
                        var contentType = r.headers.get('Content-Type') || '';
                        if (!contentType.includes('application/json')) {
                            return r.text().then(function(t) { throw new Error('Server did not return JSON. Try again.'); });
                        }
                        return r.json().then(function(data) {
                            if (!r.ok) {
                                var msg = (data && data.message) ? data.message : (data && data.reply) ? data.reply : 'Something went wrong. Try again!';
                                throw new Error(msg);
                            }
                            return data;
                        });
                    })
                    .then(function(data) {
                        setTyping(false);
                        appendMessage((data && data.reply) ? data.reply : 'Sorry, I couldn\'t reply. Try again!', false);
                    })
                    .catch(function(err) {
                        setTyping(false);
                        appendMessage(err && err.message ? err.message : 'Something went wrong. Please try again in a moment! 😊', false);
                    });
                }

                if (sendBtn) sendBtn.addEventListener('click', function() { sendMessage(); });
                if (input) {
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') { e.preventDefault(); sendMessage(); }
                    });
                }
                if (suggestions) {
                    suggestions.querySelectorAll('.eco-edubuddy-suggestion').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            var msg = this.getAttribute('data-message');
                            if (msg) sendMessage(msg);
                        });
                    });
                }
            })();
        </script>
    @endif
    @if (($studentPage ?? '') === 'dashboard')
        <script>window.ecoStudentData = { topics: @json($topicsPayload ?? []) };</script>
    @endif

    <div class="eco-badge-modal" id="ecoBadgeModal" data-default-title="{{ __('messages.dashboard.default_badge_name') }}" data-default-desc="{{ __('messages.dashboard.congratulations') }}">
        <div class="eco-badge-modal-ribbon" id="ecoBadgeRibbon">{{ __('messages.dashboard.you_got_badge') }} 🎉</div>
        <div style="font-size: 4rem; margin-bottom: 0.8rem; min-height: 4rem; display: flex; align-items: center; justify-content: center;" id="ecoBadgeIcon">🏆</div>
        <div class="eco-badge-modal-badge-title" id="ecoBadgeTitle"></div>
        <p id="ecoBadgeDescription" style="color:#333; margin:0.75rem 0;"></p>
        <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
            <button type="button" class="eco-btn" id="ecoCloseBadgeBtn" style="background: #fff8e1; color: #558b2f; border: 2px solid #81c784;">{{ __('messages.common.close') }}</button>
            <a href="{{ route('dashboard.student.badges') }}" class="eco-btn" id="ecoViewAllBadgesBtn">{{ __('messages.dashboard.see_my_badges') }}</a>
        </div>
    </div>
    <script>
        window.ecoShowBadgeModal = function(badge) {
            var modal = document.getElementById('ecoBadgeModal');
            var icon = document.getElementById('ecoBadgeIcon');
            var title = document.getElementById('ecoBadgeTitle');
            var desc = document.getElementById('ecoBadgeDescription');
            var defaultTitle = modal ? modal.getAttribute('data-default-title') || 'Forest Guardian' : 'Forest Guardian';
            var defaultDesc = modal ? modal.getAttribute('data-default-desc') || 'Congratulations! You\'ve earned this badge.' : 'Congratulations! You\'ve earned this badge.';
            if (modal && icon && title && desc) {
                if (badge.image_url) {
                    icon.innerHTML = '<img src="' + badge.image_url + '" alt="" style="width:80px;height:80px;object-fit:contain;border-radius:12px;">';
                } else {
                    icon.textContent = badge.icon || '🏆';
                }
                title.textContent = badge.name || defaultTitle;
                desc.textContent = badge.description || defaultDesc;
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
