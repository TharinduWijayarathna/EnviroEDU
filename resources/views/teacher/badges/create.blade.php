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
        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Badge image (optional)</label>
            <p style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">Generate an image with Google AI (Imagen). Set <code>GEMINI_API_KEY</code> in .env. Free tier available at <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener">Google AI Studio</a>.</p>
            <input type="hidden" name="image_path" id="image_path" value="{{ old('image_path') }}">
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: flex-start;">
                <textarea id="image_prompt" class="eco-input" rows="2" placeholder="e.g. A round eco badge with a leaf and water drop, friendly cartoon style" style="flex: 1; min-width: 200px;"></textarea>
                <button type="button" id="generate_badge_image" class="eco-btn" style="background: #2C3E50;">Generate image</button>
            </div>
            <p id="image_generate_status" style="font-size: 0.85rem; margin-top: 0.5rem; display: none;"></p>
            <div id="badge_image_preview" style="margin-top: 0.75rem; display: none;">
                <img id="badge_image_img" src="" alt="Badge preview" style="max-width: 120px; max-height: 120px; border-radius: 12px; border: 2px solid var(--eco-primary);">
                <p style="font-size: 0.85rem; color: #666; margin-top: 0.25rem;">Generated image will be used for this badge. Leave empty to use icon only.</p>
            </div>
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
