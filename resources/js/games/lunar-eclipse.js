/**
 * Lunar Eclipse – Position the Moon in Earth's shadow!
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let canvas, ctx;
  let moonAngle = Math.PI * 0.5;
  let dragging = false;
  let lastAngle = 0;
  let eclipseAchieved = false;
  let eclipseTimer = 0;
  let starField = [];

  const ORBIT_R = 125;
  const CENTER_X = 350;
  const CENTER_Y = 250;
  const EARTH_R = 52;
  const MOON_R = 24;

  function init() {
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#0a0a1a 0%,#1a1a3a 100%);border-radius:20px;overflow:hidden;';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'position:absolute;top:16px;left:50%;transform:translateX(-50%);z-index:10;max-width:92%;';
    header.textContent = '🌒 Drag the Moon into Earth\'s shadow to see a lunar eclipse!';
    mount.appendChild(header);

    canvas = document.createElement('canvas');
    canvas.width = 700;
    canvas.height = 500;
    canvas.style.cssText = 'display:block;width:100%;max-width:700px;height:500px;margin:0 auto;cursor:grab;';
    mount.appendChild(canvas);
    ctx = canvas.getContext('2d');

    for (let i = 0; i < 80; i++) {
      starField.push({
        x: Math.random() * 700,
        y: Math.random() * 500,
        r: 0.5 + Math.random(),
        a: 0.25 + Math.random() * 0.5,
      });
    }

    canvas.addEventListener('mousedown', onDown);
    canvas.addEventListener('mousemove', onMove);
    canvas.addEventListener('mouseup', onUp);
    canvas.addEventListener('mouseleave', onUp);
    canvas.addEventListener('touchstart', (e) => { e.preventDefault(); onDown(e.touches[0]); }, { passive: false });
    canvas.addEventListener('touchmove', (e) => { e.preventDefault(); onMove(e.touches[0]); }, { passive: false });
    canvas.addEventListener('touchend', (e) => { if (e.touches.length === 0) onUp(); }, { passive: false });

    requestAnimationFrame(draw);
  }

  function getAngle(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const x = (e.clientX - rect.left) * scaleX - CENTER_X;
    const y = (e.clientY - rect.top) * scaleY - CENTER_Y;
    return Math.atan2(y, x);
  }

  function onDown(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const px = (e.clientX - rect.left) * scaleX;
    const py = (e.clientY - rect.top) * scaleY;
    const mx = CENTER_X + Math.cos(moonAngle) * ORBIT_R;
    const my = CENTER_Y + Math.sin(moonAngle) * ORBIT_R;
    const dx = px - mx;
    const dy = py - my;
    if (Math.sqrt(dx * dx + dy * dy) < 55) {
      dragging = true;
      lastAngle = getAngle(e);
    }
  }

  function onMove(e) {
    if (!dragging) return;
    const angle = getAngle(e);
    moonAngle += angle - lastAngle;
    lastAngle = angle;
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

    const sunX = 75;
    const sunY = CENTER_Y;

    ctx.fillStyle = '#ffdd44';
    ctx.shadowColor = '#ffc107';
    ctx.shadowBlur = 35;
    ctx.beginPath();
    ctx.arc(sunX, sunY, 38, 0, Math.PI * 2);
    ctx.fill();
    ctx.shadowBlur = 0;

    ctx.fillStyle = '#1565c0';
    ctx.beginPath();
    ctx.arc(CENTER_X, CENTER_Y, EARTH_R, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = '#0d47a1';
    ctx.lineWidth = 3;
    ctx.stroke();

    const shadowGrad = ctx.createRadialGradient(
      CENTER_X - 60, CENTER_Y, 0,
      CENTER_X + 80, CENTER_Y, ORBIT_R + 60
    );
    shadowGrad.addColorStop(0, 'rgba(0,0,0,0.85)');
    shadowGrad.addColorStop(0.6, 'rgba(0,0,0,0.4)');
    shadowGrad.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = shadowGrad;
    ctx.beginPath();
    ctx.ellipse(CENTER_X + 40, CENTER_Y, ORBIT_R + 40, ORBIT_R * 0.8, 0, 0, Math.PI * 2);
    ctx.fill();

    const moonX = CENTER_X + Math.cos(moonAngle) * ORBIT_R;
    const moonY = CENTER_Y + Math.sin(moonAngle) * ORBIT_R;

    const sunToMoon = Math.atan2(moonY - sunY, moonX - sunX);
    let diff = Math.abs(sunToMoon - Math.PI);
    if (diff > Math.PI) diff = Math.PI * 2 - diff;
    const inShadow = diff < 0.5 && moonX > CENTER_X + 20;

    if (inShadow) {
      eclipseTimer += 0.016;
      if (eclipseTimer > 1.2 && !eclipseAchieved) {
        eclipseAchieved = true;
        showWinUI('🌒', 'Lunar Eclipse!', 'When Earth is between the Sun and Moon, Earth\'s shadow covers the Moon!');
        recordComplete(slug, progressUrl, csrfToken, {});
        return;
      }
    } else {
      eclipseTimer = 0;
    }

    ctx.fillStyle = eclipseTimer > 0.5 ? '#5c6bc0' : '#e0e0e0';
    ctx.beginPath();
    ctx.arc(moonX, moonY, MOON_R, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = '#9e9e9e';
    ctx.lineWidth = 2;
    ctx.stroke();

    ctx.fillStyle = '#fff';
    ctx.font = 'bold 15px Quicksand, sans-serif';
    ctx.fillText('Sun', sunX - 18, sunY + 58);
    ctx.fillText('Earth', CENTER_X - 24, CENTER_Y + EARTH_R + 28);
    ctx.fillText('Moon (drag me!)', moonX - 52, moonY - MOON_R - 12);

    requestAnimationFrame(draw);
  }

  init();
})();
