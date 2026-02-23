@extends('layouts.teacher')

@section('title', 'Edit Class: ' . $classRoom->name)

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.class-rooms.show', $classRoom) }}" style="color: var(--eco-primary); font-weight: 600;">← Back to class</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Edit Class</h1>

    <form method="POST" action="{{ route('teacher.class-rooms.update', $classRoom) }}" style="max-width: 700px;">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 1rem;">
            <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Class name</label>
            <input id="name" type="text" name="name" class="eco-input" value="{{ old('name', $classRoom->name) }}" required placeholder="e.g. Grade 4A">
            @error('name')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1.5rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="3" placeholder="Short description of this class">{{ old('description', $classRoom->description) }}</textarea>
        </div>
        <button type="submit" class="eco-btn">Update Class</button>
        <a href="{{ route('teacher.class-rooms.show', $classRoom) }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>
@endsection
