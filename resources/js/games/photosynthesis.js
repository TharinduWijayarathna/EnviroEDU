/**
 * Photosynthesis for Kids – Platform game.
 * Drag water to the plant, drag sun to the sky, click wind, then flowers bloom.
 */
import * as THREE from 'three';
import { recordComplete as recordProgress, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;

  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, raycaster, pointer;
  let plantBucketGroup, waterBucketGroup, sunMesh, windZone;
  let flowersGroup;
  let state = { watered: false, sunPlaced: false, windDone: false, bloomed: false, completed: false };
  let dragged = null;
  let dragPlane = new THREE.Plane(new THREE.Vector3(0, 0, 1), 0);
  let dragOffset = new THREE.Vector3();
  let intersectPoint = new THREE.Vector3();

  function recordComplete() {
    if (state.completed) {
      return;
    }
    state.completed = true;
    recordProgress(slug, progressUrl, csrfToken, state);
  }

  function createPot(x, z, color) {
    const g = new THREE.Group();
    const body = new THREE.Mesh(
      new THREE.CylinderGeometry(0.55, 0.45, 0.9, 20, 1, true),
      new THREE.MeshStandardMaterial({ color, roughness: 0.8, metalness: 0.1 })
    );
    body.position.y = 0.45;
    body.castShadow = true;
    body.receiveShadow = true;
    g.add(body);
    const rim = new THREE.Mesh(
      new THREE.TorusGeometry(0.58, 0.06, 12, 24),
      new THREE.MeshStandardMaterial({ color: 0x5d4037, roughness: 0.7 })
    );
    rim.rotation.x = Math.PI / 2;
    rim.position.y = 0.9;
    g.add(rim);
    g.position.set(x, 0, z);
    return g;
  }

  function createPlantInBucket() {
    const g = new THREE.Group();
    const pot = createPot(0, 0, 0x6d4c41);
    g.add(pot);

    const stem = new THREE.Group();
    const stemGeo = new THREE.CylinderGeometry(0.06, 0.1, 1.4, 10);
    const stemMat = new THREE.MeshStandardMaterial({ color: 0x33691e, roughness: 0.9 });
    const stemMesh = new THREE.Mesh(stemGeo, stemMat);
    stemMesh.position.y = 0.7;
    stemMesh.castShadow = true;
    stem.add(stemMesh);

    const leafMat = new THREE.MeshStandardMaterial({ color: 0x43a047, roughness: 0.85 });
    for (let i = 0; i < 4; i++) {
      const leaf = new THREE.Mesh(
        new THREE.SphereGeometry(0.22, 12, 8),
        leafMat
      );
      leaf.scale.set(1.4, 0.35, 0.4);
      leaf.position.set(0.12 * (i - 1.5), 0.9 + i * 0.28, 0);
      leaf.rotation.z = (i - 1.5) * 0.25;
      leaf.castShadow = true;
      stem.add(leaf);
    }
    stem.position.y = 0.45;
    g.add(stem);
    g.userData.stem = stem;
    g.position.set(0, 0, 2);
    return g;
  }

  function createFlower(color) {
    const g = new THREE.Group();
    const center = new THREE.Mesh(
      new THREE.SphereGeometry(0.14, 12, 12),
      new THREE.MeshStandardMaterial({ color: 0xffeb3b, roughness: 0.6 })
    );
    g.add(center);
    for (let i = 0; i < 6; i++) {
      const angle = (i / 6) * Math.PI * 2;
      const petal = new THREE.Mesh(
        new THREE.SphereGeometry(0.18, 10, 8),
        new THREE.MeshStandardMaterial({ color, roughness: 0.5 })
      );
      petal.scale.set(1, 0.45, 1);
      petal.position.set(Math.cos(angle) * 0.25, 0.08, Math.sin(angle) * 0.25);
      g.add(petal);
    }
    return g;
  }

  function createWaterBucket() {
    const g = createPot(-4, -2, 0x90a4ae);
    const water = new THREE.Mesh(
      new THREE.CylinderGeometry(0.42, 0.38, 0.35, 24, 1, true),
      new THREE.MeshStandardMaterial({
        color: 0x29b6f6,
        transparent: true,
        opacity: 0.95,
        roughness: 0.2,
        metalness: 0.1,
      })
    );
    water.position.y = 0.55;
    water.castShadow = true;
    g.add(water);
    g.userData.draggable = true;
    g.userData.type = 'water';
    return g;
  }

  function createSun() {
    const g = new THREE.Group();
    const core = new THREE.Mesh(
      new THREE.SphereGeometry(0.55, 24, 24),
      new THREE.MeshBasicMaterial({ color: 0xffeb3b })
    );
    g.add(core);
    const glow = new THREE.Mesh(
      new THREE.SphereGeometry(0.7, 16, 16),
      new THREE.MeshBasicMaterial({ color: 0xffc107, transparent: true, opacity: 0.25 })
    );
    g.add(glow);
    for (let i = 0; i < 12; i++) {
      const angle = (i / 12) * Math.PI * 2;
      const ray = new THREE.Mesh(
        new THREE.BoxGeometry(0.12, 0.5, 0.08),
        new THREE.MeshBasicMaterial({ color: 0xffa000 })
      );
      ray.position.set(Math.cos(angle) * 0.75, Math.sin(angle) * 0.75, 0);
      ray.rotation.z = -angle;
      g.add(ray);
    }
    g.position.set(-5, 2, -3);
    g.userData.draggable = true;
    g.userData.type = 'sun';
    return g;
  }

  function createWindZone() {
    const g = new THREE.Group();
    const pad = new THREE.Mesh(
      new THREE.CylinderGeometry(1.4, 1.4, 0.15, 24, 1, true),
      new THREE.MeshStandardMaterial({
        color: 0x4dd0e1,
        roughness: 0.6,
        transparent: true,
        opacity: 0.9,
        side: THREE.DoubleSide,
      })
    );
    pad.rotation.x = Math.PI / 2;
    pad.position.set(4, 0.08, 0);
    g.add(pad);
    const ring = new THREE.Mesh(
      new THREE.TorusGeometry(1.35, 0.08, 8, 24),
      new THREE.MeshStandardMaterial({ color: 0x00acc1 })
    );
    ring.rotation.x = Math.PI / 2;
    ring.position.set(4, 0.08, 0);
    g.add(ring);
    g.userData.clickable = true;
    g.userData.type = 'wind';
    g.position.y = 0;
    return g;
  }

  function init() {
    const rect = mount.getBoundingClientRect();
    const width = rect.width || 800;
    const height = rect.height || 520;

    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x87ceeb);

    camera = new THREE.PerspectiveCamera(50, width / height, 0.1, 100);
    camera.position.set(0, 3, 10);
    camera.lookAt(0, 1, 2);

    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    mount.appendChild(renderer.domElement);

    const ambient = new THREE.AmbientLight(0xffffff, 0.65);
    scene.add(ambient);
    const dir = new THREE.DirectionalLight(0xffffff, 0.9);
    dir.position.set(5, 12, 5);
    dir.castShadow = true;
    dir.shadow.mapSize.width = 1024;
    dir.shadow.mapSize.height = 1024;
    scene.add(dir);
    const fill = new THREE.DirectionalLight(0xe3f2fd, 0.35);
    fill.position.set(-3, 5, 3);
    scene.add(fill);

    const ground = new THREE.Mesh(
      new THREE.PlaneGeometry(30, 30),
      new THREE.MeshStandardMaterial({ color: 0x689f38, roughness: 0.95 })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.receiveShadow = true;
    scene.add(ground);

    plantBucketGroup = createPlantInBucket();
    scene.add(plantBucketGroup);
    flowersGroup = new THREE.Group();
    flowersGroup.position.set(0, 0.45, 2);
    flowersGroup.visible = false;
    scene.add(flowersGroup);

    waterBucketGroup = createWaterBucket();
    scene.add(waterBucketGroup);

    sunMesh = createSun();
    scene.add(sunMesh);

    windZone = createWindZone();
    scene.add(windZone);

    raycaster = new THREE.Raycaster();
    pointer = new THREE.Vector2();

    mount.addEventListener('pointermove', onPointerMove);
    mount.addEventListener('pointerdown', onPointerDown);
    mount.addEventListener('pointerup', onPointerUp);
    mount.addEventListener('pointerleave', onPointerUp);
    window.addEventListener('resize', onResize);

    addInstructionUI();
    addWindButton();
    animate();
  }

  function addInstructionUI() {
    const div = document.createElement('div');
    div.id = 'photosynthesis-instructions';
    div.style.cssText =
      'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,0.96);padding:12px 24px;border-radius:20px;font-weight:700;color:#1a3c34;z-index:10;pointer-events:none;font-size:1rem;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.12);border:2px solid rgba(78,205,196,0.5);line-height:1.4;max-width:92%;';
    mount.style.position = 'relative';
    mount.appendChild(div);
    updateInstructionUI();
  }

  function updateInstructionUI(extra = '') {
    const div = document.getElementById('photosynthesis-instructions');
    if (!div) {
      return;
    }
    const checklist = [
      `${state.watered ? '✓' : '○'} Drag 💧 water to the plant`,
      `${state.sunPlaced ? '✓' : '○'} Drag ☀️ sun up to the sky`,
      `${state.windDone ? '✓' : '○'} Click 🌬️ Wind`,
    ];
    const hint = extra || (!state.sunPlaced || !state.watered ? 'Finish water and sun first, then use wind.' : 'Great! Click wind to help pollination.');
    div.textContent = `${checklist.join('   ')} | ${hint}`;
  }

  function addWindButton() {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.id = 'photosynthesis-wind-btn';
    btn.innerHTML = '🌬️ Wind';
    btn.disabled = true;
    btn.style.cssText =
      'position:absolute;bottom:20px;right:20px;padding:14px 28px;background:#b0bec5;color:#37474f;border:3px solid #90a4ae;border-radius:20px;font-weight:700;font-size:1.1rem;cursor:not-allowed;z-index:10;box-shadow:0 4px 12px rgba(0,0,0,0.15);opacity:0.8;';
    btn.addEventListener('click', () => triggerWind());
    mount.appendChild(btn);
  }

  function updateWindButtonState() {
    const btn = document.getElementById('photosynthesis-wind-btn');
    if (!btn || state.windDone) {
      return;
    }
    const canUseWind = state.watered && state.sunPlaced;
    btn.disabled = !canUseWind;
    if (canUseWind) {
      btn.style.background = '#4dd0e1';
      btn.style.borderColor = '#00acc1';
      btn.style.color = '#1a3c34';
      btn.style.cursor = 'pointer';
      btn.style.opacity = '1';
    }
  }

  function onResize() {
    const rect = mount.getBoundingClientRect();
    const width = rect.width || 800;
    const height = rect.height || 520;
    if (!camera || !renderer) return;
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
  }

  function setPointer(ev) {
    const rect = mount.getBoundingClientRect();
    pointer.x = ((ev.clientX - rect.left) / rect.width) * 2 - 1;
    pointer.y = -((ev.clientY - rect.top) / rect.height) * 2 + 1;
  }

  function getIntersections() {
    raycaster.setFromCamera(pointer, camera);
    const all = [waterBucketGroup, sunMesh, windZone, plantBucketGroup];
    return raycaster.intersectObjects(all, true);
  }

  function onPointerDown(ev) {
    setPointer(ev);
    const is = getIntersections();
    if (is.length === 0) return;
    let obj = is[0].object;
    while (obj && obj !== scene) {
      if (obj.userData.draggable || obj.userData.clickable) break;
      obj = obj.parent;
    }
    if (!obj || obj === scene) return;
    if (obj.userData.draggable) {
      dragged = obj;
      const normal = new THREE.Vector3();
      camera.getWorldDirection(normal);
      dragPlane.setFromNormalAndCoplanarPoint(normal, obj.position);
      raycaster.ray.intersectPlane(dragPlane, intersectPoint);
      dragOffset.copy(obj.position).sub(intersectPoint);
    }
    if (obj.userData.clickable && obj.userData.type === 'wind') {
      triggerWind();
    }
  }

  function onPointerMove(ev) {
    setPointer(ev);
    if (dragged) {
      dragPlane.setFromNormalAndCoplanarPoint(
        camera.getWorldDirection(new THREE.Vector3()),
        dragged.position
      );
      raycaster.setFromCamera(pointer, camera);
      if (raycaster.ray.intersectPlane(dragPlane, intersectPoint)) {
        dragged.position.copy(intersectPoint.add(dragOffset));
        dragged.position.x = Math.max(-6, Math.min(6, dragged.position.x));
        dragged.position.y = Math.max(0.2, Math.min(7, dragged.position.y));
        dragged.position.z = Math.max(-6, Math.min(6, dragged.position.z));
      }
      checkDropZones();
      return;
    }
  }

  function checkDropZones() {
    if (!dragged) return;
    if (dragged.userData.type === 'water') {
      const dist = dragged.position.distanceTo(plantBucketGroup.position);
      if (dist < 2.5 && !state.watered) {
        state.watered = true;
        playWaterPour();
        dragged.visible = false;
        dragged = null;
        updateWindButtonState();
        updateInstructionUI();
      }
    }
    if (dragged.userData.type === 'sun') {
      if (dragged.position.y > 4 && !state.sunPlaced) {
        state.sunPlaced = true;
        dragged.position.set(0, 6, 0);
        dragged = null;
        updateWindButtonState();
        updateInstructionUI();
      }
    }
  }

  function onPointerUp() {
    if (dragged) {
      checkDropZones();
      if (dragged && dragged.userData.type === 'sun' && dragged.position.y < 3) {
        dragged.position.set(-5, 2, -3);
      }
      dragged = null;
    }
  }

  function playWaterPour() {
    const drop = new THREE.Mesh(
      new THREE.SphereGeometry(0.25, 12, 12),
      new THREE.MeshStandardMaterial({ color: 0x29b6f6 })
    );
    drop.position.copy(plantBucketGroup.position).add(new THREE.Vector3(0.25, 0.6, 0));
    scene.add(drop);
    let t = 0;
    function tick() {
      t += 0.06;
      drop.position.y -= 0.12;
      drop.scale.multiplyScalar(0.92);
      if (t < 1.2) requestAnimationFrame(tick);
      else scene.remove(drop);
    }
    tick();
  }

  function triggerWind() {
    if (state.windDone) return;
    if (!state.watered || !state.sunPlaced) {
      updateInstructionUI('Do water + sun first, then use wind.');
      return;
    }
    state.windDone = true;
    const btn = document.getElementById('photosynthesis-wind-btn');
    if (btn) {
      btn.disabled = true;
      btn.style.background = '#81d4fa';
      btn.style.borderColor = '#0288d1';
      btn.style.cursor = 'default';
      btn.textContent = '✓ Wind';
    }
    updateInstructionUI('Awesome! Wind helps pollination. Watch the flowers bloom.');
    windZone.children.forEach((c) => {
      if (c.material && c.material.opacity !== undefined) {
        c.material.opacity = 0.95;
        c.material.color.setHex(0x81d4fa);
      }
    });
    let windT = 0;
    function windAnim() {
      windT += 0.02;
      if (plantBucketGroup && plantBucketGroup.userData.stem) {
        plantBucketGroup.userData.stem.rotation.z = Math.sin(windT * 10) * 0.06;
      }
      if (windT < 1.5) requestAnimationFrame(windAnim);
      else tryBloom();
    }
    windAnim();
  }

  function tryBloom() {
    if (!state.watered || !state.sunPlaced || !state.windDone || state.bloomed) return;
    state.bloomed = true;
    updateInstructionUI('Great! Flowers are blooming now.');
    flowersGroup.visible = true;
    const colors = [0xec407a, 0xffca28, 0xab47bc];
    for (let i = 0; i < 3; i++) {
      const flower = createFlower(colors[i % colors.length]);
      flower.position.set((i - 1) * 0.4, 0.95 + i * 0.18, 0);
      flower.scale.setScalar(0);
      flowersGroup.add(flower);
      (function (fl) {
        let s = 0;
        function grow() {
          s += 0.04;
          fl.scale.setScalar(Math.min(s, 1));
          if (s < 1) requestAnimationFrame(grow);
        }
        setTimeout(() => grow(), i * 200);
      })(flower);
    }
    setTimeout(() => {
      showWinUI('🌻', 'Great Job!', 'You helped the plant grow with water, sunlight, and air. Flowers bloomed!');
      recordComplete();
    }, 2200);
  }

  function animate() {
    requestAnimationFrame(animate);
    const t = Date.now() * 0.001;
    if (sunMesh && sunMesh.visible && !dragged) {
      sunMesh.rotation.z += 0.004;
    }
    if (plantBucketGroup && plantBucketGroup.userData.stem && !state.windDone) {
      plantBucketGroup.userData.stem.rotation.z = Math.sin(t * 0.8) * 0.025;
    }
    renderer.render(scene, camera);
  }

  init();
})();
