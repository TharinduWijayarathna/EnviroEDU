/**
 * Day and Night – Tap the sunny side, then the dark side!
 * Simple: tap directly on Earth where it's daytime, then where it's nighttime.
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let canvas, ctx;
  let foundDay = false;
  let foundNight = false;
  let starField = [];
  let earthTexture = null;

  const sunX = 110;
  const earthX = 450;
  const earthY = 220;
  const earthR = 95;

  function createEarthTexture() {
    const size = 256;
    const tex = document.createElement('canvas');
    tex.width = size;
    tex.height = size;
    const t = tex.getContext('2d');

    const oceanGrad = t.createLinearGradient(0, 0, size, 0);
    oceanGrad.addColorStop(0, '#0d47a1');
    oceanGrad.addColorStop(0.2, '#1565c0');
    oceanGrad.addColorStop(0.4, '#1976d2');
    oceanGrad.addColorStop(0.5, '#1e88e5');
    oceanGrad.addColorStop(0.7, '#2196f3');
    oceanGrad.addColorStop(0.85, '#1565c0');
    oceanGrad.addColorStop(1, '#0d47a1');
    t.fillStyle = oceanGrad;
    t.fillRect(0, 0, size, size);

    t.fillStyle = 'rgba(46, 125, 50, 0.85)';
    t.beginPath();
    t.ellipse(size * 0.25, size * 0.35, size * 0.22, size * 0.18, 0.3, 0, Math.PI * 2);
    t.fill();
    t.beginPath();
    t.ellipse(size * 0.72, size * 0.55, size * 0.18, size * 0.15, -0.2, 0, Math.PI * 2);
    t.fill();
    t.beginPath();
    t.ellipse(size * 0.55, size * 0.22, size * 0.2, size * 0.14, 0.1, 0, Math.PI * 2);
    t.fill();
    t.fillStyle = 'rgba(97, 97, 97, 0.7)';
    t.beginPath();
    t.ellipse(size * 0.4, size * 0.7, size * 0.25, size * 0.12, -0.1, 0, Math.PI * 2);
    t.fill();
    t.fillStyle = 'rgba(139, 90, 43, 0.6)';
    t.beginPath();
    t.ellipse(size * 0.65, size * 0.35, size * 0.15, size * 0.2, 0.2, 0, Math.PI * 2);
    t.fill();

    t.fillStyle = 'rgba(255, 255, 255, 0.25)';
    t.beginPath();
    t.ellipse(size * 0.3, size * 0.25, size * 0.12, size * 0.08, 0, 0, Math.PI * 2);
    t.fill();
    t.beginPath();
    t.ellipse(size * 0.75, size * 0.4, size * 0.1, size * 0.06, 0, 0, Math.PI * 2);
    t.fill();

    return tex;
  }

  function isTapOnEarth(px, py) {
    const dx = px - earthX;
    const dy = py - earthY;
    return dx * dx + dy * dy <= earthR * earthR;
  }

  function isDaySide(px) {
    return px < earthX;
  }

  function init() {
    earthTexture = createEarthTexture();
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#0a0a1a 0%,#1a1a3a 100%);border-radius:20px;overflow:hidden;padding:28px;';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'margin-bottom:16px;background:rgba(0,0,0,0.4);color:#fff;border-color:rgba(255,255,255,0.3);';
    header.innerHTML = '<h2 style="color:#fff;font-size:1.5rem;margin:0 0 8px;">🌍 Day and Night</h2><p style="color:#90caf9;margin:0;font-weight:600;">The Sun lights one side of Earth. Tap the sunny part, then the dark part.</p>';
    mount.appendChild(header);

    const instruction = document.createElement('div');
    instruction.id = 'dn-instruction';
    instruction.style.cssText = 'text-align:center;color:#ffc107;font-weight:700;font-size:1.2rem;margin-bottom:16px;min-height:32px;';
    instruction.textContent = 'Tap the sunny part of Earth (where it\'s daytime) ☀️';
    mount.appendChild(instruction);

    canvas = document.createElement('canvas');
    canvas.width = 640;
    canvas.height = 440;
    canvas.style.cssText = 'display:block;width:100%;max-width:640px;height:440px;margin:0 auto;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.4);cursor:pointer;';
    mount.appendChild(canvas);
    ctx = canvas.getContext('2d');

    for (let i = 0; i < 80; i++) {
      starField.push({
        x: Math.random() * 640,
        y: Math.random() * 440,
        r: 0.5 + Math.random() * 1.2,
        a: 0.35 + Math.random() * 0.5,
      });
    }

    const getPos = (e) => {
      const rect = canvas.getBoundingClientRect();
      const scaleX = canvas.width / rect.width;
      const scaleY = canvas.height / rect.height;
      const clientX = e.clientX ?? e.touches?.[0]?.clientX;
      const clientY = e.clientY ?? e.touches?.[0]?.clientY;
      return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top) * scaleY,
      };
    };

    const onTap = (e) => {
      e.preventDefault();
      const pos = getPos(e);
      if (!isTapOnEarth(pos.x, pos.y)) return;

      const instr = document.getElementById('dn-instruction');

      if (!foundDay) {
        if (isDaySide(pos.x)) {
          foundDay = true;
          instr.textContent = '✓ Good! Now tap the dark part (where it\'s nighttime) 🌙';
          instr.style.color = '#4caf50';
        } else {
          instr.style.animation = 'eco-shake 0.4s ease';
          instr.textContent = 'That\'s the dark side. Tap the sunny part (left side)! ☀️';
          setTimeout(() => { instr.style.animation = ''; }, 400);
        }
      } else if (!foundNight) {
        if (!isDaySide(pos.x)) {
          foundNight = true;
          showWinUI('🌍', 'You Got It!', 'The side facing the Sun has day. The other side has night. That\'s why we have day and night!');
          recordComplete(slug, progressUrl, csrfToken, {});
        } else {
          instr.style.animation = 'eco-shake 0.4s ease';
          instr.textContent = 'That\'s the sunny side. Tap the dark part (right side)! 🌙';
          setTimeout(() => { instr.style.animation = ''; }, 400);
        }
      }
    };

    canvas.addEventListener('click', onTap);
    canvas.addEventListener('touchend', (e) => { e.preventDefault(); onTap(e); }, { passive: false });

    requestAnimationFrame(draw);
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

    const sunY = canvas.height / 2;

    ctx.fillStyle = '#ffdd44';
    ctx.shadowColor = '#ffc107';
    ctx.shadowBlur = 50;
    ctx.beginPath();
    ctx.arc(sunX, sunY, 50, 0, Math.PI * 2);
    ctx.fill();
    ctx.shadowBlur = 0;
    ctx.strokeStyle = 'rgba(255, 200, 50, 0.5)';
    ctx.lineWidth = 2;
    ctx.stroke();

    ctx.save();
    ctx.translate(earthX, earthY);

    ctx.beginPath();
    ctx.arc(0, 0, earthR, 0, Math.PI * 2);
    ctx.closePath();
    ctx.save();
    ctx.clip();

    const scale = (earthR * 2) / earthTexture.width;
    ctx.scale(scale, scale);
    ctx.drawImage(earthTexture, -earthTexture.width / 2, -earthTexture.height / 2);
    ctx.restore();

    ctx.beginPath();
    ctx.arc(0, 0, earthR, 0, Math.PI * 2);
    ctx.closePath();
    ctx.save();
    ctx.clip();
    ctx.fillStyle = 'rgba(0, 0, 20, 0.82)';
    ctx.beginPath();
    ctx.arc(0, 0, earthR, -Math.PI / 2, Math.PI / 2);
    ctx.lineTo(0, 0);
    ctx.closePath();
    ctx.fill();
    ctx.restore();

    ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.arc(0, 0, earthR, 0, Math.PI * 2);
    ctx.stroke();

    ctx.restore();

    ctx.fillStyle = 'rgba(255,255,255,0.9)';
    ctx.font = 'bold 14px Quicksand, sans-serif';
    ctx.fillText('Sun', sunX - 18, sunY + 65);
    ctx.fillText('Earth', earthX - 24, earthY + earthR + 28);

    if (!foundDay) {
      ctx.fillStyle = 'rgba(255, 255, 255, 0.25)';
      ctx.font = 'bold 14px Quicksand, sans-serif';
      ctx.fillText('← tap here', earthX - earthR - 10, earthY);
    } else if (!foundNight) {
      ctx.fillStyle = 'rgba(255, 255, 255, 0.25)';
      ctx.font = 'bold 14px Quicksand, sans-serif';
      ctx.fillText('tap here →', earthX + earthR - 55, earthY);
    }

    requestAnimationFrame(draw);
  }

  init();
})();
