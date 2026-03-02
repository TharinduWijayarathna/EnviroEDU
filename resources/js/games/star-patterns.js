/**
 * Star Patterns – Connect the stars to form the constellation!
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  const constellations = [
    { name: 'Big Dipper', points: [[0.2, 0.15], [0.35, 0.2], [0.5, 0.25], [0.65, 0.22], [0.6, 0.35], [0.4, 0.38], [0.25, 0.32]] },
    { name: 'Orion', points: [[0.5, 0.2], [0.45, 0.35], [0.55, 0.35], [0.5, 0.5], [0.42, 0.7], [0.58, 0.7], [0.5, 0.85]] },
    { name: 'Cassiopeia', points: [[0.2, 0.4], [0.35, 0.25], [0.5, 0.45], [0.65, 0.3], [0.8, 0.5]] },
  ];

  let currentIndex = 0;
  let connected = [];
  let completed = false;
  let starField = [];

  function init() {
    mount.innerHTML = '';
    mount.style.cssText = 'position:relative;min-height:650px;background:linear-gradient(180deg,#0a0a1a 0%,#1a1a3a 100%);border-radius:20px;overflow:hidden;padding:28px;';

    const header = document.createElement('div');
    header.className = 'eco-game-header';
    header.style.cssText = 'margin-bottom:20px;background:rgba(0,0,0,0.5);color:#fff;border-color:rgba(255,204,0,0.5);';
    header.innerHTML = '<h2 style="color:#ffcc00;font-size:1.5rem;margin:0 0 8px;">⭐ Connect the Stars</h2><p style="color:#90caf9;margin:0;font-weight:600;">Click stars in order to draw the constellation</p>';
    mount.appendChild(header);

    const progress = document.createElement('div');
    progress.id = 'sp-progress';
    progress.style.cssText = 'text-align:center;color:#ffcc00;font-weight:800;margin-bottom:20px;font-size:1.15rem;';
    mount.appendChild(progress);

    const canvasWrap = document.createElement('div');
    canvasWrap.id = 'sp-canvas-wrap';
    canvasWrap.style.cssText = 'max-width:520px;margin:0 auto;position:relative;';
    mount.appendChild(canvasWrap);

    const nextBtn = document.createElement('button');
    nextBtn.className = 'eco-game-btn';
    nextBtn.textContent = 'Next constellation →';
    nextBtn.style.cssText = 'display:block;margin:24px auto 0;background:#ffc107;color:#1a1a1a;border-color:#f9a825;';
    nextBtn.onclick = () => {
      currentIndex = Math.min(currentIndex + 1, constellations.length - 1);
      showConstellation();
    };

    const doneBtn = document.createElement('button');
    doneBtn.className = 'eco-game-btn';
    doneBtn.textContent = "✓ I've learned!";
    doneBtn.style.cssText = 'display:none;margin:24px auto 0;background:#2196f3;color:#fff;border-color:#1976d2;';
    doneBtn.onclick = () => {
      showWinUI('⭐', 'Star Expert!', 'You learned how to find constellations by connecting stars!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };

    mount.appendChild(nextBtn);
    mount.appendChild(doneBtn);

    function showConstellation() {
      connected = [];
      completed = false;
      const c = constellations[currentIndex];
      nextBtn.style.display = 'none';
      doneBtn.style.display = 'none';
      const wrap = document.getElementById('sp-canvas-wrap');
      wrap.innerHTML = '';

      const size = 480;
      const h = 380;
      const canvas = document.createElement('canvas');
      canvas.width = size;
      canvas.height = h;
      canvas.style.cssText = 'display:block;width:100%;border-radius:16px;background:#0d0d20;box-shadow:0 8px 24px rgba(0,0,0,0.4);';
      wrap.appendChild(canvas);
      const ctx = canvas.getContext('2d');

      starField = [];
      for (let i = 0; i < 50; i++) {
        starField.push({
          x: Math.random() * size,
          y: Math.random() * h,
          r: 0.5 + Math.random(),
          a: 0.3 + Math.random() * 0.5,
        });
      }

      const starPositions = c.points.map((p) => ({
        x: p[0] * size,
        y: p[1] * h,
        radius: 22,
      }));

      function draw() {
        ctx.fillStyle = '#0d0d20';
        ctx.fillRect(0, 0, size, h);

        starField.forEach((s) => {
          ctx.fillStyle = `rgba(255,255,255,${s.a})`;
          ctx.beginPath();
          ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
          ctx.fill();
        });

        ctx.strokeStyle = '#ffcc00';
        ctx.lineWidth = 3;
        ctx.setLineDash([6, 6]);
        if (connected.length >= 2) {
          ctx.beginPath();
          ctx.moveTo(starPositions[connected[0]].x, starPositions[connected[0]].y);
          for (let i = 1; i < connected.length; i++) {
            ctx.lineTo(starPositions[connected[i]].x, starPositions[connected[i]].y);
          }
          ctx.stroke();
        }
        ctx.setLineDash([]);

        starPositions.forEach((s, i) => {
          ctx.fillStyle = connected.includes(i) ? '#ffeb3b' : '#fff';
          ctx.beginPath();
          ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2);
          ctx.fill();
          ctx.strokeStyle = '#ffcc00';
          ctx.lineWidth = 2;
          ctx.stroke();
        });
      }

      canvas.addEventListener('click', (e) => {
        if (completed) return;
        const rect = canvas.getBoundingClientRect();
        const scaleX = size / rect.width;
        const scaleY = h / rect.height;
        const px = (e.clientX - rect.left) * scaleX;
        const py = (e.clientY - rect.top) * scaleY;

        for (let i = 0; i < starPositions.length; i++) {
          const s = starPositions[i];
          const dx = px - s.x;
          const dy = py - s.y;
          if (Math.sqrt(dx * dx + dy * dy) < s.radius) {
            if (i === connected.length) {
              connected.push(i);
            }
            break;
          }
        }
        draw();

        if (connected.length === starPositions.length) {
          completed = true;
          progress.textContent = `✓ ${c.name} complete!`;
          if (currentIndex < constellations.length - 1) {
            nextBtn.style.display = 'block';
          } else {
            doneBtn.style.display = 'block';
          }
        }
      });

      document.getElementById('sp-progress').textContent = `${c.name} (${connected.length}/${starPositions.length})`;
      draw();
    }

    showConstellation();
  }

  init();
})();
