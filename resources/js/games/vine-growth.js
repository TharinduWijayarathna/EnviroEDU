/**
 * How a Vine Grows Around a Tree – vine wraps around trunk.
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
  let treeGroup;
  let vineCurve;
  let instructionEl;
  let vineMesh;
  let rootMesh;
  let leafGroup;
  let phase = 0;
  let stage = 0;
  let vineProgress = 0;
  let vineTarget = 0;
  let leafTarget = 0;

  const stageSteps = ['anchor', 'climb', 'leaves'];

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x87ceeb);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 1.6, 5.5);
    camera.lookAt(0, 1.2, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 0.7));
    const dir = new THREE.DirectionalLight(0xffffff, 0.8);
    dir.position.set(5, 10, 5);
    scene.add(dir);

    const ground = new THREE.Mesh(
      new THREE.PlaneGeometry(20, 20),
      new THREE.MeshStandardMaterial({ color: 0x689f38 })
    );
    ground.rotation.x = -Math.PI / 2;
    scene.add(ground);

    const trunkRadius = 0.5;
    const trunkHeight = 2.6;
    treeGroup = new THREE.Group();
    const trunk = new THREE.Mesh(
      new THREE.CylinderGeometry(trunkRadius * 0.9, trunkRadius * 1.1, trunkHeight, 24),
      new THREE.MeshStandardMaterial({ color: 0x4e342e, roughness: 0.9, metalness: 0 })
    );
    trunk.position.y = trunkHeight / 2;
    trunk.castShadow = true;
    treeGroup.add(trunk);
    const canopy = new THREE.Mesh(
      new THREE.SphereGeometry(1.4, 24, 20),
      new THREE.MeshStandardMaterial({ color: 0x2e7d32, roughness: 0.85, metalness: 0 })
    );
    canopy.position.y = trunkHeight + 0.4;
    treeGroup.add(canopy);
    treeGroup.position.set(0, 0, 0);
    scene.add(treeGroup);

    const vineRadius = trunkRadius + 0.12;
    const spiralPoints = [];
    for (let i = 0; i <= 40; i++) {
      const t = (i / 40) * Math.PI * 2.5;
      const y = 0.02 + (trunkHeight * 0.92) * (i / 40);
      spiralPoints.push(new THREE.Vector3(
        Math.cos(t) * vineRadius,
        y,
        Math.sin(t) * vineRadius
      ));
    }
    vineCurve = new THREE.CatmullRomCurve3(spiralPoints);

    rootMesh = new THREE.Mesh(
      new THREE.TorusGeometry(vineRadius, 0.08, 12, 32),
      new THREE.MeshStandardMaterial({ color: 0x33691e, roughness: 0.8, metalness: 0 })
    );
    rootMesh.rotation.x = Math.PI / 2;
    rootMesh.position.y = 0.02;
    rootMesh.scale.setScalar(0.01);
    scene.add(rootMesh);

    leafGroup = new THREE.Group();
    leafGroup.visible = false;
    const leafPositions = [0.35, 0.5, 0.65, 0.78, 0.88, 0.96];
    leafPositions.forEach((u, i) => {
      const p = vineCurve.getPoint(u);
      const tangent = vineCurve.getTangent(u);
      const leaf = new THREE.Mesh(
        new THREE.SphereGeometry(0.18, 12, 10),
        new THREE.MeshStandardMaterial({ color: 0x388e3c, roughness: 0.7, metalness: 0 })
      );
      leaf.scale.set(1.8, 0.4, 1);
      leaf.position.copy(p);
      leaf.position.y += 0.05;
      leaf.position.addScaledVector(new THREE.Vector3(-tangent.z, 0, tangent.x), 0.25);
      leaf.rotation.z = i % 2 ? 0.5 : -0.5;
      leaf.scale.multiplyScalar(0.01);
      leafGroup.add(leaf);
    });
    scene.add(leafGroup);

    buildUI();
    updateInstruction();
    mount.style.position = 'relative';
    window.addEventListener('resize', onResize);

    animate();
  }

  function buildUI() {
    instructionEl = document.createElement('div');
    instructionEl.style.cssText =
      'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,0.95);padding:12px 20px;border-radius:16px;font-weight:700;color:#1a3c34;z-index:10;text-align:center;max-width:90%;';
    mount.appendChild(instructionEl);

    const wrap = document.createElement('div');
    wrap.style.cssText =
      'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:10px;flex-wrap:wrap;justify-content:center;z-index:10;';

    [
      { id: 'anchor', label: '🪵 Anchor at root' },
      { id: 'climb', label: '🌿 Climb the trunk' },
      { id: 'leaves', label: '🍃 Grow leaves' },
    ].forEach((item, index) => {
      const btn = document.createElement('button');
      btn.dataset.step = item.id;
      btn.textContent = item.label;
      btn.style.cssText =
        'padding:12px 18px;border-radius:18px;font-weight:700;background:#fff;border:3px solid #4caf50;cursor:pointer;';
      if (index !== 0) {
        btn.disabled = true;
        btn.style.opacity = '0.55';
      }
      btn.onclick = () => onStep(item.id);
      wrap.appendChild(btn);
    });

    const doneBtn = document.createElement('button');
    doneBtn.id = 'vine-done-btn';
    doneBtn.textContent = "✓ I've learned!";
    doneBtn.disabled = true;
    doneBtn.style.cssText =
      'padding:12px 22px;border-radius:18px;font-weight:700;background:#1976d2;color:#fff;border:3px solid #1565c0;opacity:0.6;cursor:not-allowed;';
    doneBtn.onclick = () => {
      showWinUI('🌿', 'Great Job!', 'You discovered how vines grow around trees!');
      recordComplete(slug, progressUrl, csrfToken, { vine_progress: Math.round(vineProgress * 100) });
    };
    wrap.appendChild(doneBtn);

    mount.appendChild(wrap);
  }

  function onStep(id) {
    if (stage >= stageSteps.length) {
      return;
    }
    const expected = stageSteps[stage];
    if (id !== expected) {
      updateInstruction(`Try "${labelForStep(expected)}" next.`);
      return;
    }

    stage += 1;
    if (id === 'anchor') {
      rootMesh.scale.setScalar(1);
      vineTarget = 0.25;
    } else if (id === 'climb') {
      vineTarget = 0.82;
    } else if (id === 'leaves') {
      vineTarget = 1;
      leafGroup.visible = true;
      leafTarget = 0;
    }

    unlockNextButton();
    updateInstruction();

    if (stage === stageSteps.length) {
      const doneBtn = document.getElementById('vine-done-btn');
      if (doneBtn) {
        doneBtn.disabled = false;
        doneBtn.style.opacity = '1';
        doneBtn.style.cursor = 'pointer';
      }
    }
  }

  function labelForStep(id) {
    if (id === 'anchor') {
      return 'Anchor at root';
    }
    if (id === 'climb') {
      return 'Climb the trunk';
    }
    return 'Grow leaves';
  }

  function unlockNextButton() {
    const stepButtons = mount.querySelectorAll('button[data-step]');
    stepButtons.forEach((btn, index) => {
      if (index < stage) {
        btn.disabled = true;
        btn.style.opacity = '0.72';
        return;
      }
      if (index === stage) {
        btn.disabled = false;
        btn.style.opacity = '1';
      }
    });
  }

  function updateInstruction(extra = '') {
    if (!instructionEl) {
      return;
    }
    if (stage === 0) {
      instructionEl.textContent = 'Step 1: Vines start by anchoring at the tree base.';
      return;
    }
    if (stage === 1) {
      instructionEl.textContent = 'Step 2: The vine wraps around the trunk as it climbs.';
      return;
    }
    if (stage === 2) {
      instructionEl.textContent = 'Step 3: Leaves spread to capture more sunlight.';
      return;
    }
    instructionEl.textContent = extra || 'Great! Vine growth is complete. Click "I\'ve learned" to finish.';
  }

  function rebuildVine() {
    const pointCount = Math.max(12, Math.ceil(50 * vineProgress));
    const points = [];
    for (let i = 0; i <= pointCount; i++) {
      const u = (i / pointCount) * vineProgress;
      points.push(vineCurve.getPoint(u));
    }
    const curve = new THREE.CatmullRomCurve3(points);
    const tubeSegments = Math.max(8, pointCount);
    const geo = new THREE.TubeGeometry(curve, tubeSegments, 0.1, 12, false);
    if (vineMesh) {
      scene.remove(vineMesh);
      vineMesh.geometry.dispose();
    }
    vineMesh = new THREE.Mesh(
      geo,
      new THREE.MeshStandardMaterial({ color: 0x33691e, roughness: 0.8, metalness: 0 })
    );
    vineMesh.name = 'vine';
    scene.add(vineMesh);
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
    phase += 0.015;

    if (treeGroup) {
      treeGroup.rotation.y += 0.002;
    }

    if (vineProgress < vineTarget) {
      vineProgress = Math.min(vineTarget, vineProgress + 0.008);
      rebuildVine();
    }

    if (leafGroup.visible) {
      leafGroup.children.forEach((leaf, index) => {
        const baseScale = Math.min(1, leafTarget);
        leaf.scale.set(
          1.5 * baseScale,
          0.35 * baseScale,
          baseScale
        );
        leaf.rotation.y = Math.sin(phase + index * 0.8) * 0.25;
      });
      if (leafTarget < 1) {
        leafTarget = Math.min(1, leafTarget + 0.04);
      }
    }

    renderer.render(scene, camera);
  }

  init();
})();
