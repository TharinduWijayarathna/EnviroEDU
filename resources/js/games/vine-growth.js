/**
 * How a Vine Grows – Canvas-based visualization of vine growth stages.
 * Anchor → Climb → Reach sunlight. Clean, professional presentation.
 */
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  const STAGES = [
    { id: 'anchor', label: 'Anchor', desc: 'Roots attach to the tree base' },
    { id: 'climb', label: 'Climb', desc: 'Vine wraps around the trunk' },
    { id: 'sun', label: 'Reach sunlight', desc: 'Leaves spread toward light' },
  ];

  let canvas, ctx;
  let currentStage = 0;
  let stageProgress = 0;
  let animating = false;
  let w = 640;
  let h = 400;

  function init() {
    mount.innerHTML = '';
    mount.style.cssText =
      'position:relative;min-height:650px;background:linear-gradient(180deg,#e8eef4 0%,#d4e4d9 50%,#c5d9c8 100%);border-radius:12px;overflow:hidden;padding:28px;';

    const header = document.createElement('div');
    header.style.cssText =
      'margin-bottom:24px;text-align:center;';
    header.innerHTML = `
      <h2 style="font-size:1.35rem;font-weight:600;color:#2d4a3e;margin:0 0 4px;letter-spacing:-0.02em;">How a Vine Grows Around a Tree</h2>
      <p style="font-size:0.9rem;color:#5a6b5d;margin:0;font-weight:500;">Three stages: anchor, climb, reach sunlight</p>
    `;
    mount.appendChild(header);

    const canvasWrap = document.createElement('div');
    canvasWrap.style.cssText =
      'position:relative;max-width:640px;margin:0 auto 24px;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);background:#fafbf9;';
    canvas = document.createElement('canvas');
    canvas.width = w;
    canvas.height = h;
    canvas.style.cssText = 'display:block;width:100%;height:auto;';
    canvasWrap.appendChild(canvas);
    mount.appendChild(canvasWrap);

    const stageLabel = document.createElement('div');
    stageLabel.id = 'vg-stage-label';
    stageLabel.style.cssText =
      'text-align:center;font-size:1rem;font-weight:600;color:#3d5a4a;margin-bottom:20px;min-height:24px;';
    stageLabel.textContent = STAGES[0].desc;
    mount.appendChild(stageLabel);

    const controls = document.createElement('div');
    controls.style.cssText = 'display:flex;flex-wrap:wrap;gap:12px;justify-content:center;align-items:center;';
    const nextBtn = document.createElement('button');
    nextBtn.className = 'eco-game-btn';
    nextBtn.textContent = 'Next stage';
    nextBtn.style.cssText =
      'background:#3d5a4a;color:#fff;border:none;padding:12px 28px;border-radius:8px;font-weight:600;font-size:0.95rem;cursor:pointer;transition:opacity 0.2s;';
    nextBtn.onclick = advanceStage;
    controls.appendChild(nextBtn);

    const doneBtn = document.createElement('button');
    doneBtn.id = 'vg-done-btn';
    doneBtn.className = 'eco-game-btn';
    doneBtn.textContent = 'Complete';
    doneBtn.style.cssText =
      'display:none;background:#2e7d32;color:#fff;border:none;padding:12px 28px;border-radius:8px;font-weight:600;font-size:0.95rem;cursor:pointer;';
    doneBtn.onclick = () => {
      showWinUI('✓', 'Complete', 'Vines anchor at the base, wrap around the trunk, and grow toward sunlight.');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    controls.appendChild(doneBtn);
    mount.appendChild(controls);

    window.addEventListener('resize', onResize);
    draw();
  }

  function onResize() {
    if (!canvas || !canvas.parentElement) return;
    const rect = canvas.parentElement.getBoundingClientRect();
    if (rect.width > 0) {
      w = Math.min(640, Math.floor(rect.width));
      h = Math.floor((w / 640) * 400);
      canvas.width = w;
      canvas.height = h;
      draw();
    }
  }

  function advanceStage() {
    if (animating || currentStage >= STAGES.length) return;
    animating = true;
    const duration = 1400;
    const start = performance.now();

    function tick(now) {
      const t = Math.min(1, (now - start) / duration);
      const eased = 1 - Math.pow(1 - t, 2);
      stageProgress = (currentStage + eased) / 3;
      draw();
      if (t < 1) {
        requestAnimationFrame(tick);
      } else {
        currentStage++;
        animating = false;
        const label = document.getElementById('vg-stage-label');
        const doneBtn = document.getElementById('vg-done-btn');
        if (label) {
          if (currentStage < STAGES.length) {
            label.textContent = STAGES[currentStage].desc;
          } else {
            label.textContent = 'Vines use the tree for support and grow toward light.';
            if (doneBtn) doneBtn.style.display = 'inline-block';
          }
        }
      }
    }
    requestAnimationFrame(tick);
  }

  function draw() {
    if (!ctx) ctx = canvas.getContext('2d');
    if (!ctx) return;

    const cx = w / 2;
    const groundH = 70;
    const trunkW = 44;
    const trunkH = h - groundH - 50;

    ctx.clearRect(0, 0, w, h);

    // Sky gradient
    const skyGrad = ctx.createLinearGradient(0, 0, 0, h);
    skyGrad.addColorStop(0, '#e8f0f5');
    skyGrad.addColorStop(0.6, '#dce8e0');
    skyGrad.addColorStop(1, '#c8dcc8');
    ctx.fillStyle = skyGrad;
    ctx.fillRect(0, 0, w, h);

    // Sun – simple circle with rays
    const sunX = cx + w * 0.35;
    const sunY = 55;
    ctx.save();
    ctx.fillStyle = '#f5e6a3';
    ctx.strokeStyle = '#e8d88a';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.arc(sunX, sunY, 28, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();
    for (let i = 0; i < 8; i++) {
      const a = (i / 8) * Math.PI * 2;
      ctx.beginPath();
      ctx.moveTo(sunX + Math.cos(a) * 32, sunY + Math.sin(a) * 32);
      ctx.lineTo(sunX + Math.cos(a) * 42, sunY + Math.sin(a) * 42);
      ctx.stroke();
    }
    ctx.restore();

    // Ground
    const groundGrad = ctx.createLinearGradient(0, h - groundH, 0, h);
    groundGrad.addColorStop(0, '#8b7355');
    groundGrad.addColorStop(0.5, '#6f5b44');
    groundGrad.addColorStop(1, '#5a4a38');
    ctx.fillStyle = groundGrad;
    ctx.beginPath();
    ctx.moveTo(0, h);
    ctx.lineTo(0, h - groundH);
    ctx.lineTo(w, h - groundH);
    ctx.lineTo(w, h);
    ctx.closePath();
    ctx.fill();

    // Tree trunk
    const trunkLeft = cx - trunkW / 2;
    const trunkGrad = ctx.createLinearGradient(trunkLeft, 0, trunkLeft + trunkW, 0);
    trunkGrad.addColorStop(0, '#4a3f35');
    trunkGrad.addColorStop(0.3, '#5c5044');
    trunkGrad.addColorStop(0.7, '#4a3f35');
    trunkGrad.addColorStop(1, '#3d342c');
    ctx.fillStyle = trunkGrad;
    ctx.fillRect(trunkLeft, h - groundH - trunkH, trunkW, trunkH);
    ctx.fillStyle = 'rgba(0,0,0,0.15)';
    ctx.fillRect(trunkLeft, h - groundH - trunkH, trunkW * 0.3, trunkH);

    // Vine – anchor (roots)
    if (stageProgress > 0.08) {
      const anchorAlpha = Math.min(1, (stageProgress - 0.08) / 0.15);
      ctx.save();
      ctx.globalAlpha = anchorAlpha;
      ctx.fillStyle = '#3d5a2e';
      ctx.strokeStyle = '#2d4220';
      ctx.lineWidth = 2;
      const baseY = h - groundH - 20;
      ctx.beginPath();
      ctx.ellipse(cx, baseY, 38, 12, 0, 0, Math.PI * 2);
      ctx.fill();
      ctx.stroke();
      ctx.restore();
    }

    // Vine – spiral climb
    if (stageProgress > 0.35) {
      const climbProgress = Math.min(1, (stageProgress - 0.35) / 0.5);
      ctx.save();
      ctx.strokeStyle = '#3d5a2e';
      ctx.lineWidth = 10;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.beginPath();
      const startY = h - groundH - 50;
      const endY = 80;
      const segments = 40;
      for (let i = 0; i <= segments; i++) {
        const t = (i / segments) * climbProgress;
        const y = startY - t * (startY - endY);
        const angle = t * Math.PI * 2.2;
        const x = cx + Math.cos(angle) * 28;
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
      }
      ctx.stroke();
      ctx.restore();
    }

    // Vine – leaves
    if (stageProgress > 0.88) {
      const leafProgress = Math.min(1, (stageProgress - 0.88) / 0.12);
      const leafAlpha = Math.min(1, leafProgress * 4);
      ctx.save();
      ctx.globalAlpha = leafAlpha;
      const leafPositions = [
        { x: cx - 38, y: 95, rot: -18 },
        { x: cx - 18, y: 78, rot: 0 },
        { x: cx + 8, y: 72, rot: 12 },
        { x: cx + 35, y: 82, rot: 22 },
        { x: cx + 48, y: 100, rot: 35 },
      ];
      leafPositions.forEach((p) => {
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate((p.rot * Math.PI) / 180);
        ctx.fillStyle = '#4a6b3a';
        ctx.strokeStyle = '#2d4220';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        ctx.ellipse(0, 0, 14, 7, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.stroke();
        ctx.restore();
      });
      ctx.restore();
    }
  }

  init();
})();
