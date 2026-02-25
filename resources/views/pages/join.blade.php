@extends('layouts.landing')

@section('title', 'Join your school')

@section('landing')
    <section class="eco-landing-section eco-landing-join">
        <h2 class="eco-landing-h2">Join your school</h2>
        <p class="eco-landing-desc">Get your school code from your teacher or school admin, then sign in or create an account.</p>

        <div class="eco-landing-prose">
            <p>If your school is already on EnviroEdu, you need the <strong>school code</strong> to join. The code is created when a school admin registers and can be shared with staff and students. Enter it below to go to registration, or use the role cards further down to log in if you already have an account.</p>
        </div>

        <div class="eco-landing-join-box eco-card">
            <p class="eco-landing-join-label">Have a school code? Enter it here:</p>
            <div class="eco-landing-join-row">
                <input type="text" id="school-code-input" class="eco-input eco-landing-join-input" placeholder="e.g. my-school-2024" maxlength="60" autocomplete="off">
                <div class="eco-landing-join-btns">
                    <a href="{{ route('register', ['role' => 'teacher']) }}" id="join-as-teacher" class="eco-btn eco-btn-join" data-base="{{ route('register', ['role' => 'teacher']) }}">Join as teacher</a>
                    <a href="{{ route('register', ['role' => 'student']) }}" id="join-as-student" class="eco-btn eco-btn-join eco-btn-join-student" data-base="{{ route('register', ['role' => 'student']) }}">Join as student</a>
                </div>
            </div>
            <p class="eco-landing-join-hint">New? You’ll enter the code on the next page. Already have an account? <a href="#login-links">Log in here</a>.</p>
        </div>

        <div class="eco-landing-how-join">
            <h3 class="eco-landing-h3">How to join</h3>
            <ol class="eco-landing-steps">
                <li>Get your <strong>school code</strong> from your school admin or teacher.</li>
                <li>Click <strong>Join as teacher</strong> or <strong>Join as student</strong> above (or use the cards below).</li>
                <li>Register with your name, email, and password, and enter the school code when asked.</li>
                <li>After your school admin approves you, you can log in and use the platform.</li>
            </ol>
        </div>

                <div class="eco-landing-roles" id="login-links">
            <h3 class="eco-landing-h3">Sign in or register by role</h3>
            <p class="eco-landing-desc eco-landing-desc-small">Choose your role to log in or create an account. Teachers and students must enter the school code when registering.</p>
            <div class="eco-landing-role-cards">
                <a href="{{ route('login', ['role' => 'teacher']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👩‍🏫</span>
                    <h3>Teacher</h3>
                    <p>Manage classes, topics & track progress. Register with your school code; your admin will approve your account.</p>
                </a>
                <a href="{{ route('login', ['role' => 'student']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">🎒</span>
                    <h3>Student</h3>
                    <p>Learn & play with quizzes, games & badges. Join with your school code; once approved, you can start learning.</p>
                </a>
                <a href="{{ route('login', ['role' => 'parent']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👨‍👩‍👧</span>
                    <h3>Parent</h3>
                    <p>View your child's progress & badges. You can register without a school code and link to your child from the parent dashboard.</p>
                </a>
            </div>
        </div>

        <div class="eco-landing-prose eco-landing-prose-end">
            <h3 class="eco-landing-h3">Don’t have a school code?</h3>
            <p>If your school isn’t on EnviroEdu yet, ask your principal or admin to <a href="{{ route('register', ['role' => 'admin']) }}">register your school</a> first. They’ll receive the school code to share with you.</p>
        </div>
    </section>

    @push('scripts')
        <script>
            (function() {
                var input = document.getElementById('school-code-input');
                var teacherLink = document.getElementById('join-as-teacher');
                var studentLink = document.getElementById('join-as-student');
                if (!input || !teacherLink || !studentLink) return;
                function updateLinks() {
                    var code = (input.value || '').trim();
                    var qs = code ? '?school_code=' + encodeURIComponent(code) : '';
                    teacherLink.href = teacherLink.getAttribute('data-base') + qs;
                    studentLink.href = studentLink.getAttribute('data-base') + qs;
                }
                input.addEventListener('input', updateLinks);
                input.addEventListener('change', updateLinks);
                if (input.value) updateLinks();
            })();
        </script>
    @endpush
@endsection
