@extends('layouts.teacher')

@section('title', 'Create Badge')

@section('teacher')
    <p style="margin-bottom: 1rem;"><a href="{{ route('teacher.badges.index') }}" style="color: var(--eco-primary); font-weight: 600;">← Back to Badges</a></p>
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Create Badge</h1>

    <form method="POST" action="{{ route('teacher.badges.store') }}" style="max-width: 700px;" enctype="multipart/form-data">
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
        <div class="badge-image-section" style="margin-bottom: 1.5rem;">
            <input type="hidden" name="image_path" id="image_path" value="{{ old('image_path') }}">
            <div class="badge-image-choices" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <label class="badge-image-choice" style="flex: 1; min-width: 140px; cursor: pointer; border: 2px solid #e0e0e0; border-radius: 16px; padding: 1rem; text-align: center; transition: border-color 0.2s, box-shadow 0.2s;">
                    <input type="radio" name="badge_image_source" value="upload" style="display: none;" class="badge-src-radio">
                    <span style="font-size: 1.5rem;">📤</span>
                    <div style="font-weight: 600; margin-top: 0.25rem;">Upload</div>
                </label>
                <label class="badge-image-choice" style="flex: 1; min-width: 140px; cursor: pointer; border: 2px solid #e0e0e0; border-radius: 16px; padding: 1rem; text-align: center; transition: border-color 0.2s, box-shadow 0.2s;">
                    <input type="radio" name="badge_image_source" value="ai" style="display: none;" class="badge-src-radio">
                    <span style="font-size: 1.5rem;">✨</span>
                    <div style="font-weight: 600; margin-top: 0.25rem;">Generate with AI</div>
                </label>
            </div>
            <div id="badge_upload_panel" class="badge-panel" style="display: none;">
                <input type="file" name="badge_image" id="badge_image_file" accept="image/jpeg,image/png,image/gif,image/webp" class="eco-input" style="padding: 0.5rem;">
                <div id="badge_upload_preview" style="margin-top: 0.75rem; display: none;"><img id="badge_upload_img" src="" alt="" style="max-width: 120px; max-height: 120px; object-fit: contain; border-radius: 12px; border: 2px solid var(--eco-primary);"></div>
            </div>
            <div id="badge_ai_panel" class="badge-panel" style="display: none;">
                <button type="button" id="generate_badge_image" class="eco-btn" style="background: linear-gradient(135deg, #2C3E50 0%, #4ECDC4 100%); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; cursor: pointer;">
                    <span class="btn-text">Generate</span>
                </button>
                <div id="badge_ai_loading" class="badge-ai-loading" style="display: none; margin-top: 1rem; text-align: center;">
                    <div class="badge-ai-spinner"></div>
                    <div class="badge-ai-dots"><span></span><span></span><span></span></div>
                </div>
                <div id="badge_image_preview" class="badge-result-preview" style="display: none; margin-top: 1rem;">
                    <img id="badge_image_img" src="" alt="" style="max-width: 120px; max-height: 120px; object-fit: contain; border-radius: 12px; border: 2px solid var(--eco-primary); opacity: 0; transition: opacity 0.4s ease;">
                </div>
                <p id="image_generate_error" style="display: none; color: var(--eco-accent); font-size: 0.9rem; margin-top: 0.5rem;"></p>
            </div>
        </div>
        <style>
            .badge-image-choice:hover { border-color: var(--eco-primary) !important; }
            .badge-image-choice.selected { border-color: var(--eco-primary) !important; box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.3); }
            .badge-ai-spinner { width: 48px; height: 48px; margin: 0 auto 0.5rem; border: 4px solid #e0e0e0; border-top-color: var(--eco-primary); border-radius: 50%; animation: badge-spin 0.8s linear infinite; }
            .badge-ai-dots { display: flex; justify-content: center; gap: 6px; }
            .badge-ai-dots span { width: 8px; height: 8px; background: var(--eco-primary); border-radius: 50%; animation: badge-bounce 1.4s ease-in-out infinite both; }
            .badge-ai-dots span:nth-child(1) { animation-delay: 0s; }
            .badge-ai-dots span:nth-child(2) { animation-delay: 0.2s; }
            .badge-ai-dots span:nth-child(3) { animation-delay: 0.4s; }
            @keyframes badge-spin { to { transform: rotate(360deg); } }
            @keyframes badge-bounce { 0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; } 40% { transform: scale(1); opacity: 1; } }
        </style>
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
        (function () {
            var uploadRadio = document.querySelector('input[name="badge_image_source"][value="upload"]');
            var aiRadio = document.querySelector('input[name="badge_image_source"][value="ai"]');
            var uploadPanel = document.getElementById('badge_upload_panel');
            var aiPanel = document.getElementById('badge_ai_panel');
            var choices = document.querySelectorAll('.badge-image-choice');
            var fileInput = document.getElementById('badge_image_file');
            var uploadPreview = document.getElementById('badge_upload_preview');
            var uploadImg = document.getElementById('badge_upload_img');
            var pathInput = document.getElementById('image_path');

            function selectChoice(value) {
                choices.forEach(function (c) { c.classList.remove('selected'); });
                var label = value === 'upload' ? uploadRadio.closest('label') : aiRadio.closest('label');
                if (label) label.classList.add('selected');
                uploadPanel.style.display = value === 'upload' ? 'block' : 'none';
                aiPanel.style.display = value === 'ai' ? 'block' : 'none';
                if (value === 'upload') pathInput.value = '';
            }
            uploadRadio.addEventListener('change', function () { if (this.checked) selectChoice('upload'); });
            aiRadio.addEventListener('change', function () { if (this.checked) selectChoice('ai'); });
            fileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    var r = new FileReader();
                    r.onload = function () { uploadImg.src = r.result; uploadPreview.style.display = 'block'; };
                    r.readAsDataURL(this.files[0]);
                    pathInput.value = '';
                } else { uploadPreview.style.display = 'none'; uploadImg.src = ''; }
            });

            var genBtn = document.getElementById('generate_badge_image');
            var loading = document.getElementById('badge_ai_loading');
            var preview = document.getElementById('badge_image_preview');
            var img = document.getElementById('badge_image_img');
            var errEl = document.getElementById('image_generate_error');
            genBtn.addEventListener('click', function () {
                var topicId = document.getElementById('topic_id').value;
                var name = document.getElementById('name').value.trim();
                var description = (document.getElementById('description') && document.getElementById('description').value) ? document.getElementById('description').value.trim() : '';
                if (!topicId || !name) return;
                errEl.style.display = 'none';
                errEl.textContent = '';
                preview.style.display = 'none';
                img.style.opacity = '0';
                loading.style.display = 'block';
                genBtn.disabled = true;
                fetch('{{ route('teacher.badges.generate-image') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
                    body: JSON.stringify({ topic_id: parseInt(topicId, 10), name: name, description: description || null })
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    loading.style.display = 'none';
                    genBtn.disabled = false;
                    if (data.success && data.path && data.url) {
                        pathInput.value = data.path;
                        img.src = data.url;
                        preview.style.display = 'block';
                        img.offsetHeight;
                        img.style.opacity = '1';
                    } else {
                        errEl.textContent = data.message || 'Failed.';
                        errEl.style.display = 'block';
                    }
                })
                .catch(function () {
                    loading.style.display = 'none';
                    genBtn.disabled = false;
                    errEl.textContent = 'Request failed.';
                    errEl.style.display = 'block';
                });
            });
        })();
    </script>
@endsection
