/**
 * Water Cycle – Put the stages in the correct order!
 * Drag evaporation, condensation, precipitation, collection into the right sequence.
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  const STAGES = [
    { id: 'evaporation', emoji: '💨', label: 'Evaporation', desc: 'Sun heats water, it rises as vapor' },
    { id: 'condensation', emoji: '☁️', label: 'Condensation', desc: 'Vapor cools and forms clouds' },
    { id: 'precipitation', emoji: '🌧️', label: 'Precipitation', desc: 'Water falls as rain or snow' },
    { id: 'collection', emoji: '💧', label: 'Collection', desc: 'Water flows back to oceans and lakes' },
  ];

  let slots = [null, null, null, null];
  let completed = false;

  function showToast(msg) {
    const existing = document.getElementById('wc-toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.id = 'wc-toast';
    toast.className = 'eco-game-toast';
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
  }

  function createCard(stage) {
    const card = document.createElement('div');
    card.className = 'wc-card';
    card.draggable = true;
    card.dataset.id = stage.id;
    card.style.cssText = 'background:linear-gradient(135deg,#fff 0%,#e8f5e9 100%);border:3px solid #4caf50;border-radius:18px;padding:18px 26px;cursor:grab;font-weight:700;color:#1b5e20;box-shadow:0 4px 14px rgba(0,0,0,0.12);transition:transform 0.2s,box-shadow 0.2s;';
    card.innerHTML = `<span style="font-size:2rem;display:block;margin-bottom:6px;">${stage.emoji}</span><span>${stage.label}</span>`;
    card.addEventListener('dragstart', (e) => {
      e.dataTransfer.setData('text/plain', stage.id);
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('application/json', JSON.stringify(stage));
      card.style.opacity = '0.6';
      card.style.transform = 'scale(0.98)';
    });
    card.addEventListener('dragend', () => {
      card.style.opacity = '1';
      card.style.transform = '';
    });
    card.addEventListener('dragover', (e) => e.preventDefault());
    return card;
  }

  function init() {
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#0288d1 0%,#4fc3f7 30%,#e1f5fe 70%,#b3e5fc 100%);border-radius:20px;overflow:hidden;padding:24px;box-shadow:inset 0 0 60px rgba(255,255,255,0.2);';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'margin-bottom:20px;';
    header.innerHTML = '<h2 style="color:#01579b;font-size:1.5rem;margin:0 0 6px;">💧 Water Cycle</h2><p style="color:#0277bd;margin:0;font-weight:600;font-size:1rem;">Drag each stage into the correct order (1 → 4)</p>';
    mount.appendChild(header);

    const gameArea = document.createElement('div');
    gameArea.style.cssText = 'max-width:520px;margin:0 auto;';

    const slotsDiv = document.createElement('div');
    slotsDiv.id = 'wc-slots';
    slotsDiv.style.cssText = 'display:flex;flex-direction:column;gap:14px;';
    [1, 2, 3, 4].forEach((i) => {
      const slot = document.createElement('div');
      slot.className = 'wc-slot';
      slot.dataset.index = i - 1;
      slot.style.cssText = 'min-height:78px;background:rgba(255,255,255,0.95);border:3px dashed #4fc3f7;border-radius:18px;display:flex;align-items:center;padding:0 22px;transition:all 0.25s;box-shadow:0 2px 8px rgba(0,0,0,0.06);';
      slot.innerHTML = `<span style="font-weight:800;color:#0288d1;margin-right:14px;font-size:1.3rem;">${i}.</span><span class="wc-slot-content" style="color:#90a4ae;font-weight:600;">Drop stage here</span>`;
      slot.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        slot.style.background = '#e1f5fe';
        slot.style.borderColor = '#0288d1';
      });
      slot.addEventListener('dragleave', () => {
        slot.style.background = 'rgba(255,255,255,0.95)';
        slot.style.borderColor = '#4fc3f7';
      });
      slot.addEventListener('drop', (e) => onDrop(e, slot));
      slotsDiv.appendChild(slot);
    });
    gameArea.appendChild(slotsDiv);

    const poolDiv = document.createElement('div');
    poolDiv.id = 'wc-pool';
    poolDiv.style.cssText = 'display:flex;flex-wrap:wrap;gap:14px;justify-content:center;margin-top:28px;';
    [...STAGES].sort(() => Math.random() - 0.5).forEach((s) => {
      poolDiv.appendChild(createCard(s));
    });
    gameArea.appendChild(poolDiv);

    const checkBtn = document.createElement('button');
    checkBtn.className = 'eco-game-btn';
    checkBtn.textContent = '✓ Check my order';
    checkBtn.style.cssText = 'margin:28px auto 0;display:block;background:#0288d1;color:#fff;border-color:#01579b;';
    checkBtn.onclick = checkOrder;
    gameArea.appendChild(checkBtn);

    mount.appendChild(gameArea);
  }

  function onDrop(e, slot) {
    e.preventDefault();
    slot.style.background = 'rgba(255,255,255,0.95)';
    slot.style.borderColor = '#4fc3f7';
    const id = e.dataTransfer.getData('text/plain');
    const stage = STAGES.find((s) => s.id === id);
    if (!stage) return;

    const idx = parseInt(slot.dataset.index, 10);
    const existing = slots[idx];
    const pool = document.getElementById('wc-pool');
    const cardInPool = mount.querySelector(`.wc-card[data-id="${id}"]`);

    if (existing && pool && existing.id !== id) {
      pool.appendChild(createCard(existing));
    }

    slots[idx] = stage;
    const content = slot.querySelector('.wc-slot-content');
    if (content) {
      content.remove();
    }
    const span = document.createElement('span');
    span.className = 'wc-slot-content';
    span.style.cssText = 'display:flex;align-items:center;gap:10px;font-weight:700;color:#1b5e20;';
    span.innerHTML = `<span style="font-size:1.6rem;">${stage.emoji}</span><span>${stage.label}</span>`;
    slot.appendChild(span);

    if (cardInPool) {
      cardInPool.remove();
    }
  }

  function checkOrder() {
    const correct = slots.every((s, i) => s && s.id === STAGES[i].id);
    if (correct) {
      completed = true;
      showWinUI('💧', 'Perfect!', 'You know the water cycle: evaporation → condensation → precipitation → collection!');
      recordComplete(slug, progressUrl, csrfToken, {});
    } else {
      showToast('Not quite! Remember: Sun heats water → clouds form → rain falls → water collects.');
    }
  }

  init();
})();
