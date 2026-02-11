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
        .eco-topic-progress { background: #e0e0e0; height: 8px; border-radius: 10px; overflow: hidden; margin-top: 0.5rem; }
        .eco-topic-progress-bar { background: var(--eco-primary); height: 100%; transition: width 0.5s; }
        .eco-game-area { flex: 1; min-width: 0; background: #fff; border-radius: 20px; padding: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 2px solid var(--eco-primary); max-height: calc(100vh - 100px); overflow-y: auto; }
        .eco-game-header { font-family: 'Bubblegum Sans', cursive; font-size: 1.6rem; color: var(--eco-accent); margin-bottom: 1rem; text-align: center; }
        .eco-game-content { flex: 1; display: flex; flex-direction: column; justify-content: flex-start; align-items: center; gap: 1.5rem; overflow-y: auto; }
        .eco-badge-count { background: var(--eco-secondary); padding: 0.5rem 1.5rem; border-radius: 25px; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(255, 230, 109, 0.4); }
        .eco-grade-select { background: white; border: 3px solid var(--eco-primary); padding: 0.5rem 1rem; border-radius: 20px; font-family: 'Quicksand', sans-serif; font-weight: 700; font-size: 1rem; cursor: pointer; }
        .eco-drop-zone { background: #f0f4f3; border: 4px dashed var(--eco-primary); border-radius: 15px; padding: 1rem; min-width: 160px; min-height: 140px; transition: all 0.3s; }
        .eco-drop-zone.drag-over { background: rgba(78, 205, 196, 0.2); border-color: var(--eco-green); transform: scale(1.05); }
        .eco-draggable-item { background: white; border: 3px solid var(--eco-secondary); border-radius: 12px; padding: 0.8rem 1.2rem; cursor: grab; font-weight: 600; transition: all 0.3s; }
        .eco-option { background: white; border: 3px solid var(--eco-secondary); border-radius: 12px; padding: 0.8rem; cursor: pointer; transition: all 0.3s; font-weight: 600; }
        .eco-option:hover { transform: translateX(10px); background: var(--eco-secondary); }
        .eco-option.correct { background: var(--eco-green); border-color: var(--eco-green); }
        .eco-option.incorrect { background: var(--eco-accent); border-color: var(--eco-accent); }
        .eco-feedback { position: fixed; top: 100px; right: 2rem; background: white; padding: 1rem 1.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: translateX(400px); transition: transform 0.3s; z-index: 100; }
        .eco-feedback.show { transform: translateX(0); }
        .eco-feedback.success { border-left: 5px solid var(--eco-green); }
        .eco-feedback.error { border-left: 5px solid var(--eco-accent); }
        .eco-badge-modal { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0); background: white; border-radius: 25px; padding: 2rem; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3); z-index: 1000; transition: transform 0.3s; max-width: 400px; border: 4px solid var(--eco-primary); }
        .eco-badge-modal.show { transform: translate(-50%, -50%) scale(1); }
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
                <select class="eco-grade-select" id="ecoGradeSelector">
                    <option value="4">Grade 4</option>
                    <option value="5">Grade 5</option>
                </select>
                <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="eco-logout-btn">Logout</button>
                </form>
            </div>
        </header>

        <div class="eco-student-main">
            <aside class="eco-topics-panel">
                <h2>Choose Your Topic!</h2>
                <div id="ecoTopicsList"></div>
            </aside>
            <main class="eco-game-area">
                <h2 class="eco-game-header" id="ecoGameHeader">Select a topic to start!</h2>
                <div class="eco-game-content" id="ecoGameContent">
                    <div style="text-align: center; color: #666;">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">🎮</div>
                        <p style="font-size: 1.2rem;">Pick a topic from the left to begin your adventure!</p>
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
@endsection

@push('scripts')
    @vite(['resources/js/eco-student.js'])
@endpush
