@extends('layouts.admin')

@section('title', __('messages.admin.teachers'))

@section('admin')
    <p style="margin-bottom: 1rem;"><a href="{{ route('dashboard.admin') }}" style="color: var(--eco-primary); font-weight: 600;">← {{ __('messages.admin.back_to_dashboard') }}</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">{{ __('messages.admin.teachers') }}</h1>
    <p style="margin-bottom: 1.5rem; font-size: 1rem; color: #555;">{{ __('messages.admin.teachers_desc') }}</p>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse ($teachers as $t)
            <div class="eco-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $t->name }}</h3>
                    <p style="color: #666; font-size: 0.9rem;">
                        {{ $t->email }} · {{ $t->teaching_classes_count }} {{ __('messages.admin.classes') }}
                    </p>
                    @if (!$t->is_approved)
                        <span style="display: inline-block; background: #fff3cd; color: #856404; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.85rem; margin-top: 0.35rem;">{{ __('messages.admin.pending') }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="eco-card" style="text-align: center; color: #666;">
                <p>{{ __('messages.admin.no_teachers') }}</p>
            </div>
        @endforelse
    </div>

    @if ($teachers->hasPages())
        <div style="margin-top: 1.5rem;">{{ $teachers->links() }}</div>
    @endif
@endsection
