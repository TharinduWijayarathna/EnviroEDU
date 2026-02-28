@extends('layouts.landing')

@section('title', __('messages.join.title'))

@section('landing')
    <section class="eco-landing-section eco-landing-join">
        <h2 class="eco-landing-h2">{{ __('messages.join.headline') }}</h2>
        <p class="eco-landing-desc">{{ __('messages.join.desc') }}</p>

        <div class="eco-landing-prose">
            <p>{!! __('messages.join.prose') !!}</p>
        </div>

        <div class="eco-landing-join-box eco-card">
            <p class="eco-landing-join-label">{{ __('messages.join.have_code') }}</p>
            <div class="eco-landing-join-row">
                <input type="text" id="school-code-input" class="eco-input eco-landing-join-input" placeholder="{{ __('messages.auth.school_code_placeholder') }}" maxlength="60" autocomplete="off">
                <div class="eco-landing-join-btns">
                    <a href="{{ route('register', ['role' => 'teacher']) }}" id="join-as-teacher" class="eco-btn eco-btn-join" data-base="{{ route('register', ['role' => 'teacher']) }}">{{ __('messages.join.join_as_teacher') }}</a>
                    <a href="{{ route('register', ['role' => 'student']) }}" id="join-as-student" class="eco-btn eco-btn-join eco-btn-join-student" data-base="{{ route('register', ['role' => 'student']) }}">{{ __('messages.join.join_as_student') }}</a>
                    <a href="{{ route('register', ['role' => 'parent']) }}" class="eco-btn eco-btn-join eco-btn-join-parent">{{ __('messages.join.join_as_parent') }}</a>
                </div>
            </div>
            <p class="eco-landing-join-hint">{{ __('messages.join.new_enter_code') }} <a href="#login-links">{{ __('messages.join.log_in_here') }}</a>.</p>
        </div>

        <div class="eco-landing-how-join">
            <h3 class="eco-landing-h3">{{ __('messages.join.how_to_join') }}</h3>
            <ol class="eco-landing-steps">
                <li>{!! __('messages.join.step1') !!}</li>
                <li>{!! __('messages.join.step2') !!}</li>
                <li>{{ __('messages.join.step3') }}</li>
                <li>{{ __('messages.join.step4') }}</li>
            </ol>
        </div>

        <div class="eco-landing-roles" id="login-links">
            <h3 class="eco-landing-h3">{{ __('messages.join.sign_in_by_role') }}</h3>
            <p class="eco-landing-desc eco-landing-desc-small">{{ __('messages.join.sign_in_by_role_desc') }}</p>
            <div class="eco-landing-role-cards">
                <a href="{{ route('login.role', ['role' => 'teacher']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👩‍🏫</span>
                    <h3>{{ __('messages.auth.teacher') }}</h3>
                    <p>{{ __('messages.join.teacher_join_desc') }}</p>
                </a>
                <a href="{{ route('login.role', ['role' => 'student']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">🎒</span>
                    <h3>{{ __('messages.auth.student') }}</h3>
                    <p>{{ __('messages.join.student_join_desc') }}</p>
                </a>
                <a href="{{ route('login.role', ['role' => 'parent']) }}" class="eco-card eco-role-card eco-landing-role-card">
                    <span class="eco-role-icon">👨‍👩‍👧</span>
                    <h3>{{ __('messages.auth.parent') }}</h3>
                    <p>{{ __('messages.join.parent_join_desc') }}</p>
                </a>
            </div>
        </div>

        <div class="eco-landing-prose eco-landing-prose-end">
            <h3 class="eco-landing-h3">{{ __('messages.join.no_school_code') }}</h3>
            <p>{!! __('messages.join.no_school_code_desc', ['url' => route('register', ['role' => 'admin'])]) !!}</p>
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
