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
    camera.position.set(0, 2.4, 6.8);
    camera.lookAt(0, 1, 0);
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

    treeGroup = new THREE.Group();
    const trunk = new THREE.Mesh(
      new THREE.CylinderGeometry(0.4, 0.5, 2.5, 12),
      new THREE.MeshStandardMaterial({ color: 0x5d4037 })
    );
    trunk.position.y = 1.25;
    treeGroup.add(trunk);
    const leaves = new THREE.Mesh(
      new THREE.SphereGeometry(1.2, 16, 12),
      new THREE.MeshStandardMaterial({ color: 0x2e7d32 })
    );
    leaves.position.y = 2.8;
    treeGroup.add(leaves);
    treeGroup.position.z = 0;
    scene.add(treeGroup);

    vineCurve = new THREE.CatmullRomCurve3([
      new THREE.Vector3(0.5, 0, 0),
      new THREE.Vector3(0.52, 0.35, 0.35),
      new THREE.Vector3(0.38, 0.8, -0.28),
      new THREE.Vector3(0.48, 1.25, 0.24),
      new THREE.Vector3(0.32, 1.75, -0.15),
      new THREE.Vector3(0.3, 2.25, 0.02),
    ]);

    rootMesh = new THREE.Mesh(
      new THREE.TorusGeometry(0.48, 0.06, 8, 24),
      new THREE.MeshStandardMaterial({ color: 0x558b2f })
    );
    rootMesh.rotation.x = Math.PI / 2;
    rootMesh.position.y = 0.04;
    rootMesh.scale.setScalar(0.01);
    scene.add(rootMesh);

    leafGroup = new THREE.Group();
    leafGroup.visible = false;
    for (let i = 0; i < 6; i++) {
      const leaf = new THREE.Mesh(
        new THREE.SphereGeometry(0.13, 8, 8),
        new THREE.MeshStandardMaterial({ color: 0x43a047 })
      );
      leaf.scale.set(1.5, 0.35, 1);
      const p = vineCurve.getPoint(0.24 + i * 0.12);
      leaf.position.copy(p).add(new THREE.Vector3(i % 2 ? 0.12 : -0.12, 0.06, i % 2 ? 0.05 : -0.05));
      leaf.rotation.z = i % 2 ? 0.6 : -0.6;
      leaf.scale.multiplyScalar(0.01);
      leafGroup.add(leaf);
    }
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
    const pointCount = Math.max(6, Math.ceil(45 * vineProgress));
    const points = vineCurve.getPoints(pointCount);
    const curve = new THREE.CatmullRomCurve3(points);
    const geo = new THREE.TubeGeometry(curve, Math.max(12, pointCount), 0.08, 10, false);
    if (vineMesh) {
      scene.remove(vineMesh);
      vineMesh.geometry.dispose();
    }
    vineMesh = new THREE.Mesh(
      geo,
      new THREE.MeshStandardMaterial({ color: 0x558b2f })
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
