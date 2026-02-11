@extends('layouts.teacher')

@section('title', 'Edit Mini Game')

@section('teacher')
    <h1 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; color: var(--eco-primary); margin-bottom: 1rem;">Edit Mini Game</h1>

    <form method="POST" action="{{ route('teacher.mini-games.update', $miniGame) }}" id="mini-game-form" style="max-width: 700px;">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 1rem;">
            <p style="font-weight: 600;">Game type: {{ $miniGame->gameTemplate->name }}</p>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="title" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Title</label>
            <input id="title" type="text" name="title" class="eco-input" value="{{ old('title', $miniGame->title) }}" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="description" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Description (optional)</label>
            <textarea id="description" name="description" class="eco-input" rows="2">{{ old('description', $miniGame->description) }}</textarea>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="grade_level" style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Grade level (optional)</label>
            <select id="grade_level" name="grade_level" class="eco-input">
                <option value="">Any</option>
                @for ($g = 1; $g <= 12; $g++)
                    <option value="{{ $g }}" {{ old('grade_level', $miniGame->grade_level) == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                @endfor
            </select>
        </div>

        @if ($miniGame->gameTemplate->slug === 'drag_drop')
            <div id="config-drag_drop" class="config-panel eco-card" style="padding: 1.25rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Categories</h3>
                <div id="categories-list">
                    @foreach (old('config_categories', $miniGame->config['categories'] ?? []) as $i => $cat)
                        <div class="eco-card cat-row" style="padding: 0.75rem; margin-bottom: 0.5rem;">
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                                <input type="text" name="config_categories[{{ $i }}][id]" placeholder="ID (e.g. living)" value="{{ $cat['id'] ?? '' }}" required style="width: 120px;" class="eco-input cat-id">
                                <input type="text" name="config_categories[{{ $i }}][label]" placeholder="Display name" value="{{ $cat['label'] ?? '' }}" required class="eco-input" style="flex:1;">
                                <button type="button" class="remove-cat eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-category" class="eco-btn" style="margin-top: 0.5rem; background: #2C3E50; font-size: 0.9rem;">+ Add category</button>

                <h3 style="font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 1rem;">Items to sort</h3>
                <div id="items-list">
                    @foreach (old('config_items', $miniGame->config['items'] ?? []) as $i => $item)
                        <div class="eco-card item-row" style="padding: 0.75rem; margin-bottom: 0.5rem;">
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                                <input type="text" name="config_items[{{ $i }}][label]" placeholder="Item (e.g. 🌳 Tree)" value="{{ $item['label'] ?? '' }}" required class="eco-input" style="flex:1;">
                                <select name="config_items[{{ $i }}][category_id]" required class="eco-input config-item-cat-select" style="width: 160px;">
                                    @foreach ($miniGame->config['categories'] ?? [] as $ci => $c)
                                        <option value="{{ $ci }}" {{ ($item['category_id'] ?? '') === ($c['id'] ?? '') ? 'selected' : '' }}>{{ $c['label'] ?? 'Category '.($ci+1) }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="remove-item eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-item" class="eco-btn" style="margin-top: 0.5rem; background: #2C3E50; font-size: 0.9rem;">+ Add item</button>
            </div>
        @endif

        @if ($miniGame->gameTemplate->slug === 'multiple_choice')
            <div id="config-multiple_choice" class="config-panel eco-card" style="padding: 1.25rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Questions</h3>
                <div id="mc-questions-list">
                    @foreach (old('config_questions', $miniGame->config['questions'] ?? []) as $qi => $q)
                        <div class="eco-card mcq-row" style="padding: 1rem; margin-bottom: 1rem;">
                            <div style="margin-bottom: 0.5rem;"><strong>Question {{ $qi + 1 }}</strong> <button type="button" class="remove-mcq eco-logout-btn" style="float:right; padding: 0.2rem 0.4rem; font-size: 0.8rem;">Remove</button></div>
                            <input type="text" name="config_questions[{{ $qi }}][question_text]" value="{{ $q['question_text'] ?? '' }}" placeholder="Question text" required class="eco-input" style="margin-bottom: 0.75rem;">
                            <p style="font-size: 0.9rem; margin-bottom: 0.25rem;">Options (select correct):</p>
                            @foreach ($q['options'] ?? [] as $oi => $opt)
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <input type="radio" name="config_questions[{{ $qi }}][correct]" value="{{ $oi }}" {{ ($opt['is_correct'] ?? false) ? 'checked' : '' }} required>
                                    <input type="text" name="config_questions[{{ $qi }}][options][{{ $oi }}][text]" value="{{ $opt['text'] ?? '' }}" placeholder="Option" required class="eco-input" style="flex:1;">
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-mcq-question" class="eco-btn" style="margin-top: 0.5rem; background: #2C3E50; font-size: 0.9rem;">+ Add question</button>
            </div>
        @endif

        @if ($miniGame->gameTemplate->slug === 'matching')
            <div id="config-matching" class="config-panel eco-card" style="padding: 1.25rem; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Pairs</h3>
                <div id="pairs-list">
                    @foreach (old('config_pairs', $miniGame->config['pairs'] ?? []) as $i => $p)
                        <div class="eco-card pair-row" style="padding: 0.75rem; margin-bottom: 0.5rem;">
                            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                <input type="text" name="config_pairs[{{ $i }}][left]" value="{{ $p['left'] ?? '' }}" placeholder="Left" required class="eco-input" style="flex:1;">
                                <span>→</span>
                                <input type="text" name="config_pairs[{{ $i }}][right]" value="{{ $p['right'] ?? '' }}" placeholder="Right" required class="eco-input" style="flex:1;">
                                <button type="button" class="remove-pair eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-pair" class="eco-btn" style="margin-top: 0.5rem; background: #2C3E50; font-size: 0.9rem;">+ Add pair</button>
            </div>
        @endif

        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $miniGame->is_published) ? 'checked' : '' }}>
                <span>Publish (visible to students)</span>
            </label>
        </div>
        <button type="submit" class="eco-btn">Update Mini Game</button>
        <a href="{{ route('teacher.mini-games.index') }}" style="margin-left: 1rem; color: #555;">Cancel</a>
    </form>

    @if ($miniGame->gameTemplate->slug === 'drag_drop')
        @push('scripts')
        <script>
        (function() {
            const categoriesList = document.getElementById('categories-list');
            const itemsList = document.getElementById('items-list');
            let catIndex = {{ count($miniGame->config['categories'] ?? []) }};
            let itemIndex = {{ count($miniGame->config['items'] ?? []) }};

            document.getElementById('add-category').onclick = function() {
                const block = document.createElement('div');
                block.className = 'eco-card cat-row';
                block.style.padding = '0.75rem';
                block.style.marginBottom = '0.5rem';
                block.innerHTML = `<div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                    <input type="text" name="config_categories[${catIndex}][id]" placeholder="ID" required style="width: 120px;" class="eco-input cat-id">
                    <input type="text" name="config_categories[${catIndex}][label]" placeholder="Display name" required class="eco-input" style="flex:1;">
                    <button type="button" class="remove-cat eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
                </div>`;
                categoriesList.appendChild(block);
                block.querySelector('.remove-cat').onclick = () => block.remove();
                catIndex++;
            };

            function refreshItemCategoryOptions() {
                const catRows = categoriesList.querySelectorAll('.cat-row');
                itemsList.querySelectorAll('.config-item-cat-select').forEach(sel => {
                    const currentVal = sel.value;
                    const opts = Array.from(catRows).map((row, idx) => {
                        const labelIn = row.querySelector('input[placeholder="Display name"]');
                        return `<option value="${idx}">${(labelIn && labelIn.value) ? labelIn.value : 'Category ' + (idx+1)}</option>`;
                    }).join('');
                    sel.innerHTML = opts;
                    if (currentVal !== undefined && sel.querySelector(`option[value="${currentVal}"]`)) sel.value = currentVal;
                });
            }
            categoriesList.addEventListener('input', refreshItemCategoryOptions);

            document.getElementById('add-item').onclick = function() {
                const catRows = categoriesList.querySelectorAll('.cat-row');
                let opts = Array.from(catRows).map((row, idx) => {
                    const labelIn = row.querySelector('input[placeholder="Display name"]');
                    return `<option value="${idx}">${(labelIn && labelIn.value) ? labelIn.value : 'Category ' + (idx+1)}</option>`;
                }).join('');
                if (!opts) opts = '<option value="">Add categories first</option>';
                const block = document.createElement('div');
                block.className = 'eco-card item-row';
                block.style.padding = '0.75rem';
                block.style.marginBottom = '0.5rem';
                block.innerHTML = `<div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                    <input type="text" name="config_items[${itemIndex}][label]" placeholder="Item" required class="eco-input" style="flex:1;">
                    <select name="config_items[${itemIndex}][category_id]" required class="eco-input config-item-cat-select" style="width: 160px;">${opts}</select>
                    <button type="button" class="remove-item eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
                </div>`;
                itemsList.appendChild(block);
                block.querySelector('.remove-item').onclick = () => block.remove();
                itemIndex++;
            };

            document.querySelectorAll('.remove-cat').forEach(btn => btn.onclick = function() { this.closest('.cat-row').remove(); });
            document.querySelectorAll('.remove-item').forEach(btn => btn.onclick = function() { this.closest('.item-row').remove(); });
        })();
        </script>
        @endpush
    @endif

    @if ($miniGame->gameTemplate->slug === 'multiple_choice')
        @push('scripts')
        <script>
        (function() {
            const mcList = document.getElementById('mc-questions-list');
            let mcqIndex = {{ count($miniGame->config['questions'] ?? []) }};
            document.getElementById('add-mcq-question').onclick = function() {
                const block = document.createElement('div');
                block.className = 'eco-card mcq-row';
                block.style.padding = '1rem';
                block.style.marginBottom = '1rem';
                block.innerHTML = `<div style="margin-bottom: 0.5rem;"><strong>Question ${mcqIndex + 1}</strong> <button type="button" class="remove-mcq eco-logout-btn" style="float:right; padding: 0.2rem 0.4rem; font-size: 0.8rem;">Remove</button></div>
                    <input type="text" name="config_questions[${mcqIndex}][question_text]" placeholder="Question text" required class="eco-input" style="margin-bottom: 0.75rem;">
                    <p style="font-size: 0.9rem; margin-bottom: 0.25rem;">Options (select correct):</p>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;"><input type="radio" name="config_questions[${mcqIndex}][correct]" value="0" required><input type="text" name="config_questions[${mcqIndex}][options][0][text]" placeholder="Option" required class="eco-input" style="flex:1;"></div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;"><input type="radio" name="config_questions[${mcqIndex}][correct]" value="1"><input type="text" name="config_questions[${mcqIndex}][options][1][text]" placeholder="Option" required class="eco-input" style="flex:1;"></div>`;
                mcList.appendChild(block);
                block.querySelector('.remove-mcq').onclick = () => block.remove();
                mcqIndex++;
            };
            document.querySelectorAll('.remove-mcq').forEach(btn => btn.onclick = function() { this.closest('.mcq-row').remove(); });
        })();
        </script>
        @endpush
    @endif

    @if ($miniGame->gameTemplate->slug === 'matching')
        @push('scripts')
        <script>
        (function() {
            const pairsList = document.getElementById('pairs-list');
            let pairIndex = {{ count($miniGame->config['pairs'] ?? []) }};
            document.getElementById('add-pair').onclick = function() {
                const block = document.createElement('div');
                block.className = 'eco-card pair-row';
                block.style.padding = '0.75rem';
                block.style.marginBottom = '0.5rem';
                block.innerHTML = `<div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                    <input type="text" name="config_pairs[${pairIndex}][left]" placeholder="Left" required class="eco-input" style="flex:1;">
                    <span>→</span>
                    <input type="text" name="config_pairs[${pairIndex}][right]" placeholder="Right" required class="eco-input" style="flex:1;">
                    <button type="button" class="remove-pair eco-logout-btn" style="padding: 0.25rem 0.5rem;">Remove</button>
                </div>`;
                pairsList.appendChild(block);
                block.querySelector('.remove-pair').onclick = () => block.remove();
                pairIndex++;
            };
            document.querySelectorAll('.remove-pair').forEach(btn => btn.onclick = function() { this.closest('.pair-row').remove(); });
        })();
        </script>
        @endpush
    @endif
@endsection
