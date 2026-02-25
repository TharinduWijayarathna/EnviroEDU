@extends('layouts.teacher')

@section('title', 'Badge: ' . $badge->name)

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.badges.index') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Badges</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ $badge->icon ?? '🏆' }} {{ $badge->name }}</h1>
    <p style="margin-bottom: 1.5rem; color: #555;">Topic: {{ $badge->topic?->title }} · Award for: {{ $badge->award_for?->label() ?? '—' }}</p>

    @if ($badge->description)
        <div class="eco-card" style="padding: 1rem; margin-bottom: 1.5rem;">
            <p style="margin: 0;">{{ $badge->description }}</p>
        </div>
    @endif

    <a href="{{ route('teacher.badges.edit', $badge) }}" class="eco-btn">Edit Badge</a>
@endsection
