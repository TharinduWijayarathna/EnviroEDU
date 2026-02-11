const GRADE_4_TOPICS = [
  { id: 'living-nonliving', title: 'Living and Non-Living Things', icon: '🌱', progress: 0, games: [{ type: 'drag-drop', title: 'Living Lab' }, { type: 'quiz', title: 'Alive Check Quiz' }] },
  { id: 'plants', title: 'Plants Around Us', icon: '🌿', progress: 0, games: [{ type: 'builder', title: 'Plant Builder' }, { type: 'match', title: 'Plant Match' }] },
  { id: 'animals', title: 'Animals and Habitats', icon: '🦁', progress: 0, games: [{ type: 'drag-drop', title: 'Habitat Hero' }, { type: 'quiz', title: 'Mini Safari Quiz' }] },
  { id: 'environment', title: 'My Environment', icon: '🏙️', progress: 0, games: [{ type: 'clean', title: 'Clean the City' }, { type: 'quiz', title: 'Good or Bad Habit?' }] },
];

const GRADE_5_TOPICS = [
  { id: 'water', title: 'Water Resources', icon: '💧', progress: 0, games: [{ type: 'scenario', title: 'Water Saver Challenge' }, { type: 'quiz', title: 'Rain Cycle Quiz' }] },
  { id: 'soil', title: 'Soil and Agriculture', icon: '🌾', progress: 0, games: [{ type: 'match', title: 'Soil Smart' }, { type: 'farm', title: 'Farm Life MiniSim' }] },
  { id: 'weather', title: 'Weather and Climate', icon: '⛅', progress: 0, games: [{ type: 'dress', title: 'Dress for the Weather' }, { type: 'forecast', title: 'Forecast Fun' }] },
  { id: 'protection', title: 'Environmental Protection', icon: '♻️', progress: 0, games: [{ type: 'recycle', title: 'Recycle Right' }, { type: 'story', title: 'Save the Forest' }] },
];

const GAME_DATA = {
  'living-lab': {
    items: [
      { name: '🌳 Tree', type: 'living' },
      { name: '🐱 Cat', type: 'living' },
      { name: '🚗 Car', type: 'nonliving' },
      { name: '🪨 Rock', type: 'nonliving' },
      { name: '🦋 Butterfly', type: 'living' },
      { name: '⚽ Ball', type: 'nonliving' },
    ],
  },
  'alive-check': {
    questions: [
      { question: 'Does a plant grow?', options: ['Yes', 'No'], correct: 0 },
      { question: 'Does a rock breathe?', options: ['Yes', 'No'], correct: 1 },
      { question: 'Can animals move on their own?', options: ['Yes', 'No'], correct: 0 },
      { question: 'Does a chair need food?', options: ['Yes', 'No'], correct: 1 },
    ],
  },
  'habitat-hero': {
    items: [
      { name: '🐘 Elephant', habitat: 'forest' },
      { name: '🐠 Fish', habitat: 'water' },
      { name: '🦅 Eagle', habitat: 'sky' },
      { name: '🐄 Cow', habitat: 'farm' },
    ],
  },
  'safari-quiz': {
    questions: [
      { question: 'Where does a fish live?', options: ['Trees', 'Water', 'Desert', 'Cave'], correct: 1 },
      { question: 'What do elephants eat?', options: ['Meat', 'Fish', 'Plants', 'Stones'], correct: 2 },
      { question: 'Which is a wild animal?', options: ['Dog', 'Cat', 'Tiger', 'Cow'], correct: 2 },
    ],
  },
};

let currentGrade = 4;
let currentTopics = [...GRADE_4_TOPICS];
let earnedBadges = [];

function renderTopics() {
  const el = document.getElementById('ecoTopicsList');
  if (!el) return;
  el.innerHTML = currentTopics
    .map(
      (topic, i) => `
    <div class="eco-topic-card" data-index="${i}">
      <div class="eco-topic-icon">${topic.icon}</div>
      <div class="eco-topic-title">${topic.title}</div>
      <div class="eco-topic-progress"><div class="eco-topic-progress-bar" style="width: ${topic.progress}%"></div></div>
    </div>`
    )
    .join('');
  el.querySelectorAll('.eco-topic-card').forEach((card) => {
    card.addEventListener('click', () => selectTopic(Number(card.dataset.index)));
  });
}

function selectTopic(index) {
  const topic = currentTopics[index];
  const header = document.getElementById('ecoGameHeader');
  const content = document.getElementById('ecoGameContent');
  if (!header || !content) return;
  header.textContent = topic.title;
  content.innerHTML = `
    <button type="button" class="eco-btn" data-action="video" data-topic="${topic.id}">📺 Watch Video Lesson</button>
    <div style="margin-top: 1.5rem; text-align: center;">
      <h3 style="font-family: 'Bubblegum Sans', cursive; margin-bottom: 1rem;">Play Games!</h3>
      <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
        ${topic.games.map((g, i) => `<button type="button" class="eco-btn" data-game="${topic.id}" data-index="${i}">🎮 ${g.title}</button>`).join('')}
      </div>
    </div>
  `;
  content.querySelector('[data-action="video"]')?.addEventListener('click', () => playVideo(topic.id));
  content.querySelectorAll('[data-game]').forEach((btn) => {
    btn.addEventListener('click', () => playGame(btn.dataset.game, Number(btn.dataset.index)));
  });
}

function playVideo(topicId) {
  const content = document.getElementById('ecoGameContent');
  if (!content) return;
  content.innerHTML = `
    <div style="background: #2C3E50; border-radius: 15px; aspect-ratio: 16/9; max-width: 700px; display: flex; align-items: center; justify-content: center; color: white;">
      <div style="text-align: center;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">📺</div>
        <p>Video: ${topicId}</p>
        <p style="font-size: 0.9rem; margin-top: 1rem; opacity: 0.7;">(Video would play here)</p>
      </div>
    </div>
    <button type="button" class="eco-btn" onclick="location.reload()">Back to Topics</button>
  `;
}

function playGame(topicId, gameIndex) {
  const topic = currentTopics.find((t) => t.id === topicId);
  if (!topic) return;
  const game = topic.games[gameIndex];
  if (!game) return;

  if (topicId === 'living-nonliving' && game.type === 'drag-drop') loadLivingLab();
  else if (topicId === 'living-nonliving' && game.type === 'quiz') loadAliveCheckQuiz();
  else if (topicId === 'animals' && game.type === 'drag-drop') loadHabitatHero();
  else if (topicId === 'animals' && game.type === 'quiz') loadSafariQuiz();
  else showGenericGame(game.title);
}

function loadLivingLab() {
  const content = document.getElementById('ecoGameContent');
  const data = GAME_DATA['living-lab'];
  if (!content || !data) return;
  content.innerHTML = `
    <div style="text-align: center; margin-bottom: 1rem;"><h3 style="font-family: 'Bubblegum Sans', cursive;">Drag items to the correct box!</h3></div>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
      <div class="eco-drop-zone" data-type="living"><h3 style="font-size: 1rem; text-align: center;">🌱 Living</h3><div class="dropped-items"></div></div>
      <div class="eco-drop-zone" data-type="nonliving"><h3 style="font-size: 1rem; text-align: center;">🪨 Non-Living</h3><div class="dropped-items"></div></div>
    </div>
    <div style="display: flex; flex-wrap: wrap; gap: 0.8rem; justify-content: center; padding: 1rem;">
      ${data.items.map((item, i) => `<div class="eco-draggable-item" draggable="true" data-type="${item.type}" data-index="${i}">${item.name}</div>`).join('')}
    </div>
  `;
  initDragDrop(data.items.length, 'living', 'nonliving', 'Life Detective');
}

function initDragDrop(totalItems, typeA, typeB, badgeName) {
  const draggables = document.querySelectorAll('.eco-draggable-item');
  const zones = document.querySelectorAll('.eco-drop-zone');
  let correct = 0;

  draggables.forEach((d) => {
    d.addEventListener('dragstart', (e) => {
      e.dataTransfer.setData('text/plain', d.dataset.type);
      e.dataTransfer.setData('html', d.outerHTML);
      d.style.opacity = '0.5';
    });
    d.addEventListener('dragend', () => { d.style.opacity = '1'; });
  });

  zones.forEach((zone) => {
    zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', (e) => {
      e.preventDefault();
      zone.classList.remove('drag-over');
      const type = e.dataTransfer.getData('text/plain');
      const zoneType = zone.dataset.type;
      const html = e.dataTransfer.getData('html');
      if (type === zoneType) {
        showEcoFeedback('Correct! Great job! 🎉', 'success');
        const div = document.createElement('div');
        div.innerHTML = html;
        const item = div.firstChild;
        item.draggable = false;
        item.style.cursor = 'default';
        zone.querySelector('.dropped-items').appendChild(item);
        const idx = item.dataset.index;
        document.querySelector(`.eco-draggable-item[data-index="${idx}"]`)?.remove();
        correct++;
        if (correct === totalItems) setTimeout(() => awardEcoBadge(badgeName, '🔬', 'You correctly classified all items!'), 500);
      } else showEcoFeedback('Oops! Try again! 🤔', 'error');
    });
  });
}

function loadAliveCheckQuiz() {
  const content = document.getElementById('ecoGameContent');
  const data = GAME_DATA['alive-check'];
  if (!content || !data) return;
  let currentQ = 0;
  let score = 0;

  function showQ() {
    if (currentQ >= data.questions.length) {
      content.innerHTML = `<div style="text-align: center;"><h2 style="font-family: 'Bubblegum Sans', cursive; color: var(--eco-primary);">Quiz Complete! 🎉</h2><p style="font-size: 2rem; margin: 2rem 0;">Score: ${score}/${data.questions.length}</p><button type="button" class="eco-btn" onclick="location.reload()">Back to Topics</button></div>`;
      if (score / data.questions.length >= 0.8) setTimeout(() => awardEcoBadge('Living Expert', '🌟', 'You scored 80%+!'), 500);
      return;
    }
    const q = data.questions[currentQ];
    content.innerHTML = `
      <div style="max-width: 550px; width: 100%;">
        <div style="background: #fff; border: 3px solid var(--eco-primary); border-radius: 15px; padding: 1.5rem;">
          <h3>Question ${currentQ + 1} of ${data.questions.length}</h3>
          <h3 style="margin: 1rem 0;">${q.question}</h3>
          <div style="display: flex; flex-direction: column; gap: 0.8rem;">
            ${q.options.map((opt, i) => `<div class="eco-option" data-answer="${i}">${opt}</div>`).join('')}
          </div>
        </div>
        <p style="text-align: center; margin-top: 1rem; font-weight: 700;">Score: ${score}/${currentQ}</p>
      </div>
    `;
    content.querySelectorAll('.eco-option').forEach((opt) => {
      opt.addEventListener('click', function () {
        const all = content.querySelectorAll('.eco-option');
        all.forEach((o) => { o.style.pointerEvents = 'none'; });
        const chosen = Number(this.dataset.answer);
        if (chosen === q.correct) {
          this.classList.add('correct');
          score++;
          showEcoFeedback('Correct! 🎉', 'success');
        } else {
          this.classList.add('incorrect');
          all[q.correct].classList.add('correct');
          showEcoFeedback('Not quite! 📚', 'error');
        }
        setTimeout(() => { currentQ++; showQ(); }, 1200);
      });
    });
  }
  showQ();
}

function loadHabitatHero() {
  const content = document.getElementById('ecoGameContent');
  const data = GAME_DATA['habitat-hero'];
  if (!content || !data) return;
  content.innerHTML = `
    <div style="text-align: center; margin-bottom: 1rem;"><h3 style="font-family: 'Bubblegum Sans', cursive;">Match animals to their habitats!</h3></div>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
      <div class="eco-drop-zone" data-type="forest"><h3 style="font-size: 1rem; text-align: center;">🌳 Forest</h3><div class="dropped-items"></div></div>
      <div class="eco-drop-zone" data-type="water"><h3 style="font-size: 1rem; text-align: center;">🌊 Water</h3><div class="dropped-items"></div></div>
      <div class="eco-drop-zone" data-type="sky"><h3 style="font-size: 1rem; text-align: center;">☁️ Sky</h3><div class="dropped-items"></div></div>
      <div class="eco-drop-zone" data-type="farm"><h3 style="font-size: 1rem; text-align: center;">🏡 Farm</h3><div class="dropped-items"></div></div>
    </div>
    <div style="display: flex; flex-wrap: wrap; gap: 0.8rem; justify-content: center; padding: 1rem;">
      ${data.items.map((item, i) => `<div class="eco-draggable-item" draggable="true" data-type="${item.habitat}" data-index="${i}">${item.name}</div>`).join('')}
    </div>
  `;
  initDragDrop(data.items.length, 'forest', 'water', 'Habitat Hero');
  document.querySelectorAll('.eco-drop-zone').forEach((z) => {
    z.addEventListener('drop', () => {});
  });
}

function loadSafariQuiz() {
  const content = document.getElementById('ecoGameContent');
  const data = GAME_DATA['safari-quiz'];
  if (!content || !data) return;
  let currentQ = 0;
  let score = 0;

  function showQ() {
    if (currentQ >= data.questions.length) {
      content.innerHTML = `<div style="text-align: center;"><h2 style="font-family: 'Bubblegum Sans', cursive; color: var(--eco-primary);">Safari Complete! 🦁</h2><p style="font-size: 2rem; margin: 2rem 0;">Score: ${score}/${data.questions.length}</p><button type="button" class="eco-btn" onclick="location.reload()">Back to Topics</button></div>`;
      if (score / data.questions.length >= 0.9) setTimeout(() => awardEcoBadge('Wildlife Wizard', '🧙', 'You scored 90%+!'), 500);
      return;
    }
    const q = data.questions[currentQ];
    content.innerHTML = `
      <div style="max-width: 550px; width: 100%;">
        <div style="background: #fff; border: 3px solid var(--eco-primary); border-radius: 15px; padding: 1.5rem;">
          <h3>Question ${currentQ + 1} of ${data.questions.length}</h3>
          <h3 style="margin: 1rem 0;">${q.question}</h3>
          <div style="display: flex; flex-direction: column; gap: 0.8rem;">
            ${q.options.map((opt, i) => `<div class="eco-option" data-answer="${i}">${opt}</div>`).join('')}
          </div>
        </div>
      </div>
    `;
    content.querySelectorAll('.eco-option').forEach((opt) => {
      opt.addEventListener('click', function () {
        const all = content.querySelectorAll('.eco-option');
        all.forEach((o) => { o.style.pointerEvents = 'none'; });
        const chosen = Number(this.dataset.answer);
        if (chosen === q.correct) {
          this.classList.add('correct');
          score++;
          showEcoFeedback('Perfect! 🎉', 'success');
        } else {
          this.classList.add('incorrect');
          all[q.correct].classList.add('correct');
          showEcoFeedback('Good try! 💪', 'error');
        }
        setTimeout(() => { currentQ++; showQ(); }, 1200);
      });
    });
  }
  showQ();
}

function showGenericGame(title) {
  const content = document.getElementById('ecoGameContent');
  if (!content) return;
  content.innerHTML = `
    <div style="text-align: center;">
      <h2 style="font-family: 'Bubblegum Sans', cursive; font-size: 2rem; margin-bottom: 2rem;">${title}</h2>
      <div style="font-size: 5rem; margin-bottom: 2rem;">🎮</div>
      <p style="font-size: 1.2rem; margin-bottom: 2rem;">This game is coming soon!</p>
      <button type="button" class="eco-btn" onclick="location.reload()">Back to Topics</button>
    </div>
  `;
}

function showEcoFeedback(message, type) {
  const el = document.getElementById('ecoFeedback');
  const text = document.getElementById('ecoFeedbackText');
  if (!el || !text) return;
  text.textContent = message;
  el.className = 'eco-feedback ' + type + ' show';
  setTimeout(() => el.classList.remove('show'), 2000);
}

function awardEcoBadge(title, icon, description) {
  if (earnedBadges.includes(title)) return;
  earnedBadges.push(title);
  const countEl = document.getElementById('ecoBadgeCount');
  if (countEl) countEl.textContent = earnedBadges.length;
  const modal = document.getElementById('ecoBadgeModal');
  const iconEl = document.getElementById('ecoBadgeIcon');
  const titleEl = document.getElementById('ecoBadgeTitle');
  const descEl = document.getElementById('ecoBadgeDescription');
  if (modal && iconEl && titleEl && descEl) {
    iconEl.textContent = icon;
    titleEl.textContent = title;
    descEl.textContent = description;
    modal.classList.add('show');
  }
}

function closeEcoBadgeModal() {
  document.getElementById('ecoBadgeModal')?.classList.remove('show');
}

document.getElementById('ecoCloseBadgeBtn')?.addEventListener('click', closeEcoBadgeModal);

document.getElementById('ecoGradeSelector')?.addEventListener('change', (e) => {
  currentGrade = Number(e.target.value);
  currentTopics = currentGrade === 4 ? [...GRADE_4_TOPICS] : [...GRADE_5_TOPICS];
  renderTopics();
  const content = document.getElementById('ecoGameContent');
  const header = document.getElementById('ecoGameHeader');
  if (content) content.innerHTML = '<div style="text-align: center; color: #666;"><div style="font-size: 4rem; margin-bottom: 1rem;">🎮</div><p style="font-size: 1.2rem;">Pick a topic from the left to begin!</p></div>';
  if (header) header.textContent = 'Select a topic to start!';
});

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', renderTopics);
} else {
  renderTopics();
}
