@extends('layouts.student')

@section('title', __('messages.dashboard.quizzes'))

@section('student-main')
    <div class="eco-env-wrap eco-env-wrap-list">
        <div id="eco-env-container" class="eco-env-canvas" aria-hidden="true"></div>
        <div class="eco-env-overlay eco-env-overlay-list">
            <a href="{{ route('dashboard.student') }}" class="eco-env-back">{{ __('messages.dashboard.back_to_my_learning') }}</a>
            <div class="eco-env-panel">
                <h1 class="eco-env-panel-title">📝 {{ __('messages.dashboard.quizzes_title') }}</h1>
                <p class="eco-env-panel-desc">{{ __('messages.dashboard.quizzes_desc') }}</p>
                @if (isset($standaloneQuizzes) && $standaloneQuizzes->isNotEmpty())
                    <ul class="eco-env-list">
                        @foreach ($standaloneQuizzes as $quiz)
                            <li>
                                <a href="{{ url('/play/quiz/'.$quiz->id) }}" class="eco-env-list-item eco-env-list-item-quiz">
                                    <span class="eco-env-list-icon">📝</span>
                                    <span class="eco-env-list-text">{{ $quiz->title }}</span>
                                    <span class="eco-env-list-go">{{ __('messages.dashboard.play') }} →</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="eco-env-empty">{{ __('messages.dashboard.no_standalone_quizzes') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .eco-env-wrap-list { position: relative; min-height: calc(100vh - 160px); width: 100%; }
        .eco-env-overlay-list { align-items: flex-start; justify-content: flex-start; padding: 1.25rem 1.5rem; overflow-y: auto; }
        .eco-env-back { font-weight: 700; font-size: 1rem; color: var(--eco-primary); text-decoration: none; margin-bottom: 1rem; display: inline-block; position: relative; z-index: 10; pointer-events: auto; }
        .eco-env-back:hover { text-decoration: underline; }
        .eco-env-panel { background: rgba(255,255,255,0.95); border-radius: 20px; padding: 1.5rem; max-width: 560px; border: 2px solid rgba(78, 205, 196, 0.4); box-shadow: 0 8px 32px rgba(0,0,0,0.1); position: relative; z-index: 10; pointer-events: auto; }
        .eco-env-panel-title { font-family: 'Bubblegum Sans', cursive; font-size: 1.5rem; color: #1a3c34; margin: 0 0 0.35rem; }
        .eco-env-panel-desc { font-size: 0.95rem; color: #5a6c64; margin: 0 0 1rem; line-height: 1.4; }
        .eco-env-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.5rem; }
        .eco-env-list-item { display: flex; align-items: center; gap: 0.6rem; padding: 0.9rem 1.1rem; border-radius: 14px; text-decoration: none; color: #1a3c34; font-weight: 600; background: #f8fcfb; border: 2px solid transparent; transition: all 0.2s; }
        .eco-env-list-item:hover { background: #e8f7f5; border-color: var(--eco-primary); }
        .eco-env-list-item-quiz { background: #fffbf0; }
        .eco-env-list-item-quiz:hover { border-color: #d4a84b; background: #fff5dc; }
        .eco-env-list-icon { font-size: 1.3rem; }
        .eco-env-list-text { flex: 1; }
        .eco-env-list-go { font-size: 0.9rem; color: var(--eco-primary); }
        .eco-env-empty { margin: 0; color: #5a6c64; font-size: 1rem; }
    </style>
@endpush

