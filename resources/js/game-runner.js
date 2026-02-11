/**
 * EnviroEdu Mini Game Framework – runs teacher-created games from template + config.
 */

(function () {
  const mount = document.getElementById('game-mount');
  if (!mount || !window.EnviroEduGame) return;

  const { template, config } = window.EnviroEduGame;

  function renderDragDrop() {
    const categories = config.categories || [];
    const items = config.items || [];
    let correctCount = 0;

    let html = '<p style="margin-bottom: 1rem; font-weight: 600;">Drag each item to the correct category.</p>';
    html += '<div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">';
    categories.forEach((cat) => {
      html += `<div class="game-drop-zone" data-category="${cat.id}"><strong>${cat.label}</strong><div class="dropped"></div></div>`;
    });
    html += '</div>';
    html += '<div class="draggable-pool" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">';
    items.forEach((item, i) => {
      html += `<div class="game-draggable" draggable="true" data-category="${item.category_id}" data-index="${i}">${item.label}</div>`;
    });
    html += '</div>';
    mount.innerHTML = html;

    const pool = mount.querySelector('.draggable-pool');
    const zones = mount.querySelectorAll('.game-drop-zone');

    mount.querySelectorAll('.game-draggable').forEach((el) => {
      el.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', el.dataset.category);
        e.dataTransfer.setData('index', el.dataset.index);
        el.style.opacity = '0.5';
      });
      el.addEventListener('dragend', () => { el.style.opacity = '1'; });
    });

    zones.forEach((zone) => {
      zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('drag-over'); });
      zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
      zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        const category = e.dataTransfer.getData('text/plain');
        const index = e.dataTransfer.getData('index');
        if (zone.dataset.category !== category) return;
        const item = pool.querySelector(`[data-index="${index}"]`);
        if (!item) return;
        correctCount++;
        item.draggable = false;
        item.style.cursor = 'default';
        zone.querySelector('.dropped').appendChild(item);
        if (correctCount === items.length) {
          mount.innerHTML = '<div class="game-result"><h2 style="font-family: \'Bubblegum Sans\', cursive; color: var(--eco-primary);">Well done!</h2><p>All sorted correctly.</p></div>';
        }
      });
    });
  }

  function renderMultipleChoice() {
    const questions = config.questions || [];
    let qIndex = 0;
    let score = 0;

    function showQuestion() {
      if (qIndex >= questions.length) {
        mount.innerHTML = `<div class="game-result"><h2 style="font-family: 'Bubblegum Sans', cursive; color: var(--eco-primary);">Complete!</h2><p>Score: ${score} / ${questions.length}</p></div>`;
        return;
      }
      const q = questions[qIndex];
      let html = `<div class="play-question" style="background: #fff; border-radius: 16px; padding: 1.5rem; border: 2px solid var(--eco-primary);"><p style="font-weight: 700; margin-bottom: 1rem;">${q.question_text}</p>`;
      (q.options || []).forEach((opt, i) => {
        const correct = opt.is_correct ? '1' : '0';
        html += `<div class="game-option" data-correct="${correct}">${opt.text}</div>`;
      });
      html += '</div>';
      mount.innerHTML = html;

      mount.querySelectorAll('.game-option').forEach((opt) => {
        opt.addEventListener('click', function () {
          const correct = this.dataset.correct === '1';
          mount.querySelectorAll('.game-option').forEach((o) => o.style.pointerEvents = 'none');
          this.classList.add(correct ? 'correct' : 'incorrect');
          if (correct) score++;
          qIndex++;
          setTimeout(showQuestion, 1000);
        });
      });
    }
    showQuestion();
  }

  function renderMatching() {
    const pairs = config.pairs || [];
    const leftItems = pairs.map((p, i) => ({ id: i, text: p.left })).sort(() => Math.random() - 0.5);
    const rightItems = pairs.map((p, i) => ({ id: i, text: p.right })).sort(() => Math.random() - 0.5);
    let selectedLeft = null;
    let matched = 0;

    let html = '<p style="margin-bottom: 1rem; font-weight: 600;">Match each item on the left with the correct item on the right.</p>';
    html += '<div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 1rem; align-items: center;">';
    html += '<div class="match-left">';
    leftItems.forEach((item) => {
      html += `<div class="game-draggable match-item" data-id="${item.id}" data-side="left">${item.text}</div>`;
    });
    html += '</div><div></div><div class="match-right">';
    rightItems.forEach((item) => {
      html += `<div class="game-draggable match-item" data-id="${item.id}" data-side="right">${item.text}</div>`;
    });
    html += '</div></div>';
    mount.innerHTML = html;

    mount.querySelectorAll('.match-item').forEach((el) => {
      el.addEventListener('click', function () {
        if (this.classList.contains('matched')) return;
        const id = this.dataset.id;
        const side = this.dataset.side;
        if (side === 'left') {
          if (selectedLeft) selectedLeft.classList.remove('selected');
          selectedLeft = this;
          this.classList.add('selected');
        } else {
          if (!selectedLeft) return;
          const leftId = selectedLeft.dataset.id;
          if (leftId === id) {
            selectedLeft.classList.add('matched');
            this.classList.add('matched');
            matched++;
            if (matched === pairs.length) {
              mount.innerHTML = '<div class="game-result"><h2 style="font-family: \'Bubblegum Sans\', cursive; color: var(--eco-primary);">Perfect match!</h2><p>All pairs matched.</p></div>';
            }
          }
          selectedLeft.classList.remove('selected');
          selectedLeft = null;
        }
      });
    });
  }

  if (template === 'drag_drop') renderDragDrop();
  else if (template === 'multiple_choice') renderMultipleChoice();
  else if (template === 'matching') renderMatching();
  else mount.innerHTML = '<p>Unknown game type.</p>';
})();
