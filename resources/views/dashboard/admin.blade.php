@extends('layouts.admin')

@section('title', 'School Dashboard')

@section('admin')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">School dashboard</h1>
    <p style="margin-bottom: 1.5rem; font-size: 1rem; color: #555;">Manage your school workspace. Approve teachers and students, and oversee activity.</p>

    @if ($school)
        <div class="eco-card" style="max-width: 560px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 0.75rem;">{{ $school->name }}</h2>
            <p style="color: #666; margin-bottom: 0.5rem;">School code: <strong style="font-size: 1.1rem; color: var(--eco-primary);">{{ $school->slug }}</strong></p>
            <p style="font-size: 0.9rem; color: #888;">Share this code with teachers and students so they can register and request access.</p>
        </div>

        @if ($pendingTeachersCount > 0 || $pendingStudentsCount > 0)
            <div class="eco-card" style="max-width: 560px; padding: 1.5rem;">
                <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 0.75rem;">Pending approvals</h2>
                <p style="color: #666; margin-bottom: 1rem;">
                    {{ $pendingTeachersCount }} teacher(s) and {{ $pendingStudentsCount }} student(s) waiting for approval.
                </p>
                <a href="{{ route('admin.approvals.index') }}" class="eco-btn">Review pending</a>
            </div>
        @endif
    @else
        <div class="eco-card" style="max-width: 560px; padding: 1.5rem;">
            <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 1.25rem; color: var(--eco-dark); margin-bottom: 0.75rem;">Welcome, {{ auth()->user()->name }}</h2>
            <p style="color: #666;">Your school is not set up. Please contact support.</p>
        </div>
    @endif
@endsection
