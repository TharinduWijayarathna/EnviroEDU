@extends('layouts.student')

@section('title', 'My badges')

@section('student-main')
    <div style="padding: 1.5rem;">
        <div class="eco-student-back-bar">
            <a href="{{ route('dashboard.student') }}" class="eco-student-back-link">← Back to My Learning</a>
        </div>
        <div class="eco-kid-badges-page">
            <h1 class="eco-kid-badges-page-title">🏆 My badges</h1>
            @if (isset($earnedBadges) && $earnedBadges->isNotEmpty())
                <p class="eco-kid-badges-count">You have <strong>{{ $earnedBadges->count() }}</strong>
                    {{ $earnedBadges->count() === 1 ? 'badge' : 'badges' }}! Great job! 🌟</p>
                <div class="eco-kid-badges-list">
                    @foreach ($earnedBadges as $badge)
                        <div class="eco-kid-badge-item">
                            @if ($badge->image_path)
                                <img src="{{ asset('storage/' . $badge->image_path) }}" alt=""
                                    class="eco-kid-badge-img">
                            @else
                                <span class="eco-kid-badge-emoji" aria-hidden="true">{{ $badge->icon ?? '🏆' }}</span>
                            @endif
                            <div class="eco-kid-badge-info">
                                <span class="eco-kid-badge-name">{{ $badge->name }}</span>
                                @if ($badge->description)
                                    <p class="eco-kid-badge-desc">{{ $badge->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="eco-kid-badge-empty">No badges yet. Play quizzes and games to earn some! 🌟</p>
                <a href="{{ route('dashboard.student') }}" class="eco-btn">Go to My Learning</a>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .eco-kid-badges-page {
            max-width: 700px;
            margin: 0 auto;
        }

        .eco-kid-badges-page-title {
            font-family: 'Bubblegum Sans', cursive;
            font-size: 1.75rem;
            color: var(--eco-dark);
            margin: 0 0 0.5rem;
        }

        .eco-kid-badges-count {
            font-size: 1.15rem;
            margin: 0 0 1.5rem;
            color: var(--eco-dark);
        }

        .eco-kid-badges-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .eco-kid-badge-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            background: linear-gradient(135deg, #fff9c4 0%, #fff59d 100%);
            border: 2px solid #ffc107;
            border-radius: 20px;
            padding: 1rem 1.25rem;
        }

        .eco-kid-badge-img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .eco-kid-badge-emoji {
            font-size: 2.5rem;
            flex-shrink: 0;
        }

        .eco-kid-badge-info {
            min-width: 0;
        }

        .eco-kid-badge-name {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--eco-dark);
            display: block;
            margin-bottom: 0.25rem;
        }

        .eco-kid-badge-desc {
            font-size: 0.95rem;
            color: #555;
            margin: 0;
            line-height: 1.4;
        }

        .eco-kid-badge-empty {
            font-size: 1.15rem;
            color: #666;
            margin: 0 0 1rem;
        }
    </style>
@endpush
