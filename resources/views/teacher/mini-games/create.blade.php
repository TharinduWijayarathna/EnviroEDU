@extends('layouts.teacher')

@section('title', 'Create game with AI')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.mini-games.index') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Mini Games</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 0.5rem;">Create an environmental game with AI</h1>
    <p style="color: #555; margin-bottom: 1.5rem;">Pick a game type (drag & drop or matching pairs), then describe the topic. AI will generate the game with a 3D animated scene. All content is environment-only.</p>

    <form method="POST" action="{{ route('teacher.mini-games.generate') }}" style="max-width: 600px;">
        @csrf
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Game type</label>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="game_type" value="drag_drop" {{ old('game_type', 'drag_drop') === 'drag_drop' ? 'checked' : '' }} required>
                    <span>Drag & drop</span> <span style="color: #666; font-size: 0.9rem;">— Sort items into categories</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="game_type" value="matching" {{ old('game_type') === 'matching' ? 'checked' : '' }}>
                    <span>Matching pairs</span> <span style="color: #666; font-size: 0.9rem;">— Match terms with definitions</span>
                </label>
            </div>
        </div>
        <div style="margin-bottom: 1.25rem;">
            <label for="prompt" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">What should the game be about?</label>
            <textarea id="prompt" name="prompt" class="eco-input" rows="4" required placeholder="e.g. Recycling and waste sorting for grade 4, or Ocean ecosystems and marine life for grade 5">{{ old('prompt') }}</textarea>
            @error('prompt')
                <span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>
            @enderror
            <p style="font-size: 0.85rem; color: #666; margin-top: 0.35rem;">Keep it to environmental topics: nature, recycling, climate, habitats, conservation, etc.</p>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="topic_id" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Topic (optional)</label>
            <select id="topic_id" name="topic_id" class="eco-input">
                <option value="">None</option>
                @foreach ($topics ?? [] as $t)
                    <option value="{{ $t->id }}" {{ old('topic_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom: 1.25rem;">
            <label for="grade_level" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Grade level (optional)</label>
            <select id="grade_level" name="grade_level" class="eco-input">
                <option value="">Any</option>
                @foreach (range(1, 8) as $g)
                    <option value="{{ $g }}" {{ old('grade_level') == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="eco-btn">Generate game with AI</button>
        <a href="{{ route('teacher.mini-games.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>
@endsection
