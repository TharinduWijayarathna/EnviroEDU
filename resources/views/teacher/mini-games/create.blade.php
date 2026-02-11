@extends('layouts.teacher')

@section('title', 'Create Mini Game')

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Create Mini Game</h1>

    <form method="POST" action="{{ route('teacher.mini-games.store') }}" id="mini-game-form" style="max-width: 700px;" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 1rem;">
            <label for="game_template_id" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Game type</label>
            <select id="game_template_id" name="game_template_id" class="eco-input" required>
                <option value="">Select a template</option>
                @foreach ($templates as $t)
                    <option value="{{ $t->id }}" data-slug="{{ $t->slug }}" {{ old('game_template_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="title" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Title</label>
            <input id="title" type="text" name="title" class="eco-input" value="{{ old('title') }}" required>
            @error('title')<span style="color: var(--eco-accent); font-size: 0.9rem;">{{ $message }}</span>@enderror
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="topic_id" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Topic (optional)</label>
            <select id="topic_id" name="topic_id" class="eco-input">
                <option value="">None – standalone game</option>
                @foreach ($topics ?? [] as $t)
                    <option value="{{ $t->id }}" {{ old('topic_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="2">{{ old('description') }}</textarea>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="grade_level" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Grade level (optional)</label>
            <select id="grade_level" name="grade_level" class="eco-input">
                <option value="">Any</option>
                @for ($g = 1; $g <= 12; $g++)
                    <option value="{{ $g }}" {{ old('grade_level') == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                @endfor
            </select>
        </div>

        {{-- Config: Drag & Drop --}}
        <div id="config-drag_drop" class="config-panel eco-card" style="padding: 1.25rem; margin-bottom: 1rem; display: none;">
            <p style="font-size: 0.9rem; color: #555; margin-bottom: 1rem;">You can add an optional image to each category and item. Images will be shown in the game.</p>
            <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Categories (students drag items into these)</h3>
            <div id="categories-list"></div>
            <button type="button" id="add-category" class="eco-btn" style="margin-top: 0.5rem; background: #2C3E50; font-size: 0.9rem;">+ Add category</button>

            <h3 style="font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 1rem;">Items to sort</h3>
            <div id="items-list"></div>
            <button type="button" id="add-item" class="eco-btn" style="margin-top: 0.5rem; background: #2C3E50; font-size: 0.9rem;">+ Add item</button>
        </div>

        {{-- Config: Multiple Choice --}}
        <div id="config-multiple_choice" class="config-panel eco-card" style="padding: 1.25rem; margin-bottom: 1rem; display: none;">
            <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Questions</h3>
            <div id="mc-questions-list"></div>
            <button type="button" id="add-mc-question" class="eco-btn" style="margin-top: 0.5rem; background: #2C3E50; font-size: 0.9rem;">+ Add question</button>
        </div>

        {{-- Config: Matching --}}
        <div id="config-matching" class="config-panel eco-card" style="padding: 1.25rem; margin-bottom: 1rem; display: none;">
            <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Pairs (left item → right item)</h3>
            <div id="pairs-list"></div>
            <button type="button" id="add-pair" class="eco-btn" style="margin-top: 0.5rem; background: #2C3E50; font-size: 0.9rem;">+ Add pair</button>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                <span>Publish (visible to students)</span>
            </label>
        </div>
        <button type="submit" class="eco-btn">Save Mini Game</button>
        <a href="{{ route('teacher.mini-games.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>

    @push('scripts')
    <script>
(function() {
    const form = document.getElementById('mini-game-form');
    const templateSelect = document.getElementById('game_template_id');
    const panels = document.querySelectorAll('.config-panel');

    function showPanel(slug) {
        panels.forEach(p => { p.style.display = p.id === 'config-' + slug ? 'block' : 'none'; });
    }

    templateSelect.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        showPanel(opt?.dataset?.slug || '');
    });
    if (templateSelect.value) {
        const opt = templateSelect.selectedOptions[0];
        if (opt) showPanel(opt.dataset.slug || '');
    }

    // ---- Drag & Drop ----
    let catIndex = 0, itemIndex = 0;
    const categoriesList = document.getElementById('categories-list');
    const itemsList = document.getElementById('items-list');

    const storageUrl = '{{ asset("storage") }}';

    function addCategory(id, label, imagePath) {
        const i = id ?? catIndex++;
        const block = document.createElement('div');
        block.className = 'eco-card';
        block.style.padding = '0.75rem';
        block.style.marginBottom = '0.5rem';
        const imgPreview = imagePath ? (storageUrl + '/' + imagePath) : '';
        block.innerHTML = `
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                <input type="text" name="config_categories[${i}][id]" placeholder="ID (e.g. living)" value="${(id !== undefined && label !== undefined) ? (id || '') : ''}" required style="width: 120px;" class="eco-input cat-id">
                <input type="text" name="config_categories[${i}][label]" placeholder="Display name" value="${(id !== undefined && label !== undefined) ? (label || '') : ''}" required class="eco-input" style="flex:1;">
                <button type="button" class="remove-cat eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
            </div>
            <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <label style="font-size: 0.9rem;">Image (optional):</label>
                <input type="file" accept="image/*" class="cat-image-input" style="max-width: 180px;">
                <input type="hidden" name="config_categories[${i}][image_path]" class="cat-image-path" value="${imagePath || ''}">
                <span class="cat-image-preview" style="min-width: 48px; min-height: 48px;">${imgPreview ? `<img src="${imgPreview}" alt="" style="max-width: 64px; max-height: 64px; object-fit: contain;">` : ''}</span>
            </div>
        `;
        categoriesList.appendChild(block);
        block.querySelector('.remove-cat').onclick = () => block.remove();
        const fileInput = block.querySelector('.cat-image-input');
        const pathInput = block.querySelector('.cat-image-path');
        const previewEl = block.querySelector('.cat-image-preview');
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                previewEl.innerHTML = '<img src="' + URL.createObjectURL(this.files[0]) + '" alt="" style="max-width: 64px; max-height: 64px; object-fit: contain;">';
                pathInput.value = '';
            }
        });
        return block;
    }
    document.getElementById('add-category').onclick = () => addCategory();

    function addItem(label, categoryIndex, imagePath) {
        const i = itemIndex++;
        const catRows = categoriesList.querySelectorAll('.eco-card');
        let opts = Array.from(catRows).map((row, idx) => {
            const idIn = row.querySelector('.cat-id');
            const labelIn = row.querySelector('input[placeholder="Display name"]');
            const sel = (categoryIndex !== undefined && categoryIndex === idx) ? ' selected' : '';
            return `<option value="${idx}"${sel}>${(labelIn && labelIn.value) ? labelIn.value : ('Category ' + (idx+1))}</option>`;
        }).join('');
        if (!opts) opts = '<option value="">Add categories first</option>';
        const imgPreview = imagePath ? (storageUrl + '/' + imagePath) : '';
        const block = document.createElement('div');
        block.className = 'eco-card';
        block.style.padding = '0.75rem';
        block.style.marginBottom = '0.5rem';
        block.innerHTML = `
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                <input type="text" name="config_items[${i}][label]" placeholder="Item (e.g. 🌳 Tree)" value="${label || ''}" required class="eco-input" style="flex:1;">
                <select name="config_items[${i}][category_id]" required class="eco-input config-item-cat-select" style="width: 160px;">${opts}</select>
                <button type="button" class="remove-item eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
            </div>
            <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <label style="font-size: 0.9rem;">Image (optional):</label>
                <input type="file" accept="image/*" class="item-image-input" style="max-width: 180px;">
                <input type="hidden" name="config_items[${i}][image_path]" class="item-image-path" value="${imagePath || ''}">
                <span class="item-image-preview" style="min-width: 48px; min-height: 48px;">${imgPreview ? `<img src="${imgPreview}" alt="" style="max-width: 64px; max-height: 64px; object-fit: contain;">` : ''}</span>
            </div>
        `;
        itemsList.appendChild(block);
        block.querySelector('.remove-item').onclick = () => block.remove();
        const fileInput = block.querySelector('.item-image-input');
        const pathInput = block.querySelector('.item-image-path');
        const previewEl = block.querySelector('.item-image-preview');
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                previewEl.innerHTML = '<img src="' + URL.createObjectURL(this.files[0]) + '" alt="" style="max-width: 64px; max-height: 64px; object-fit: contain;">';
                pathInput.value = '';
            }
        });
        return block;
    }
    document.getElementById('add-item').onclick = () => addItem();

    // ---- Multiple Choice ----
    let mcqIndex = 0;
    const mcList = document.getElementById('mc-questions-list');
    function addMcQuestion(questionText, options) {
        const qIdx = mcqIndex++;
        const block = document.createElement('div');
        block.className = 'eco-card';
        block.style.padding = '1rem';
        block.style.marginBottom = '1rem';
        let optsHtml = '';
        (options || [{ text: '', is_correct: false }, { text: '', is_correct: false }]).forEach((opt, oIdx) => {
            optsHtml += `
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <input type="radio" name="config_questions[${qIdx}][correct]" value="${oIdx}" ${opt.is_correct ? 'checked' : ''} required>
                    <input type="text" name="config_questions[${qIdx}][options][${oIdx}][text]" value="${opt.text || ''}" placeholder="Option" required class="eco-input" style="flex:1;">
                </div>
            `;
        });
        block.innerHTML = `
            <div style="margin-bottom: 0.5rem;"><strong>Question ${qIdx + 1}</strong> <button type="button" class="remove-mcq eco-logout-btn" style="float:right; padding: 0.2rem 0.4rem; font-size: 0.8rem;">Remove</button></div>
            <input type="text" name="config_questions[${qIdx}][question_text]" value="${questionText || ''}" placeholder="Question text" required class="eco-input" style="margin-bottom: 0.75rem;">
            <p style="font-size: 0.9rem; margin-bottom: 0.25rem;">Options (select correct):</p>${optsHtml}
        `;
        mcList.appendChild(block);
        block.querySelector('.remove-mcq').onclick = () => block.remove();
    }
    document.getElementById('add-mcq-question').onclick = () => addMcQuestion();

    // ---- Matching ----
    let pairIndex = 0;
    const pairsList = document.getElementById('pairs-list');
    function addPair(left, right) {
        const i = pairIndex++;
        const block = document.createElement('div');
        block.className = 'eco-card';
        block.style.padding = '0.75rem';
        block.style.marginBottom = '0.5rem';
        block.innerHTML = `
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                <input type="text" name="config_pairs[${i}][left]" value="${left || ''}" placeholder="Left" required class="eco-input" style="flex:1;">
                <span>→</span>
                <input type="text" name="config_pairs[${i}][right]" value="${right || ''}" placeholder="Right" required class="eco-input" style="flex:1;">
                <button type="button" class="remove-pair eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
            </div>
        `;
        pairsList.appendChild(block);
        block.querySelector('.remove-pair').onclick = () => block.remove();
    }
    document.getElementById('add-pair').onclick = () => addPair();

    // Ensure at least one panel has content when template is selected
    form.addEventListener('submit', function() {
        const slug = templateSelect.selectedOptions[0]?.dataset?.slug;
        if (slug === 'drag_drop' && categoriesList.children.length === 0) addCategory();
        if (slug === 'drag_drop' && itemsList.children.length === 0) addItem();
        if (slug === 'multiple_choice' && mcList.children.length === 0) addMcQuestion();
        if (slug === 'matching' && pairsList.children.length === 0) addPair();
    });

    addCategory();
    addItem();
    addMcQuestion();
    addPair();
})();
    </script>
    @endpush
@endsection
