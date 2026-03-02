/**
 * Vine Growth – Guide the vine toward the sunlight!
 * Hold Left or Right to steer. Reach the sun to win.
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let canvas, ctx;
  let vinePoints = [];
  let direction = 0;
  let sunY = 90;
  let gameOver = false;
  let frameCount = 0;

  function init() {
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#5dade2 0%,#82e0aa 50%,#58d68d 100%);border-radius:20px;overflow:hidden;box-shadow:inset 0 0 80px rgba(255,255,255,0.25);';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'position:absolute;top:16px;left:50%;transform:translateX(-50%);z-index:10;max-width:92%;';
    header.textContent = '🌿 Hold Left or Right to steer the vine toward the sun!';
    mount.appendChild(header);

    const btnWrap = document.createElement('div');
    btnWrap.style.cssText = 'position:absolute;bottom:28px;left:50%;transform:translateX(-50%);display:flex;gap:28px;z-index:10;';
    const leftBtn = document.createElement('button');
    leftBtn.className = 'eco-game-btn';
    leftBtn.textContent = '◀ Left';
    leftBtn.style.cssText = 'padding:18px 36px;font-size:1.25rem;background:#4caf50;color:#fff;border-color:#2e7d32;';
    leftBtn.addEventListener('mousedown', () => { direction = -1; });
    leftBtn.addEventListener('mouseup', () => { direction = 0; });
    leftBtn.addEventListener('mouseleave', () => { direction = 0; });
    leftBtn.addEventListener('touchstart', (e) => { e.preventDefault(); direction = -1; });
    leftBtn.addEventListener('touchend', () => { direction = 0; });
    const rightBtn = document.createElement('button');
    rightBtn.className = 'eco-game-btn';
    rightBtn.textContent = 'Right ▶';
    rightBtn.style.cssText = 'padding:18px 36px;font-size:1.25rem;background:#4caf50;color:#fff;border-color:#2e7d32;';
    rightBtn.addEventListener('mousedown', () => { direction = 1; });
    rightBtn.addEventListener('mouseup', () => { direction = 0; });
    rightBtn.addEventListener('mouseleave', () => { direction = 0; });
    rightBtn.addEventListener('touchstart', (e) => { e.preventDefault(); direction = 1; });
    rightBtn.addEventListener('touchend', () => { direction = 0; });
    btnWrap.appendChild(leftBtn);
    btnWrap.appendChild(rightBtn);
    mount.appendChild(btnWrap);

    document.addEventListener('mouseup', () => { direction = 0; });

    canvas = document.createElement('canvas');
    canvas.width = 620;
    canvas.height = 520;
    canvas.style.cssText = 'display:block;width:100%;max-width:620px;height:520px;margin:0 auto;touch-action:none;';
    mount.appendChild(canvas);
    ctx = canvas.getContext('2d');

    vinePoints = [{ x: canvas.width / 2, y: canvas.height - 80 }];

    requestAnimationFrame(gameLoop);
  }

  function gameLoop() {
    if (gameOver) return;

    frameCount++;
    if (frameCount % 2 !== 0) {
      draw();
      requestAnimationFrame(gameLoop);
      return;
    }

    const speed = 2.5;
    const last = vinePoints[vinePoints.length - 1];
    const drift = (Math.random() - 0.5) * 3;
    const newX = Math.max(35, Math.min(canvas.width - 35, last.x + direction * 10 + drift));
    const newY = last.y - speed;
    vinePoints.push({ x: newX, y: newY });

    if (newY < sunY + 60) {
      const sunX = canvas.width / 2;
      const dx = newX - sunX;
      if (Math.abs(dx) < 90) {
        gameOver = true;
        showWinUI('🌿', 'Vine Reached the Sun!', 'Vines grow toward sunlight, wrapping around trees for support!');
        recordComplete(slug, progressUrl, csrfToken, {});
        return;
      }
    }

    if (newY < -30) {
      vinePoints = [{ x: canvas.width / 2, y: canvas.height - 80 }];
    }

    draw();
    requestAnimationFrame(gameLoop);
  }

  function draw() {
    ctx.fillStyle = '#5dade2';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.fillStyle = '#558b2f';
    ctx.fillRect(0, canvas.height - 100, canvas.width, 120);

    ctx.fillStyle = '#ffeb3b';
    ctx.shadowColor = '#ffc107';
    ctx.shadowBlur = 25;
    ctx.beginPath();
    ctx.arc(canvas.width / 2, sunY, 55, 0, Math.PI * 2);
    ctx.fill();
    ctx.shadowBlur = 0;

    ctx.strokeStyle = '#1b5e20';
    ctx.lineWidth = 14;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.beginPath();
    ctx.moveTo(vinePoints[0].x, vinePoints[0].y);
    vinePoints.forEach((p, i) => {
      if (i > 0) ctx.lineTo(p.x, p.y);
    });
    ctx.stroke();

    ctx.fillStyle = '#2e7d32';
    vinePoints.forEach((p, i) => {
      if (i % 3 === 0) {
        ctx.beginPath();
        ctx.arc(p.x, p.y, 7, 0, Math.PI * 2);
        ctx.fill();
      }
    });
  }

  init();
})();
