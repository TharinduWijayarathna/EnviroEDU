/**
 * Star Patterns – Learn 5–9 common constellations one by one. Complete each to finish the game.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  const constellations = [
    { name: 'Big Dipper', points: [[0, 1.8], [0.6, 1.5], [1.2, 1.2], [1.8, 0.9], [1.5, 0.5], [0.9, 0.3], [0.3, 0.6]] },
    { name: 'Orion', points: [[0, 1.4], [-0.5, 1], [0.5, 1], [0, 0.6], [-0.4, 0.2], [0.4, 0.2], [0, 0]] },
    { name: 'Cassiopeia', points: [[-1.2, 0.8], [-0.5, 1.2], [0.2, 0.6], [0.8, 1], [1.4, 0.5]] },
    { name: 'Leo', points: [[-0.8, 0.9], [-0.4, 1.2], [0.2, 1], [0.6, 0.6], [0.4, 0.2], [0, 0], [-0.6, 0.3], [-0.8, 0.9]] },
    { name: 'Cygnus', points: [[-1, 0], [-0.3, 0.8], [0, 0.4], [0.3, 0.9], [1, 0.2], [0.3, 0]] },
    { name: 'Scorpius', points: [[1.2, 0.5], [0.6, 0.6], [0, 0.5], [-0.5, 0.4], [-0.8, 0.2], [-1, -0.2], [-0.6, -0.5]] },
    { name: 'Ursa Minor', points: [[0, 1.5], [0.4, 1.2], [0.9, 1.4], [1.2, 1], [1, 0.5], [0.5, 0.3], [0, 0.6]] },
    { name: 'Pegasus', points: [[-0.8, 0.6], [-0.8, -0.2], [0.2, -0.2], [0.6, 0.2], [0.2, 0.6], [-0.8, 0.6]] },
  ];

  let scene, camera, renderer, starsGroup, linesGroup;
  let currentIndex = 0;
  let labelEl, nextBtn, doneBtn, progressEl;

  function buildConstellation(c, scale, offsetX, offsetY) {
    const starMeshes = [];
    const pts = c.points.map((p) => new THREE.Vector3(p[0] * scale + offsetX, p[1] * scale + offsetY, 0));
    pts.forEach((pt) => {
      const star = new THREE.Mesh(
        new THREE.SphereGeometry(0.08, 16, 16),
        new THREE.MeshBasicMaterial({ color: 0xffffcc })
      );
      star.position.copy(pt);
      starMeshes.push(star);
    });
    const lineGeo = new THREE.BufferGeometry().setFromPoints(pts);
    const line = new THREE.Line(
      lineGeo,
      new THREE.LineBasicMaterial({ color: 0x88aaff, linewidth: 2 })
    );
    return { starMeshes, line };
  }

  function showConstellation(index) {
    if (starsGroup) {
      starsGroup.children.forEach((c) => {
        if (c.geometry) c.geometry.dispose();
        if (c.material) c.material.dispose();
      });
      starsGroup.clear();
    }
    if (linesGroup) {
      linesGroup.children.forEach((c) => {
        if (c.geometry) c.geometry.dispose();
        if (c.material) c.material.dispose();
      });
      linesGroup.clear();
    }

    const c = constellations[index];
    const scale = 1.1;
    const { starMeshes, line } = buildConstellation(c, scale, 0, 0);
    starMeshes.forEach((m) => starsGroup.add(m));
    linesGroup.add(line);

    if (labelEl) labelEl.textContent = `⭐ ${c.name}`;
    if (progressEl) progressEl.textContent = `Pattern ${index + 1} of ${constellations.length}`;
    if (nextBtn) {
      nextBtn.style.display = index < constellations.length - 1 ? 'inline-block' : 'none';
    }
    if (doneBtn) {
      doneBtn.style.display = index === constellations.length - 1 ? 'inline-block' : 'none';
    }
  }

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0a0a1a);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 0, 5);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 0.4));
    const light = new THREE.PointLight(0xffffff, 0.8);
    light.position.set(0, 0, 4);
    scene.add(light);

    starsGroup = new THREE.Group();
    linesGroup = new THREE.Group();
    scene.add(starsGroup);
    scene.add(linesGroup);

    progressEl = document.createElement('div');
    progressEl.style.cssText =
      'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.75);color:#fff;padding:8px 16px;border-radius:12px;font-weight:700;z-index:10;';
    mount.style.position = 'relative';
    mount.appendChild(progressEl);

    labelEl = document.createElement('div');
    labelEl.style.cssText =
      'position:absolute;top:48px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.7);color:#ffcc00;padding:10px 20px;border-radius:12px;font-weight:700;z-index:10;';
    mount.appendChild(labelEl);

    const wrap = document.createElement('div');
    wrap.style.cssText =
      'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:12px;justify-content:center;flex-wrap:wrap;z-index:10;';

    nextBtn = document.createElement('button');
    nextBtn.textContent = 'Next pattern →';
    nextBtn.style.cssText =
      'padding:12px 24px;border-radius:18px;font-weight:700;background:#ffc107;color:#1a1a1a;border:none;cursor:pointer;';
    nextBtn.onclick = () => {
      currentIndex = Math.min(currentIndex + 1, constellations.length - 1);
      showConstellation(currentIndex);
    };
    wrap.appendChild(nextBtn);

    doneBtn = document.createElement('button');
    doneBtn.textContent = "✓ I've learned!";
    doneBtn.style.cssText =
      'padding:12px 24px;border-radius:18px;font-weight:700;background:#2196f3;color:#fff;border:none;cursor:pointer;display:none;';
    doneBtn.onclick = () => {
      showWinUI('⭐', 'Great Job!', 'You learned about star patterns in the sky!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    wrap.appendChild(doneBtn);

    mount.appendChild(wrap);

    showConstellation(0);
    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    if (starsGroup) starsGroup.rotation.z += 0.002;
    renderer.render(scene, camera);
  }

  init();
})();
