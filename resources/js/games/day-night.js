/**
 * Day and Night – Rotate Earth to find daytime and nighttime!
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let canvas, ctx;
  let earthRotation = 0;
  let foundDay = false;
  let foundNight = false;
  let starField = [];

  function init() {
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#0a0a1a 0%,#1a1a3a 100%);border-radius:20px;overflow:hidden;padding:28px;';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'margin-bottom:20px;background:rgba(0,0,0,0.4);color:#fff;border-color:rgba(255,255,255,0.3);';
    header.innerHTML = '<h2 style="color:#fff;font-size:1.5rem;margin:0 0 8px;">🌍 Day and Night</h2><p style="color:#90caf9;margin:0;font-weight:600;">Rotate Earth with the slider. Bright side = day. Dark side = night.</p>';
    mount.appendChild(header);

    const sliderWrap = document.createElement('div');
    sliderWrap.style.cssText = 'max-width:420px;margin:0 auto 24px;';
    sliderWrap.innerHTML = `
      <label style="color:#fff;font-weight:700;display:block;margin-bottom:10px;">Rotate Earth</label>
      <input type="range" id="dn-slider" min="0" max="360" value="0" style="width:100%;height:12px;border-radius:6px;accent-color:#4fc3f7;">
    `;
    mount.appendChild(sliderWrap);

    document.getElementById('dn-slider').addEventListener('input', (e) => {
      earthRotation = (parseInt(e.target.value, 10) / 360) * Math.PI * 2;
    });

    canvas = document.createElement('canvas');
    canvas.width = 600;
    canvas.height = 420;
    canvas.style.cssText = 'display:block;width:100%;max-width:600px;height:420px;margin:0 auto;border-radius:16px;';
    mount.appendChild(canvas);
    ctx = canvas.getContext('2d');

    for (let i = 0; i < 60; i++) {
      starField.push({
        x: Math.random() * 600,
        y: Math.random() * 420,
        r: 0.5 + Math.random(),
        a: 0.3 + Math.random() * 0.5,
      });
    }

    const btnWrap = document.createElement('div');
    btnWrap.style.cssText = 'display:flex;gap:14px;justify-content:center;margin-top:24px;flex-wrap:wrap;';
    const dayBtn = document.createElement('button');
    dayBtn.className = 'eco-game-btn';
    dayBtn.textContent = '☀️ I see the daytime side!';
    dayBtn.style.cssText = 'background:#ffc107;color:#1a1a1a;border-color:#f9a825;';
    dayBtn.onclick = () => {
      const daySide = Math.cos(earthRotation) > 0;
      if (daySide) {
        foundDay = true;
        dayBtn.style.background = '#4caf50';
        dayBtn.style.borderColor = '#2e7d32';
        dayBtn.textContent = '✓ Day found!';
        dayBtn.disabled = true;
        checkComplete();
      } else {
        dayBtn.style.animation = 'eco-shake 0.4s ease';
        dayBtn.style.background = '#ff5722';
        setTimeout(() => { dayBtn.style.background = '#ffc107'; dayBtn.style.animation = ''; }, 500);
      }
    };
    const nightBtn = document.createElement('button');
    nightBtn.className = 'eco-game-btn';
    nightBtn.textContent = '🌙 I see the nighttime side!';
    nightBtn.style.cssText = 'background:#5c6bc0;color:#fff;border-color:#3949ab;';
    nightBtn.onclick = () => {
      const nightSide = Math.cos(earthRotation) < 0;
      if (nightSide) {
        foundNight = true;
        nightBtn.style.background = '#4caf50';
        nightBtn.style.borderColor = '#2e7d32';
        nightBtn.textContent = '✓ Night found!';
        nightBtn.disabled = true;
        checkComplete();
      } else {
        nightBtn.style.animation = 'eco-shake 0.4s ease';
        nightBtn.style.background = '#ff5722';
        setTimeout(() => { nightBtn.style.background = '#5c6bc0'; nightBtn.style.animation = ''; }, 500);
      }
    };
    btnWrap.appendChild(dayBtn);
    btnWrap.appendChild(nightBtn);
    mount.appendChild(btnWrap);

    function checkComplete() {
      if (foundDay && foundNight) {
        showWinUI('🌍', 'You Got It!', 'Earth rotates! The side facing the Sun has day. The other side has night.');
        recordComplete(slug, progressUrl, csrfToken, {});
      }
    }

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

    const sunX = 100;
    const sunY = canvas.height / 2;
    const earthX = 420;
    const earthY = canvas.height / 2;
    const earthR = 85;

    ctx.fillStyle = '#ffdd44';
    ctx.shadowColor = '#ffc107';
    ctx.shadowBlur = 35;
    ctx.beginPath();
    ctx.arc(sunX, sunY, 45, 0, Math.PI * 2);
    ctx.fill();
    ctx.shadowBlur = 0;

    ctx.save();
    ctx.translate(earthX, earthY);
    ctx.rotate(earthRotation);

    const gradient = ctx.createLinearGradient(-earthR, 0, earthR, 0);
    gradient.addColorStop(0, '#1a5276');
    gradient.addColorStop(0.2, '#2874a6');
    gradient.addColorStop(0.4, '#2e86ab');
    gradient.addColorStop(0.5, '#148f77');
    gradient.addColorStop(0.65, '#1e8449');
    gradient.addColorStop(0.8, '#229954');
    gradient.addColorStop(1, '#1a5276');
    ctx.fillStyle = gradient;
    ctx.beginPath();
    ctx.arc(0, 0, earthR, 0, Math.PI * 2);
    ctx.fill();

    ctx.fillStyle = 'rgba(0,0,0,0.75)';
    ctx.beginPath();
    ctx.ellipse(0, 0, earthR, earthR * 0.5, 0, Math.PI / 2, Math.PI * 1.5);
    ctx.fill();

    ctx.restore();

    ctx.fillStyle = '#fff';
    ctx.font = 'bold 15px Quicksand, sans-serif';
    ctx.fillText('Sun', sunX - 18, sunY + 60);
    ctx.fillText('Earth', earthX - 24, earthY + earthR + 28);

    requestAnimationFrame(draw);
  }

  init();
})();
