@extends('layouts.landing')

@section('title', 'How it works')

@section('landing')
    <section class="eco-landing-section eco-landing-how">
        <h2 class="eco-landing-h2">How it works</h2>
        <p class="eco-landing-desc">Get your school on EnviroEdu in three steps.</p>
        <div class="eco-landing-how-grid">
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">1</span>
                <h3>School registers</h3>
                <p>An admin registers the school and gets a unique <strong>school code</strong>.</p>
            </div>
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">2</span>
                <h3>Share the code</h3>
                <p>Share the code with teachers and students so they can join your workspace.</p>
            </div>
            <div class="eco-landing-how-item">
                <span class="eco-landing-how-num">3</span>
                <h3>Approve & learn</h3>
                <p>Admin approves new members; teachers create content and students learn and play.</p>
            </div>
        </div>
    </section>
@endsection
