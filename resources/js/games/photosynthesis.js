/**
 * Photosynthesis – Collect water and sunlight to grow your plant!
 * Catch falling drops and sunbeams. Plant grows as you collect. Win at 100%.
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let canvas, ctx;
  let plantGrowth = 0;
  let waterCollected = 0;
  let sunCollected = 0;
  let basketLeft = 0;
  let basketWidth = 90;
  let fallingItems = [];
  let particles = [];
  let gameOver = false;
  let lastSpawn = 0;
  let starField = [];
  const TARGET = 100;

  function init() {
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#5dade2 0%,#82e0aa 40%,#58d68d 100%);border-radius:20px;overflow:hidden;box-shadow:inset 0 0 80px rgba(255,255,255,0.3);';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'position:absolute;top:16px;left:50%;transform:translateX(-50%);z-index:10;max-width:95%;';
    header.innerHTML = '🌱 Collect water 💧 and sunlight ☀️ to grow your plant!';
    mount.appendChild(header);

    const progressWrap = document.createElement('div');
    progressWrap.style.cssText = 'position:absolute;top:68px;left:50%;transform:translateX(-50%);width:90%;max-width:420px;z-index:10;';
    progressWrap.innerHTML = `
      <div style="display:flex;align-items:center;gap:12px;background:rgba(255,255,255,0.95);padding:10px 16px;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,0.1);border:2px solid #4caf50;">
        <div style="flex:1;height:24px;background:#e8f5e9;border-radius:12px;overflow:hidden;">
          <div id="pg-progress-fill" style="height:100%;width:0%;background:linear-gradient(90deg,#43a047,#66bb6a);transition:width 0.3s;border-radius:12px;"></div>
        </div>
        <span id="pg-percent" style="font-weight:700;color:#2e7d32;min-width:42px;">0%</span>
      </div>
    `;
    mount.appendChild(progressWrap);

    canvas = document.createElement('canvas');
    const w = Math.max(mount.clientWidth || 800, 400);
    canvas.width = w;
    canvas.height = 600;
    canvas.style.cssText = 'display:block;width:100%;height:600px;touch-action:none;';
    mount.appendChild(canvas);
    ctx = canvas.getContext('2d');

    for (let i = 0; i < 80; i++) {
      starField.push({ x: Math.random() * canvas.width, y: Math.random() * canvas.height, r: 0.5 + Math.random() });
    }

    basketLeft = (canvas.width - basketWidth) / 2;

    const onPointer = (e) => {
      const rect = canvas.getBoundingClientRect();
      const mx = ((e.clientX || e.touches?.[0]?.clientX) - rect.left) / rect.width * canvas.width;
      basketLeft = Math.max(10, Math.min(canvas.width - basketWidth - 10, mx - basketWidth / 2));
    };
    canvas.addEventListener('mousemove', onPointer);
    canvas.addEventListener('touchmove', (e) => { e.preventDefault(); onPointer(e); }, { passive: false });

    window.addEventListener('resize', onResize);
    requestAnimationFrame(gameLoop);
  }

  function onResize() {
    if (!canvas || !mount) return;
    const w = Math.max(mount.clientWidth || 800, 400);
    canvas.width = w;
    canvas.height = 600;
  }

  function spawnItem() {
    const now = performance.now();
    if (now - lastSpawn < 600) return;
    lastSpawn = now;
    fallingItems.push({
      x: Math.random() * (canvas.width - 50) + 25,
      y: -24,
      type: Math.random() < 0.5 ? 'water' : 'sun',
      vy: 2.2 + Math.random() * 1.2,
    });
  }

  function collectItem(item) {
    const bx = basketLeft + basketWidth / 2;
    const by = canvas.height - 70;
    const half = basketWidth / 2 + 8;
    if (item.x > bx - half && item.x < bx + half && item.y > by - 25 && item.y < by + 25) {
      if (item.type === 'water') waterCollected++;
      else sunCollected++;
      plantGrowth = Math.min(TARGET, (waterCollected + sunCollected) * 5);
      particles.push({ x: item.x, y: item.y, life: 1, type: item.type });
      return true;
    }
    return false;
  }

  function drawPlant(x, y, growth) {
    const h = 50 + growth * 2.2;
    ctx.save();
    ctx.translate(x, y);

    ctx.strokeStyle = '#1b5e20';
    ctx.lineWidth = 5;
    ctx.lineCap = 'round';
    ctx.beginPath();
    ctx.moveTo(0, 0);
    ctx.lineTo(0, -h);
    ctx.stroke();

    const leafCount = Math.floor(3 + growth / 18);
    for (let i = 0; i < leafCount; i++) {
      const ly = -h * (0.25 + (i / leafCount) * 0.65);
      const lw = 18 + growth / 8;
      ctx.strokeStyle = '#388e3c';
      ctx.lineWidth = 4;
      ctx.beginPath();
      ctx.moveTo(0, ly);
      ctx.lineTo(lw * (i % 2 ? 1 : -1), ly - 18);
      ctx.stroke();
    }

    if (growth >= 75) {
      ctx.fillStyle = '#ffeb3b';
      ctx.shadowColor = '#f9a825';
      ctx.shadowBlur = 12;
      ctx.beginPath();
      ctx.arc(0, -h - 12, 14, 0, Math.PI * 2);
      ctx.fill();
      ctx.shadowBlur = 0;
      ctx.fillStyle = '#f9a825';
      for (let i = 0; i < 6; i++) {
        const a = (i / 6) * Math.PI * 2 + Date.now() * 0.002;
        ctx.beginPath();
        ctx.arc(Math.cos(a) * 20, -h - 12 + Math.sin(a) * 20, 7, 0, Math.PI * 2);
        ctx.fill();
      }
    }
    ctx.restore();
  }

  function drawBasket() {
    const bx = basketLeft + basketWidth / 2;
    const by = canvas.height - 65;
    ctx.fillStyle = '#6d4c41';
    ctx.fillRect(basketLeft, by - 6, basketWidth, 14);
    ctx.fillStyle = '#8d6e63';
    ctx.beginPath();
    ctx.moveTo(basketLeft - 6, by);
    ctx.lineTo(basketLeft, by + 18);
    ctx.lineTo(basketLeft + basketWidth, by + 18);
    ctx.lineTo(basketLeft + basketWidth + 6, by);
    ctx.closePath();
    ctx.fill();
    ctx.strokeStyle = '#4e342e';
    ctx.lineWidth = 2;
    ctx.stroke();
  }

  function gameLoop() {
    if (gameOver) return;

    spawnItem();

    ctx.fillStyle = '#5dade2';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = 'rgba(255,255,255,0.6)';
    starField.forEach((s) => {
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fill();
    });

    ctx.fillStyle = '#2e7d32';
    ctx.fillRect(0, canvas.height - 100, canvas.width, 120);

    fallingItems = fallingItems.filter((item) => {
      item.y += item.vy;
      if (collectItem(item)) return false;
      if (item.y > canvas.height + 30) return false;

      if (item.type === 'water') {
        ctx.fillStyle = 'rgba(33, 150, 243, 0.95)';
        ctx.beginPath();
        ctx.ellipse(item.x, item.y, 14, 18, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.strokeStyle = 'rgba(25, 118, 210, 0.8)';
        ctx.lineWidth = 2;
        ctx.stroke();
      } else {
        ctx.fillStyle = '#ffeb3b';
        ctx.shadowColor = '#ffc107';
        ctx.shadowBlur = 8;
        ctx.beginPath();
        ctx.arc(item.x, item.y, 16, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;
        ctx.strokeStyle = '#ffa000';
        ctx.lineWidth = 2;
        ctx.stroke();
      }
      return true;
    });

    particles = particles.filter((p) => {
      p.life -= 0.04;
      if (p.life <= 0) return false;
      ctx.globalAlpha = p.life;
      ctx.fillStyle = p.type === 'water' ? '#29b6f6' : '#ffc107';
      ctx.beginPath();
      ctx.arc(p.x, p.y, 10, 0, Math.PI * 2);
      ctx.fill();
      ctx.globalAlpha = 1;
      return true;
    });

    drawPlant(canvas.width / 2, canvas.height - 95, plantGrowth);
    drawBasket();

    const fillEl = document.getElementById('pg-progress-fill');
    const pctEl = document.getElementById('pg-percent');
    if (fillEl) fillEl.style.width = `${plantGrowth}%`;
    if (pctEl) pctEl.textContent = `${Math.round(plantGrowth)}%`;

    if (plantGrowth >= TARGET) {
      gameOver = true;
      showWinUI('🌻', 'Plant Bloomed!', 'You collected enough water and sunlight. Plants need both for photosynthesis!');
      recordComplete(slug, progressUrl, csrfToken, { waterCollected, sunCollected });
      return;
    }

    requestAnimationFrame(gameLoop);
  }

  init();
})();
