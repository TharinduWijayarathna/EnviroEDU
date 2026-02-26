/**
 * How a Seed Grows – interactive stages: seed → sprout → plant.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) {
    return;
  }
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene;
  let camera;
  let renderer;
  let stage = 0;
  let animating = false;
  let swayTarget = 0;
  let instructionEl;
  let doneBtn;

  let seedMesh;
  let soilLayer;
  let plantGroup;
  let stemMesh;
  let flowerGroup;
  const cloudGroup = new THREE.Group();

  const steps = ['soil', 'water', 'sun'];

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x87ceeb);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 2.2, 6.5);
    camera.lookAt(0, 0.8, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    mount.appendChild(renderer.domElement);

    const ground = new THREE.Mesh(
      new THREE.PlaneGeometry(20, 20),
      new THREE.MeshStandardMaterial({ color: 0x8d6e63 })
    );
    ground.rotation.x = -Math.PI / 2;
    scene.add(ground);

    const light = new THREE.DirectionalLight(0xffffff, 0.9);
    light.position.set(5, 10, 5);
    scene.add(light);
    scene.add(new THREE.AmbientLight(0xffffff, 0.6));

    soilLayer = new THREE.Mesh(
      new THREE.CylinderGeometry(0.62, 0.56, 0.12, 24),
      new THREE.MeshStandardMaterial({ color: 0x5d4037, roughness: 0.95 })
    );
    soilLayer.position.y = 0.9;
    soilLayer.visible = false;
    scene.add(soilLayer);

    seedMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.25, 16, 16),
      new THREE.MeshStandardMaterial({ color: 0x5d4037 })
    );
    seedMesh.scale.set(1.1, 0.7, 1);
    seedMesh.position.y = 0.95;
    scene.add(seedMesh);

    plantGroup = new THREE.Group();
    plantGroup.position.y = 0.95;
    scene.add(plantGroup);

    createClouds();

    addUI();
    mount.style.position = 'relative';
    window.addEventListener('resize', onResize);
    animate();
  }

  function createClouds() {
    for (let i = 0; i < 4; i++) {
      const puff = new THREE.Mesh(
        new THREE.SphereGeometry(0.4 + i * 0.08, 10, 10),
        new THREE.MeshBasicMaterial({ color: 0xffffff, transparent: true, opacity: 0.9 })
      );
      puff.position.set(i * 0.5 - 1, 3 + (i % 2) * 0.2, -2.2);
      cloudGroup.add(puff);
    }
    scene.add(cloudGroup);
  }

  function addUI() {
    instructionEl = document.createElement('div');
    instructionEl.style.cssText =
      'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,0.95);padding:12px 20px;border-radius:16px;font-weight:700;color:#1a3c34;z-index:10;text-align:center;max-width:90%;';
    mount.appendChild(instructionEl);

    const wrap = document.createElement('div');
    wrap.style.cssText =
      'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:12px;flex-wrap:wrap;justify-content:center;z-index:10;';
    const steps = [
      { label: '🌱 Add soil', id: 'soil' },
      { label: '💧 Add water', id: 'water' },
      { label: '☀️ Add sun', id: 'sun' },
    ];

    steps.forEach((s, index) => {
      const btn = document.createElement('button');
      btn.textContent = s.label;
      btn.dataset.step = s.id;
      btn.style.cssText =
        'padding:12px 20px;border-radius:20px;font-weight:700;background:#fff;border:3px solid #4caf50;cursor:pointer;';
      btn.onclick = () => onStep(s.id);
      if (index !== 0) {
        btn.disabled = true;
        btn.style.opacity = '0.55';
      }
      wrap.appendChild(btn);
    });

    doneBtn = document.createElement('button');
    doneBtn.textContent = "✓ I've learned!";
    doneBtn.disabled = true;
    doneBtn.style.cssText =
      'padding:12px 22px;border-radius:20px;font-weight:700;background:#1976d2;color:#fff;border:3px solid #1565c0;cursor:not-allowed;opacity:0.6;';
    doneBtn.onclick = () => {
      showWinUI('🌱', 'Great Job!', 'You learned how a seed grows with soil, water, and sun!');
      recordComplete(slug, progressUrl, csrfToken, { stage });
    };
    wrap.appendChild(doneBtn);

    mount.appendChild(wrap);
    updateInstruction();
  }

  function onStep(id) {
    if (animating || stage >= steps.length) {
      return;
    }

    const expected = steps[stage];
    if (id !== expected) {
      updateInstruction(`Try "${labelForStep(expected)}" next.`);
      return;
    }

    stage++;
    runStageAnimation(id);
    unlockNextButton();
    updateInstruction();

    if (stage >= steps.length) {
      doneBtn.disabled = false;
      doneBtn.style.opacity = '1';
      doneBtn.style.cursor = 'pointer';
    }
  }

  function unlockNextButton() {
    const buttons = mount.querySelectorAll('button[data-step]');
    buttons.forEach((btn, idx) => {
      if (idx < stage) {
        btn.disabled = true;
        btn.style.opacity = '0.7';
        return;
      }
      if (idx === stage) {
        btn.disabled = false;
        btn.style.opacity = '1';
      }
    });
  }

  function labelForStep(id) {
    if (id === 'soil') {
      return 'Add soil';
    }
    if (id === 'water') {
      return 'Add water';
    }
    return 'Add sun';
  }

  function updateInstruction(extra = '') {
    if (!instructionEl) {
      return;
    }
    if (stage === 0) {
      instructionEl.textContent = 'Step 1: Add soil so the seed has a home.';
      return;
    }
    if (stage === 1) {
      instructionEl.textContent = 'Step 2: Add water to wake up the seed.';
      return;
    }
    if (stage === 2) {
      instructionEl.textContent = 'Step 3: Add sunlight to help the plant grow.';
      return;
    }
    instructionEl.textContent = extra || 'Great! The plant is fully grown. Click "I\'ve learned" to finish.';
  }

  function runStageAnimation(id) {
    if (id === 'soil') {
      animateSoil();
      return;
    }
    if (id === 'water') {
      animateSprout();
      return;
    }
    animatePlantGrowth();
  }

  function animateSoil() {
    animating = true;
    soilLayer.visible = true;
    soilLayer.scale.set(1, 0.2, 1);
    let t = 0;
    function tick() {
      t += 0.05;
      soilLayer.scale.y = Math.min(1, 0.2 + t);
      if (t < 0.8) {
        requestAnimationFrame(tick);
      } else {
        animating = false;
      }
    }
    tick();
  }

  function animateSprout() {
    animating = true;
    plantGroup.clear();

    stemMesh = new THREE.Mesh(
      new THREE.CylinderGeometry(0.05, 0.07, 0.45, 8),
      new THREE.MeshStandardMaterial({ color: 0x558b2f })
    );
    stemMesh.position.y = 0.2;
    stemMesh.scale.y = 0.01;
    plantGroup.add(stemMesh);

    const leaf = new THREE.Mesh(
      new THREE.SphereGeometry(0.15, 8, 8),
      new THREE.MeshStandardMaterial({ color: 0x2e7d32 })
    );
    leaf.scale.set(1.2, 0.35, 1);
    leaf.position.set(0.12, 0.42, 0);
    leaf.scale.multiplyScalar(0.01);
    plantGroup.add(leaf);

    let t = 0;
    function tick() {
      t += 0.035;
      const p = Math.min(1, t);
      stemMesh.scale.y = p;
      leaf.scale.set(1.2 * p, 0.35 * p, p);
      if (t < 1) {
        requestAnimationFrame(tick);
      } else {
        animating = false;
        swayTarget = 0.05;
      }
    }
    tick();
  }

  function animatePlantGrowth() {
    animating = true;
    plantGroup.clear();

    stemMesh = new THREE.Mesh(
      new THREE.CylinderGeometry(0.07, 0.1, 1.05, 10),
      new THREE.MeshStandardMaterial({ color: 0x4f8a2f })
    );
    stemMesh.position.y = 0.55;
    stemMesh.scale.y = 0.01;
    plantGroup.add(stemMesh);

    for (let i = 0; i < 4; i++) {
      const leaf = new THREE.Mesh(
        new THREE.SphereGeometry(0.2, 10, 10),
        new THREE.MeshStandardMaterial({ color: 0x43a047 })
      );
      leaf.scale.set(1.3, 0.35, 1);
      leaf.position.set(0.18 * (i % 2 ? 1 : -1), 0.55 + i * 0.22, 0);
      leaf.rotation.z = i % 2 ? -0.5 : 0.5;
      leaf.scale.multiplyScalar(0.01);
      plantGroup.add(leaf);
    }

    flowerGroup = new THREE.Group();
    flowerGroup.position.y = 1.18;
    flowerGroup.scale.setScalar(0.01);
    const center = new THREE.Mesh(
      new THREE.SphereGeometry(0.11, 10, 10),
      new THREE.MeshStandardMaterial({ color: 0xffeb3b })
    );
    flowerGroup.add(center);
    for (let i = 0; i < 6; i++) {
      const angle = (i / 6) * Math.PI * 2;
      const petal = new THREE.Mesh(
        new THREE.SphereGeometry(0.13, 8, 8),
        new THREE.MeshStandardMaterial({ color: 0xf06292 })
      );
      petal.scale.set(1, 0.45, 1);
      petal.position.set(Math.cos(angle) * 0.18, 0.03, Math.sin(angle) * 0.18);
      flowerGroup.add(petal);
    }
    plantGroup.add(flowerGroup);

    let t = 0;
    function tick() {
      t += 0.03;
      const p = Math.min(1, t);
      stemMesh.scale.y = p;
      plantGroup.children.forEach((child) => {
        if (child !== stemMesh) {
          child.scale.setScalar(Math.max(0.01, p));
        }
      });
      if (t < 1) {
        requestAnimationFrame(tick);
      } else {
        animating = false;
        swayTarget = 0.09;
      }
    }
    tick();
  }

  function onResize() {
    if (!renderer || !camera) {
      return;
    }
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
  }

  function animate() {
    requestAnimationFrame(animate);
    const t = Date.now() * 0.001;
    cloudGroup.position.x = Math.sin(t * 0.18) * 0.55;
    if (plantGroup) {
      plantGroup.rotation.y += 0.003;
      plantGroup.rotation.z = Math.sin(t * 1.4) * swayTarget;
    }
    renderer.render(scene, camera);
  }

  init();
})();
