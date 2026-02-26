/**
 * Mosquito Life Cycle – egg, larva, pupa, adult.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, stage = 0;

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0xe3f2fd);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 0, 6);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 0.9));
    const dir = new THREE.DirectionalLight(0xffffff, 0.6);
    dir.position.set(5, 5, 5);
    scene.add(dir);

    const water = new THREE.Mesh(
      new THREE.PlaneGeometry(15, 8),
      new THREE.MeshStandardMaterial({ color: 0x4fc3f7, transparent: true, opacity: 0.7 })
    );
    water.rotation.x = -Math.PI / 2;
    water.position.y = -0.5;
    scene.add(water);

    addStageMeshes();
    addUI();
    animate();
  }

  function addStageMeshes() {
    const names = ['egg', 'larva', 'pupa', 'adult'];
    scene.children.filter((c) => names.includes(c.name)).forEach((c) => scene.remove(c));

    if (stage === 0) {
      const egg = new THREE.Mesh(
        new THREE.SphereGeometry(0.15, 12, 12),
        new THREE.MeshStandardMaterial({ color: 0xffffff })
      );
      egg.position.set(0, 0.2, 0);
      egg.name = 'egg';
      scene.add(egg);
    } else if (stage === 1) {
      const larva = new THREE.Mesh(
        new THREE.CylinderGeometry(0.08, 0.1, 0.5, 8),
        new THREE.MeshStandardMaterial({ color: 0x81c784 })
      );
      larva.rotation.z = Math.PI / 2;
      larva.position.set(0, 0.2, 0);
      larva.name = 'larva';
      scene.add(larva);
    } else if (stage === 2) {
      const pupa = new THREE.Mesh(
        new THREE.SphereGeometry(0.2, 12, 12),
        new THREE.MeshStandardMaterial({ color: 0x78909c })
      );
      pupa.position.set(0, 0.25, 0);
      pupa.name = 'pupa';
      scene.add(pupa);
    } else {
      const body = new THREE.Mesh(
        new THREE.CylinderGeometry(0.06, 0.06, 0.4, 8),
        new THREE.MeshStandardMaterial({ color: 0x37474f })
      );
      body.rotation.z = Math.PI / 2;
      body.position.set(0, 0.3, 0);
      body.name = 'adult';
      scene.add(body);
    }
  }

  function addUI() {
    const wrap = document.createElement('div');
    wrap.style.cssText = 'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:8px;flex-wrap:wrap;justify-content:center;z-index:10;';
    ['1️⃣ Egg', '2️⃣ Larva', '3️⃣ Pupa', '4️⃣ Adult', "✓ I've learned!"].forEach((text, i) => {
      const btn = document.createElement('button');
      btn.textContent = text;
      btn.style.cssText = 'padding:10px 16px;border-radius:16px;font-weight:700;background:#fff;border:2px solid #0288d1;cursor:pointer;';
      btn.onclick = () => {
        if (i < 4) {
          stage = i;
          addStageMeshes();
        } else {
          showWinUI('🦟', 'Great Job!', 'You learned the four stages of the mosquito life cycle!');
          recordComplete(slug, progressUrl, csrfToken, {});
        }
      };
      wrap.appendChild(btn);
    });
    mount.style.position = 'relative';
    mount.appendChild(wrap);
  }

  function animate() {
    requestAnimationFrame(animate);
    renderer.render(scene, camera);
  }

  init();
})();
