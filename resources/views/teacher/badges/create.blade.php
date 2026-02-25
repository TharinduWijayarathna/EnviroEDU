@extends('layouts.teacher')

@section('title', 'Create Badge')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.badges.index') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Badges</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Create Badge</h1>

    <form method="POST" action="{{ route('teacher.badges.store') }}" style="max-width: 700px;">
        @csrf
        <div style="margin-bottom: 1rem;">
            <label for="topic_id" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Topic <span style="color: var(--eco-accent);">*</span></label>
            <select id="topic_id" name="topic_id" class="eco-input" required>
                <option value="">Select a topic</option>
                @foreach ($topics as $t)
                    <option value="{{ $t->id }}" {{ old('topic_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                @endforeach
            </select>
            @error('topic_id')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
            <p style="font-size: 0.85rem; color: #666; margin-top: 0.25rem;">Students earn this badge when they complete activities in this topic.</p>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Badge name <span style="color: var(--eco-accent);">*</span></label>
            <input id="name" type="text" name="name" class="eco-input" value="{{ old('name') }}" required placeholder="e.g. Water Cycle Master">
            @error('name')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="2" placeholder="What does this badge mean?">{{ old('description') }}</textarea>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="icon" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Icon (optional)</label>
            <input id="icon" type="text" name="icon" class="eco-input" value="{{ old('icon', '🏆') }}" placeholder="🏆 or any emoji" maxlength="50">
        </div>
        <div style="margin-bottom: 1.5rem;">
            <span style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Award badge when student completes <span style="color: var(--eco-accent);">*</span></span>
            @foreach (\App\Enums\BadgeAwardFor::cases() as $option)
                <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; cursor: pointer;">
                    <input type="radio" name="award_for" value="{{ $option->value }}" {{ old('award_for') === $option->value ? 'checked' : '' }} required>
                    <span>{{ $option->label() }}</span>
                </label>
            @endforeach
            @error('award_for')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="eco-btn">Save Badge</button>
        <a href="{{ route('teacher.badges.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>
@endsection
