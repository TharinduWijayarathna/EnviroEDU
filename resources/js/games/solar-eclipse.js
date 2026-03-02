/**
 * Solar Eclipse – Drag the Moon between the Sun and Earth to create an eclipse!
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let canvas, ctx;
  let moonX = 0;
  let moonY = 0;
  let dragging = false;
  let dragOffsetX = 0;
  let dragOffsetY = 0;
  let eclipseAchieved = false;
  let eclipseTimer = 0;
  let starField = [];

  const SUN_X = 150;
  const EARTH_X = 550;
  const CENTER_Y = 250;

  function init() {
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#0a0a1a 0%,#1a1a3a 50%,#0d1b2a 100%);border-radius:20px;overflow:hidden;';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'position:absolute;top:16px;left:50%;transform:translateX(-50%);z-index:10;max-width:92%;';
    header.textContent = '🌑 Drag the Moon between the Sun and Earth to create a solar eclipse!';
    mount.appendChild(header);

    canvas = document.createElement('canvas');
    canvas.width = 700;
    canvas.height = 500;
    canvas.style.cssText = 'display:block;width:100%;max-width:700px;height:500px;margin:0 auto;cursor:grab;';
    mount.appendChild(canvas);
    ctx = canvas.getContext('2d');

    for (let i = 0; i < 100; i++) {
      starField.push({
        x: Math.random() * 700,
        y: Math.random() * 500,
        r: 0.5 + Math.random() * 1,
        a: 0.3 + Math.random() * 0.5,
      });
    }

    moonX = 350;
    moonY = CENTER_Y;

    canvas.addEventListener('mousedown', onDown);
    canvas.addEventListener('mousemove', onMove);
    canvas.addEventListener('mouseup', onUp);
    canvas.addEventListener('mouseleave', onUp);
    canvas.addEventListener('touchstart', (e) => { e.preventDefault(); onDown(e.touches[0]); }, { passive: false });
    canvas.addEventListener('touchmove', (e) => { e.preventDefault(); onMove(e.touches[0]); }, { passive: false });
    canvas.addEventListener('touchend', (e) => { if (e.touches.length === 0) onUp(); }, { passive: false });

    requestAnimationFrame(draw);
  }

  function getMousePos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    return {
      x: (e.clientX - rect.left) * scaleX,
      y: (e.clientY - rect.top) * scaleY,
    };
  }

  function onDown(e) {
    const pos = getMousePos(e);
    const dx = pos.x - moonX;
    const dy = pos.y - moonY;
    if (Math.sqrt(dx * dx + dy * dy) < 45) {
      dragging = true;
      dragOffsetX = dx;
      dragOffsetY = dy;
    }
  }

  function onMove(e) {
    if (!dragging) return;
    const pos = getMousePos(e);
    moonX = pos.x - dragOffsetX;
    moonY = pos.y - dragOffsetY;
    moonX = Math.max(70, Math.min(630, moonX));
    moonY = Math.max(70, Math.min(430, moonY));
  }

  function onUp() {
    dragging = false;
  }

  function draw() {
    ctx.fillStyle = '#0a0a1a';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    starField.forEach((s) => {
      ctx.fillStyle = `rgba(255,255,255,${s.a})`;
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fill();
    });

    const sunR = 62;
    const earthR = 48;
    const moonR = 28;

    ctx.fillStyle = '#ffeb3b';
    ctx.shadowColor = '#ffc107';
    ctx.shadowBlur = 50;
    ctx.beginPath();
    ctx.arc(SUN_X, CENTER_Y, sunR, 0, Math.PI * 2);
    ctx.fill();
    ctx.shadowBlur = 0;

    ctx.fillStyle = '#2196f3';
    ctx.beginPath();
    ctx.arc(EARTH_X, CENTER_Y, earthR, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = '#1976d2';
    ctx.lineWidth = 3;
    ctx.stroke();

    const inLine = moonX > SUN_X + sunR && moonX < EARTH_X - earthR && Math.abs(moonY - CENTER_Y) < 55;
    if (inLine) {
      eclipseTimer += 0.016;
      if (eclipseTimer > 1.5 && !eclipseAchieved) {
        eclipseAchieved = true;
        ctx.fillStyle = 'rgba(0,0,0,0.75)';
        ctx.beginPath();
        ctx.arc(EARTH_X, CENTER_Y, earthR + 20, 0, Math.PI * 2);
        ctx.fill();
        showWinUI('🌑', 'Solar Eclipse!', 'The Moon blocks the Sun\'s light from reaching Earth. That\'s a solar eclipse!');
        recordComplete(slug, progressUrl, csrfToken, {});
        return;
      }
    } else {
      eclipseTimer = 0;
    }

    ctx.fillStyle = '#b0bec5';
    ctx.beginPath();
    ctx.arc(moonX, moonY, moonR, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = '#78909c';
    ctx.lineWidth = 2;
    ctx.stroke();

    if (inLine && eclipseTimer > 0.5) {
      ctx.fillStyle = 'rgba(0,0,0,0.6)';
      ctx.beginPath();
      ctx.arc(SUN_X, CENTER_Y, sunR - 8, 0, Math.PI * 2);
      ctx.fill();
    }

    ctx.fillStyle = '#fff';
    ctx.font = 'bold 15px Quicksand, sans-serif';
    ctx.fillText('Sun', SUN_X - 20, CENTER_Y + sunR + 28);
    ctx.fillText('Earth', EARTH_X - 24, CENTER_Y + earthR + 28);
    ctx.fillText('Moon (drag me!)', moonX - 50, moonY - moonR - 12);

    requestAnimationFrame(draw);
  }

  init();
})();
