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
        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Badge image (optional)</label>
            <p style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">Generate with Google AI (Imagen). Set <code>GEMINI_API_KEY</code> in .env.</p>
            <input type="hidden" name="image_path" id="image_path" value="{{ old('image_path', $badge->image_path) }}">
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: flex-start;">
                <textarea id="image_prompt" class="eco-input" rows="2" placeholder="e.g. A round eco badge with a leaf, cartoon style"></textarea>
                <button type="button" id="generate_badge_image" class="eco-btn" style="background: #2C3E50;">Generate image</button>
            </div>
            <p id="image_generate_status" style="font-size: 0.85rem; margin-top: 0.5rem; display: none;"></p>
            @php $currentImagePath = old('image_path', $badge->image_path); @endphp
            <div id="badge_image_preview" style="margin-top: 0.75rem; {{ $currentImagePath ? '' : 'display: none;' }}">
                <img id="badge_image_img" src="{{ $currentImagePath ? asset('storage/'.$currentImagePath) : '' }}" alt="Badge preview" style="max-width: 120px; max-height: 120px; border-radius: 12px; border: 2px solid var(--eco-primary);">
                <p style="font-size: 0.85rem; color: #666; margin-top: 0.25rem;">Generated image. Leave empty to use icon only.</p>
            </div>
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

    <script>
        document.getElementById('generate_badge_image')?.addEventListener('click', function () {
            var prompt = document.getElementById('image_prompt').value.trim();
            var status = document.getElementById('image_generate_status');
            var preview = document.getElementById('badge_image_preview');
            var img = document.getElementById('badge_image_img');
            var pathInput = document.getElementById('image_path');
            var btn = this;
            if (!prompt) {
                status.style.display = 'block';
                status.style.color = 'var(--eco-accent)';
                status.textContent = 'Enter a description for the image.';
                return;
            }
            btn.disabled = true;
            status.style.display = 'block';
            status.style.color = '#666';
            status.textContent = 'Generating...';
            preview.style.display = 'none';
            fetch('{{ route('teacher.badges.generate-image') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ prompt: prompt })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success && data.path && data.url) {
                    pathInput.value = data.path;
                    img.src = data.url;
                    preview.style.display = 'block';
                    status.style.color = '#155724';
                    status.textContent = 'Image generated.';
                } else {
                    status.style.color = 'var(--eco-accent)';
                    status.textContent = data.message || 'Image generation failed.';
                }
            })
            .catch(function () {
                status.style.color = 'var(--eco-accent)';
                status.textContent = 'Request failed. Check GEMINI_API_KEY in .env.';
            })
            .finally(function () { btn.disabled = false; });
        });
    </script>
@endsection
