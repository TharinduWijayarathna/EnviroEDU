@extends('layouts.teacher')

@section('title', 'Edit Badge')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.badges.show', $badge) }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Badge</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Edit Badge</h1>

    <form method="POST" action="{{ route('teacher.badges.update', $badge) }}" style="max-width: 700px;">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 1rem;">
            <label for="topic_id" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Topic <span style="color: var(--eco-accent);">*</span></label>
            <select id="topic_id" name="topic_id" class="eco-input" required>
                @foreach ($topics as $t)
                    <option value="{{ $t->id }}" {{ old('topic_id', $badge->topic_id) == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                @endforeach
            </select>
            @error('topic_id')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Badge name <span style="color: var(--eco-accent);">*</span></label>
            <input id="name" type="text" name="name" class="eco-input" value="{{ old('name', $badge->name) }}" required>
            @error('name')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="2">{{ old('description', $badge->description) }}</textarea>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="icon" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Icon (optional)</label>
            <input id="icon" type="text" name="icon" class="eco-input" value="{{ old('icon', $badge->icon ?? '🏆') }}" maxlength="50">
        </div>
        <div style="margin-bottom: 1.5rem;">
            <span style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Award badge when student completes <span style="color: var(--eco-accent);">*</span></span>
            @foreach (\App\Enums\BadgeAwardFor::cases() as $option)
                <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; cursor: pointer;">
                    <input type="radio" name="award_for" value="{{ $option->value }}" {{ old('award_for', $badge->award_for?->value) === $option->value ? 'checked' : '' }} required>
                    <span>{{ $option->label() }}</span>
                </label>
            @endforeach
            @error('award_for')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="eco-btn">Update Badge</button>
        <a href="{{ route('teacher.badges.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>
@endsection
