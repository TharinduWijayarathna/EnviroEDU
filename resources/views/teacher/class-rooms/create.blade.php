@extends('layouts.teacher')

@section('title', 'Create Class')

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Create Class</h1>

    <form method="POST" action="{{ route('teacher.class-rooms.store') }}" style="max-width: 700px;">
        @csrf
        <div style="margin-bottom: 1rem;">
            <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Class name</label>
            <input id="name" type="text" name="name" class="eco-input" value="{{ old('name') }}" required placeholder="e.g. Grade 4A">
            @error('name')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1.5rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="3" placeholder="Short description of this class">{{ old('description') }}</textarea>
        </div>
        <button type="submit" class="eco-btn">Save Class</button>
        <a href="{{ route('teacher.class-rooms.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>
@endsection
