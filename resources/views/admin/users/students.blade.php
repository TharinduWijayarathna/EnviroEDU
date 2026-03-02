@extends('layouts.admin')

@section('title', __('messages.admin.students'))

@section('admin')
    <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.admin') }}" style="color: var(--eco-primary); font-weight: 600;">← {{ __('messages.admin.back_to_dashboard') }}</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ __('messages.admin.students') }}</h1>
    <p style="margin-bottom: 1.5rem; font-size: 1rem; color: #555;">{{ __('messages.admin.students_desc') }}</p>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse ($students as $s)
            <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $s->name }}</h3>
                    <p style="color: #666; font-size: 0.9rem;">
                        {{ $s->email }}
                        @if ($s->grade_level)
                            · Grade {{ $s->grade_level }}
                        @endif
                        @if ($s->enrolledClasses->isNotEmpty())
                            · {{ $s->enrolledClasses->pluck('name')->join(', ') }}
                        @endif
                        · {{ $s->quiz_attempts_count }} quiz attempts · {{ $s->mini_game_attempts_count }} game attempts · {{ $s->badges_count }} badges
                    </p>
                    @if (!$s->is_approved)
                        <span style="display: inline-block; background: #fff3cd; color: #856404; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.85rem; margin-top: 0.35rem;">{{ __('messages.admin.pending') }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="eco-card" style="text-align: center; color: #666;">
                <p>{{ __('messages.admin.no_students') }}</p>
            </div>
        @endforelse
    </div>

    @if ($students->hasPages())
        <div style="margin-top: 1.5rem;">{{ $students->links() }}</div>
    @endif
@endsection
