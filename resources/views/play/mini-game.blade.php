@extends('layouts.student')

@section('title', $miniGame->title)

@push('styles')
    @vite(['resources/css/eco.css'])
    <style>
        .play-game-container { max-width: 960px; margin: 0 auto; width: 100%; }
        #game-mount { min-height: 520px; }
        .play-game-container .eco-game-area { background: linear-gradient(180deg, #fffef5 0%, #fffde7 100%); border: 2px solid #ffc107; box-shadow: 0 4px 20px rgba(255, 193, 7, 0.12); }
        .game-drop-zone { background: rgba(255, 249, 196, 0.5); border: 3px dashed #ffb74d; border-radius: 16px; padding: 1rem; min-height: 120px; margin-bottom: 1rem; }
        .game-drop-zone.drag-over { background: rgba(255, 193, 7, 0.2); }
        .game-draggable { background: #fff; border: 2px solid #e0e0e0; border-radius: 14px; padding: 0.75rem 1rem; cursor: grab; display: inline-block; margin: 0.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .game-option { background: #fff; border: 2px solid #e0e0e0; border-radius: 14px; padding: 0.8rem 1rem; margin-bottom: 0.5rem; cursor: pointer; transition: all 0.2s; }
        .game-option:hover { border-color: #ffc107; background: #fffde7; }
        .game-option.correct { background: #c8e6c9; border-color: #81c784; }
        .game-option.incorrect { background: #ffcdd2; border-color: var(--eco-accent); }
        .game-match-row { display: flex; gap: 1rem; align-items: center; margin-bottom: 0.75rem; }
        .game-match-left, .game-match-right { flex: 1; padding: 0.5rem; border-radius: 12px; }
        .match-item.selected { border-color: #ffc107; background: rgba(255, 193, 7, 0.15); }
        .match-item.matched { opacity: 0.6; cursor: default; }
        .game-result { text-align: center; padding: 2rem; }
        /* Environmental 3D game overlay – large, kid-friendly */
        .eco-3d-overlay { align-items: flex-end; justify-content: center; padding: 1.25rem; }
        .eco-3d-game-ui { font-size: 1.15rem; }
        .eco-3d-question-card { background: linear-gradient(180deg, #fffef8 0%, #fffde7 100%); border: 3px solid var(--eco-primary); border-radius: 24px; padding: 2rem; box-shadow: 0 8px 32px rgba(0,0,0,0.12); }
        .eco-3d-question-text { font-weight: 700; font-size: 1.35rem; margin: 0 0 1.25rem; color: var(--eco-dark); line-height: 1.4; }
        .eco-3d-options { display: flex; flex-direction: column; gap: 0.75rem; }
        .eco-3d-option { background: #fff; border: 3px solid #e0e0e0; border-radius: 18px; padding: 1rem 1.25rem; cursor: pointer; font-size: 1.2rem; text-align: left; transition: all 0.2s; line-height: 1.4; }
        .eco-3d-option:hover { border-color: var(--eco-primary); background: rgba(78, 205, 196, 0.08); }
        .eco-3d-option.eco-3d-correct { background: #c8e6c9; border-color: #81c784; }
        .eco-3d-option.eco-3d-incorrect { background: #ffcdd2; border-color: var(--eco-accent); }
        .eco-3d-result { background: #fff; border: 3px solid var(--eco-primary); border-radius: 24px; padding: 2.5rem; text-align: center; font-size: 1.25rem; }
        .eco-3d-result h2 { font-size: 1.75rem; margin: 0 0 0.5rem; }
        .eco-3d-no-questions { padding: 2rem; text-align: center; color: #555; font-size: 1.2rem; }
        /* Drag & drop 3D game – large cards and zones */
        .eco-3d-instruction { font-weight: 700; margin: 0 0 1rem; color: var(--eco-dark); text-align: center; font-size: 1.3rem; line-height: 1.4; }
        .eco-3d-zones { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem; justify-content: center; }
        .eco-3d-drop-zone { background: rgba(255, 249, 196, 0.7); border: 3px dashed var(--eco-primary); border-radius: 20px; padding: 1rem 1.25rem; min-width: 180px; min-height: 100px; transition: background 0.2s; }
        .eco-3d-drop-zone.eco-3d-drag-over { background: rgba(78, 205, 196, 0.25); border-style: solid; }
        .eco-3d-zone-label { font-weight: 700; font-size: 1.2rem; color: var(--eco-dark); display: block; margin-bottom: 0.5rem; line-height: 1.4; }
        .eco-3d-dropped { min-height: 2.5rem; display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: flex-start; }
        .eco-3d-draggable-pool { display: flex; flex-wrap: wrap; gap: 0.75rem; justify-content: center; }
        .eco-3d-draggable { background: #fff; border: 3px solid #e0e0e0; border-radius: 18px; padding: 0.9rem 1.25rem; cursor: grab; font-size: 1.2rem; box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: transform 0.15s, box-shadow 0.15s; line-height: 1.4; }
        .eco-3d-draggable:hover { border-color: var(--eco-primary); box-shadow: 0 4px 16px rgba(78, 205, 196, 0.2); }
        .eco-3d-draggable.eco-3d-dragging { opacity: 0.85; cursor: grabbing; transform: scale(1.03); }
        .eco-3d-draggable.eco-3d-wrong { animation: eco-shake 0.4s ease; border-color: var(--eco-accent); background: #ffebee; }
        .eco-3d-draggable.eco-3d-correct { border-color: #81c784; background: #e8f5e9; }
        @keyframes eco-shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } }
        /* Matching pairs 3D game – draw arrow, large cards */
        .eco-3d-matching-wrap { position: relative; }
        .eco-3d-match-lines { position: absolute; left: 0; top: 0; width: 100%; height: 100%; pointer-events: none; overflow: visible; }
        .eco-3d-line-drag { stroke: var(--eco-primary, #4ecdc4); transition: none; }
        .eco-3d-line-done { stroke: #2e7d32; stroke-width: 3; }
        .eco-3d-line-wrong { stroke: #e57373; }
        .eco-3d-matching-grid { display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap; position: relative; z-index: 1; }
        .eco-3d-match-col { display: flex; flex-direction: column; gap: 0.75rem; min-width: 260px; }
        .eco-3d-match-card { background: #fff; border: 3px solid #e0e0e0; border-radius: 18px; padding: 1rem 1.25rem; cursor: crosshair; font-size: 1.2rem; transition: all 0.2s; line-height: 1.4; user-select: none; }
        .eco-3d-match-card:hover { border-color: var(--eco-primary); background: rgba(78, 205, 196, 0.08); }
        .eco-3d-match-card.eco-3d-drawing { border-color: var(--eco-primary); box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.4); }
        .eco-3d-match-card.eco-3d-matched { background: #e8f5e9; border-color: #81c784; opacity: 0.95; cursor: default; }
        .eco-3d-match-card.eco-3d-wrong { animation: eco-shake 0.4s ease; border-color: var(--eco-accent); background: #ffebee; }
    </style>
@endpush

@section('student-main')
    <div class="eco-student-back-bar">
        <a href="{{ route('dashboard.student') }}" class="eco-student-back-link">← Back to My Learning</a>
    </div>
    <div class="play-game-container">
        <div id="game-mount"></div>
    </div>
    <script>
        window.EnviroEduGame = {
            template: @json($miniGame->gameTemplate->slug),
            config: @json($miniGame->config),
            storageUrl: @json(asset('storage')),
            gameId: {{ $miniGame->id }},
            progressGameUrl: @json(route('progress.game')),
            csrfToken: @json(csrf_token()),
        };
    </script>
    @if ($miniGame->gameTemplate->slug === 'environment_3d')
        @vite(['resources/js/game-environment-3d.js'])
    @else
        @vite(['resources/js/game-runner.js'])
    @endif
@endsection
