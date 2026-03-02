/**
 * Rainbow – Tap the colors in ROYGBIV order!
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  const ROYGBIV = [
    { name: 'Red', color: '#e53935' },
    { name: 'Orange', color: '#ff9800' },
    { name: 'Yellow', color: '#fdd835' },
    { name: 'Green', color: '#43a047' },
    { name: 'Blue', color: '#1e88e5' },
    { name: 'Indigo', color: '#3949ab' },
    { name: 'Violet', color: '#8e24aa' },
  ];

  let shuffled = [];
  let nextIndex = 0;
  let completed = false;

  function init() {
    shuffled = [...ROYGBIV].sort(() => Math.random() - 0.5);
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#4fc3f7 0%,#81d4fa 40%,#e1f5fe 100%);border-radius:20px;overflow:hidden;padding:28px;box-shadow:inset 0 0 80px rgba(255,255,255,0.3);';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'margin-bottom:20px;';
    header.innerHTML = '<h2 style="color:#01579b;font-size:1.5rem;margin:0 0 8px;">🌈 Rainbow Colors</h2><p style="color:#0277bd;margin:0;font-weight:600;">Tap colors in order: Red → Orange → Yellow → Green → Blue → Indigo → Violet</p>';
    mount.appendChild(header);

    const progress = document.createElement('div');
    progress.id = 'rb-progress';
    progress.style.cssText = 'text-align:center;font-weight:800;color:#0d47a1;margin-bottom:24px;font-size:1.2rem;background:rgba(255,255,255,0.9);padding:12px 24px;border-radius:16px;display:inline-block;margin-left:50%;transform:translateX(-50%);';
    progress.textContent = '0 / 7';
    mount.appendChild(progress);

    const grid = document.createElement('div');
    grid.style.cssText = 'display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:18px;max-width:520px;margin:0 auto;';
    shuffled.forEach((c) => {
      const btn = document.createElement('button');
      btn.dataset.name = c.name;
      btn.style.cssText = `aspect-ratio:1;border-radius:50%;border:4px solid ${c.color};background:${c.color};cursor:pointer;transition:all 0.25s;box-shadow:0 6px 16px rgba(0,0,0,0.2);font-size:0;`;
      btn.addEventListener('click', () => onTap(btn));
      btn.addEventListener('mouseenter', () => { if (!btn.disabled) btn.style.transform = 'scale(1.12)'; });
      btn.addEventListener('mouseleave', () => { btn.style.transform = 'scale(1)'; });
      grid.appendChild(btn);
    });
    mount.appendChild(grid);

    const hint = document.createElement('p');
    hint.style.cssText = 'text-align:center;margin-top:24px;font-size:1rem;color:#546e7a;font-weight:600;';
    hint.textContent = 'Sunlight + rain = light bends in water drops = rainbow!';
    mount.appendChild(hint);

    const style = document.createElement('style');
    style.textContent = '@keyframes rb-pop { 0%{transform:scale(1)} 50%{transform:scale(1.2)} 100%{transform:scale(1)} } @keyframes rb-shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-12px)} 75%{transform:translateX(12px)} }';
    document.head.appendChild(style);
  }

  function onTap(btn) {
    if (completed) return;
    const expected = ROYGBIV[nextIndex];
    const correct = btn.dataset.name === expected.name;

    if (correct) {
      btn.style.background = '#c8e6c9';
      btn.style.borderColor = '#2e7d32';
      btn.style.transform = 'scale(1)';
      btn.disabled = true;
      btn.style.animation = 'rb-pop 0.4s ease';
      nextIndex++;
      document.getElementById('rb-progress').textContent = `${nextIndex} / 7`;

      if (nextIndex >= 7) {
        completed = true;
        setTimeout(() => {
          showWinUI('🌈', 'Rainbow Complete!', 'You know ROYGBIV! Rainbows form when light bends in water drops.');
          recordComplete(slug, progressUrl, csrfToken, {});
        }, 600);
      }
    } else {
      btn.style.animation = 'rb-shake 0.45s ease';
      btn.style.background = '#ffcdd2';
      btn.style.borderColor = '#e53935';
      setTimeout(() => {
        btn.style.animation = '';
        const c = ROYGBIV.find((x) => x.name === btn.dataset.name);
        if (c) {
          btn.style.background = c.color;
          btn.style.borderColor = c.color;
        }
      }, 450);
    }
  }

  init();
})();
