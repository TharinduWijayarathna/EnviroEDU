@extends('layouts.teacher')

@section('title', 'Create Topic')

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Create Topic</h1>

    <form method="POST" action="{{ route('teacher.topics.store') }}" style="max-width: 700px;">
        @csrf
        <div style="margin-bottom: 1rem;">
            <label for="title" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Title</label>
            <input id="title" type="text" name="title" class="eco-input" value="{{ old('title') }}" required placeholder="e.g. Living and Non-Living Things">
            @error('title')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="3" placeholder="Short description of this lesson">{{ old('description') }}</textarea>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="grade_level" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Grade level (optional)</label>
            <select id="grade_level" name="grade_level" class="eco-input">
                <option value="">Any</option>
                @foreach (config('app.grade_levels', [4, 5]) as $g)
                    <option value="{{ $g }}" {{ old('grade_level') == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="video_url" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Video lesson URL (optional)</label>
            <input id="video_url" type="url" name="video_url" class="eco-input" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=... or any video link">
            <p style="font-size: 0.85rem; color: #666; margin-top: 0.25rem;">Students will see a &quot;Watch Video Lesson&quot; button when you add a URL.</p>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="order" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Order (optional)</label>
            <input id="order" type="number" name="order" class="eco-input" value="{{ old('order', 0) }}" min="0" placeholder="0">
        </div>
        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                <span>Publish (visible to students)</span>
            </label>
        </div>
        <button type="submit" class="eco-btn">Save Topic</button>
        <a href="{{ route('teacher.topics.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>
@endsection
