/**
 * How a Rainbow is Made – Sun, rain, and light refraction. Step-by-step demo with sun, clouds, and animated rainbow arc.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  const RAINBOW_COLORS = [0xff0000, 0xff7f00, 0xffff00, 0x00ff00, 0x0066ff, 0x4b0082, 0x8f00ff];

  let scene, camera, renderer;
  let sunMesh, cloudGroup, rainbowArcs = [], rainDrops = [];
  let step = 0;
  let rainbowReveal = 0;
  let labelEl, instructionEl;

  function createCloud(x, y, z, scale) {
    const group = new THREE.Group();
    const geo = new THREE.SphereGeometry(1, 12, 10);
    const mat = new THREE.MeshBasicMaterial({ color: 0xffffff });
    const parts = [
      [0, 0, 0], [0.6, 0.1, 0], [-0.4, 0.05, 0.2], [0.2, 0.15, -0.2], [-0.2, -0.05, 0.3], [0.5, -0.05, 0.1],
    ];
    parts.forEach(([px, py, pz]) => {
      const m = new THREE.Mesh(geo, mat.clone());
      m.position.set(px, py, pz);
      m.scale.setScalar(0.5 + Math.random() * 0.4);
      group.add(m);
    });
    group.position.set(x, y, z);
    group.scale.setScalar(scale);
    return group;
  }

  function buildRainbowArcs() {
    const radius = 3.5;
    const arcAngle = Math.PI * 0.92;
    RAINBOW_COLORS.forEach((color, i) => {
      const r = radius - i * 0.2;
      const curve = new THREE.EllipseCurve(0, 0, r, r * 0.5, Math.PI - arcAngle / 2, Math.PI + arcAngle / 2, false, 0);
      const pts = curve.getPoints(48);
      const geo = new THREE.BufferGeometry().setFromPoints(
        pts.map((p) => new THREE.Vector3(p.x, p.y + 0.5, -2.5))
      );
      const line = new THREE.Line(
        geo,
        new THREE.LineBasicMaterial({
          color,
          linewidth: 4,
          transparent: true,
          opacity: 0,
        })
      );
      line.position.set(0, 0, 0);
      rainbowArcs.push({ line, targetOpacity: 0.95 });
      scene.add(line);
    });
  }

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x87ceeb);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 0, 10);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 0.8));
    const dir = new THREE.DirectionalLight(0xffffee, 1);
    dir.position.set(-5, 5, 5);
    scene.add(dir);

    const ground = new THREE.Mesh(
      new THREE.PlaneGeometry(40, 40),
      new THREE.MeshStandardMaterial({ color: 0x5d9b4a })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -2.5;
    scene.add(ground);

    sunMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.7, 32, 32),
      new THREE.MeshBasicMaterial({ color: 0xffdd44 })
    );
    sunMesh.position.set(-4, 2.2, 1);
    sunMesh.visible = false;
    scene.add(sunMesh);

    cloudGroup = new THREE.Group();
    cloudGroup.add(createCloud(-1, 1.2, -1.5, 0.5));
    cloudGroup.add(createCloud(2, 0.8, -2, 0.45));
    cloudGroup.add(createCloud(0.5, 1.5, -1, 0.35));
    cloudGroup.visible = false;
    scene.add(cloudGroup);

    for (let i = 0; i < 30; i++) {
      const drop = new THREE.Mesh(
        new THREE.CylinderGeometry(0.02, 0.02, 0.15, 6),
        new THREE.MeshBasicMaterial({ color: 0xaaddff, transparent: true, opacity: 0.7 })
      );
      drop.rotation.x = Math.PI / 2;
      drop.position.set((Math.random() - 0.5) * 6, (Math.random() - 0.3) * 4, -1.5 - Math.random() * 2);
      drop.visible = false;
      drop.userData.speed = 0.02 + Math.random() * 0.03;
      drop.userData.offset = Math.random() * Math.PI * 2;
      rainDrops.push(drop);
      scene.add(drop);
    }

    buildRainbowArcs();

    labelEl = document.createElement('div');
    labelEl.style.cssText =
      'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,0.95);padding:10px 20px;border-radius:16px;font-weight:700;color:#1a3c34;z-index:10;text-align:center;max-width:92%;';
    labelEl.textContent = '🌈 How a rainbow is made: Sun + rain = light bends in water drops';
    mount.style.position = 'relative';
    mount.appendChild(labelEl);

    instructionEl = document.createElement('div');
    instructionEl.style.cssText =
      'position:absolute;top:52px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.7);color:#fff;padding:10px 18px;border-radius:12px;font-weight:600;z-index:10;text-align:center;';
    instructionEl.textContent = 'Click "Show Sun" to start.';
    mount.appendChild(instructionEl);

    const wrap = document.createElement('div');
    wrap.style.cssText =
      'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:10px;flex-wrap:wrap;justify-content:center;z-index:10;';

    const steps = [
      { id: 'sun', label: '☀️ Show Sun', action: () => setStep(1) },
      { id: 'clouds', label: '☁️ Add Clouds', action: () => setStep(2) },
      { id: 'rain', label: '🌧️ Add Rain', action: () => setStep(3) },
      { id: 'rainbow', label: '🌈 Show Rainbow', action: () => setStep(4) },
    ];
    steps.forEach((s) => {
      const btn = document.createElement('button');
      btn.textContent = s.label;
      btn.dataset.step = s.id;
      btn.style.cssText =
        'padding:12px 18px;border-radius:18px;font-weight:700;background:#fff;border:3px solid #4caf50;color:#1a3c34;cursor:pointer;';
      btn.onclick = s.action;
      wrap.appendChild(btn);
    });

    const doneBtn = document.createElement('button');
    doneBtn.textContent = "✓ I've learned!";
    doneBtn.style.cssText =
      'padding:12px 22px;border-radius:18px;font-weight:700;background:#2196f3;color:#fff;border:none;cursor:pointer;';
    doneBtn.onclick = () => {
      showWinUI('🌈', 'Great Job!', 'You learned how rainbows are made from light and water!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    wrap.appendChild(doneBtn);
    mount.appendChild(wrap);

    animate();
  }

  function setStep(n) {
    step = Math.max(step, n);
    if (step >= 1) {
      sunMesh.visible = true;
      instructionEl.textContent = 'Sun shines. Click "Add Clouds" next.';
    }
    if (step >= 2) {
      cloudGroup.visible = true;
      instructionEl.textContent = 'Clouds bring rain. Click "Add Rain".';
    }
    if (step >= 3) {
      rainDrops.forEach((d) => { d.visible = true; });
      instructionEl.textContent = 'Raindrops in the air. Click "Show Rainbow".';
    }
    if (step >= 4) {
      rainbowReveal = 1;
      instructionEl.textContent = 'Light bends in water drops — we see a rainbow!';
    }
  }

  function animate() {
    requestAnimationFrame(animate);
    const t = performance.now() * 0.001;

    if (sunMesh && sunMesh.visible) {
      sunMesh.rotation.y += 0.008;
    }
    if (cloudGroup && cloudGroup.visible) {
      cloudGroup.position.x = Math.sin(t * 0.3) * 0.15;
    }
    rainDrops.forEach((drop, i) => {
      if (!drop.visible) return;
      drop.position.y -= drop.userData.speed;
      if (drop.position.y < -3) drop.position.y = 2.5;
    });

    if (rainbowReveal < 1) {
      rainbowReveal = Math.min(1, rainbowReveal + 0.015);
    }
    rainbowArcs.forEach(({ line, targetOpacity }, i) => {
      const mat = line.material;
      if (mat.opacity < targetOpacity) {
        mat.opacity = Math.min(targetOpacity, mat.opacity + 0.04 * (1 - i * 0.05));
      }
      mat.opacity *= rainbowReveal;
    });

    renderer.render(scene, camera);
  }

  init();
})();
