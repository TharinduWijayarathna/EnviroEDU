@extends('layouts.student')

@section('title', __('messages.dashboard.my_badges'))

@section('student-main')
    <div style="padding: 1.5rem;">
        <div class="eco-student-back-bar">
            <a href="{{ route('dashboard.student') }}" class="eco-student-back-link">{{ __('messages.dashboard.back_to_my_learning') }}</a>
        </div>
        <div class="eco-kid-badges-page">
            <h1 class="eco-kid-badges-page-title">🏆 {{ __('messages.dashboard.my_badges') }}</h1>
            @if (isset($earnedBadges) && $earnedBadges->isNotEmpty())
                <p class="eco-kid-badges-count">{!! __('messages.dashboard.badges_count', ['count' => $earnedBadges->count(), 'badge' => $earnedBadges->count() === 1 ? __('messages.dashboard.badge') : __('messages.dashboard.badges_plural')]) !!} 🌟</p>
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
                <p class="eco-kid-badge-empty">{{ __('messages.dashboard.no_badges') }} 🌟</p>
                <a href="{{ route('dashboard.student') }}" class="eco-btn">{{ __('messages.dashboard.go_to_my_learning') }}</a>
            @endif
        </div>
    </div>
@endsection
