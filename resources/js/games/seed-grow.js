/**
 * How a Seed Grows – Choose the right care each day!
 * Pick: Add soil, Add water, or Add sun. Correct order = plant grows.
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  const STEPS = ['soil', 'water', 'sun'];
  const LABELS = { soil: '🌱 Add soil', water: '💧 Add water', sun: '☀️ Add sun' };
  let currentStep = 0;
  let plantStage = 0;
  let completed = false;

  function init() {
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#81c784 0%,#a5d6a7 30%,#c8e6c9 70%,#e8f5e9 100%);border-radius:20px;overflow:hidden;padding:28px;box-shadow:inset 0 0 80px rgba(255,255,255,0.4);';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'margin-bottom:24px;';
    header.innerHTML = '<h2 style="color:#2e7d32;font-size:1.5rem;margin:0 0 8px;">🌱 How a Seed Grows</h2><p style="color:#388e3c;margin:0;font-weight:600;">Pick the right care in order: soil → water → sun</p>';
    mount.appendChild(header);

    const visual = document.createElement('div');
    visual.id = 'sg-visual';
    visual.style.cssText = 'height:300px;display:flex;align-items:flex-end;justify-content:center;margin:24px 0;';
    mount.appendChild(visual);

    const msg = document.createElement('div');
    msg.id = 'sg-msg';
    msg.style.cssText = 'text-align:center;font-weight:700;font-size:1.15rem;color:#1b5e20;margin-bottom:24px;min-height:32px;padding:12px;background:rgba(255,255,255,0.8);border-radius:16px;';
    msg.textContent = 'Day 1: The seed needs soil to grow in.';
    mount.appendChild(msg);

    const btnWrap = document.createElement('div');
    btnWrap.style.cssText = 'display:flex;flex-wrap:wrap;gap:14px;justify-content:center;';
    STEPS.forEach((step) => {
      const btn = document.createElement('button');
      btn.className = 'eco-game-btn';
      btn.dataset.step = step;
      btn.textContent = LABELS[step];
      btn.style.cssText = 'background:#fff;border-color:#4caf50;color:#1b5e20;';
      btn.onclick = () => onChoice(step, btn);
      btnWrap.appendChild(btn);
    });
    mount.appendChild(btnWrap);

    renderPlant();
  }

  function renderPlant() {
    const visual = document.getElementById('sg-visual');
    if (!visual) return;
    visual.innerHTML = '';

    const stages = [
      { emoji: '🫘', size: 90, desc: 'Seed' },
      { emoji: '🌱', size: 120, desc: 'Sprout in soil' },
      { emoji: '🌿', size: 160, desc: 'Growing with water' },
      { emoji: '🌻', size: 180, desc: 'Flowering with sun' },
    ];
    const s = stages[Math.min(plantStage, stages.length - 1)];
    const div = document.createElement('div');
    div.style.cssText = 'text-align:center;animation:eco-float 2s ease-in-out infinite;';
    div.innerHTML = `<div style="font-size:${s.size}px;line-height:1;filter:drop-shadow(0 4px 8px rgba(0,0,0,0.15));">${s.emoji}</div><p style="font-size:1rem;color:#2e7d32;margin-top:10px;font-weight:700;">${s.desc}</p>`;
    visual.appendChild(div);
  }

  function onChoice(step, btn) {
    if (completed) return;
    const expected = STEPS[currentStep];
    const msg = document.getElementById('sg-msg');

    if (step !== expected) {
      btn.style.background = '#ffcdd2';
      btn.style.borderColor = '#e53935';
      btn.style.animation = 'eco-shake 0.4s ease';
      msg.textContent = `Try "${LABELS[expected]}" first!`;
      setTimeout(() => {
        btn.style.background = '#fff';
        btn.style.borderColor = '#4caf50';
        btn.style.animation = '';
      }, 600);
      return;
    }

    currentStep++;
    plantStage = currentStep;
    btn.style.background = '#c8e6c9';
    btn.style.borderColor = '#2e7d32';
    btn.disabled = true;

    const messages = [
      'Day 2: Now the seed needs water to wake up.',
      'Day 3: Add sunlight so the plant can grow strong.',
      'Your plant is fully grown!',
    ];
    msg.textContent = messages[Math.min(currentStep, 2)];

    renderPlant();

    if (currentStep >= 3) {
      completed = true;
      setTimeout(() => {
        showWinUI('🌻', 'Plant Grown!', 'You learned: soil gives roots a home, water wakes the seed, sun helps it grow!');
        recordComplete(slug, progressUrl, csrfToken, {});
      }, 1000);
    }
  }

  init();
})();
