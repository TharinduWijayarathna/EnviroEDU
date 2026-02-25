@extends('layouts.landing')

@section('title', 'How it works')

@section('landing')
    <section class="eco-landing-section eco-landing-how">
        <h2 class="eco-landing-h2">How it works</h2>
        <p class="eco-landing-desc">Get your school on EnviroEdu in three steps. Each school has one workspace; only people you approve can join.</p>

        <div class="eco-landing-prose">
            <p>EnviroEdu is built around a simple flow: the school registers first, then teachers and students join using a shared code. The school admin controls who gets access by approving each new account. No one from another school can see your data.</p>
        </div>

        <div class="eco-landing-how-grid">
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">1</span>
                <h3>School registers</h3>
                <p>A school admin signs up and enters the school name and a unique <strong>school code</strong>. The code is like a password for your workspace—only people with this code can request to join your school.</p>
                <p class="eco-landing-how-note eco-callout">Keep the code safe and share it only with your teachers and students.</p>
            </div>
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">2</span>
                <h3>Share the code</h3>
                <p>Share the school code with teachers and students. They go to the <a href="{{ route('landing.join') }}">Join your school</a> page, enter the code, and register with their name and email. Until you approve them, they cannot use the platform.</p>
                <p class="eco-landing-how-note eco-callout">Teachers and students both use the same code; their role is chosen during registration.</p>
            </div>
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">3</span>
                <h3>Approve & learn</h3>
                <p>In the school admin dashboard, you’ll see a list of pending teachers and students. Approve each person when ready. Once approved, they can log in: teachers can create classes and content, students can join classes and complete quizzes and games.</p>
                <p class="eco-landing-how-note eco-callout">Parents can register separately and link to their child; they don’t need to be approved by the school.</p>
            </div>
        </div>

        <div class="eco-landing-prose eco-landing-prose-end">
            <h3 class="eco-landing-h3">What happens next</h3>
            <p>After approval, teachers set up classes and add topics, quizzes, and games. Students are enrolled in classes and can start learning. Progress and badges are tracked automatically. If you haven’t registered yet, <a href="{{ route('register', ['role' => 'admin']) }}">register your school</a> to get your code and dashboard.</p>
        </div>

        <div class="eco-home-cta eco-landing-cta">
            <p class="eco-home-cta-text">Ready to get started?</p>
            <div class="eco-home-actions">
                <a href="{{ route('register', ['role' => 'admin']) }}" class="eco-btn eco-btn-hero">Register your school</a>
                <a href="{{ route('landing.join') }}" class="eco-btn eco-btn-outline">Join your school</a>
            </div>
        </div>
    </section>
@endsection
