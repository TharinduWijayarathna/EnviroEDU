/**
 * The Water Cycle – evaporation, clouds, precipitation.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, ocean, cloudGroup;
  let phase = 0;

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
    const dir = new THREE.DirectionalLight(0xffffff, 0.7);
    dir.position.set(5, 10, 5);
    scene.add(dir);

    const ground = new THREE.Mesh(
      new THREE.PlaneGeometry(30, 30),
      new THREE.MeshStandardMaterial({ color: 0x4db6ac })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -1;
    scene.add(ground);

    ocean = new THREE.Mesh(
      new THREE.PlaneGeometry(20, 8),
      new THREE.MeshStandardMaterial({ color: 0x0288d1, transparent: true, opacity: 0.9 })
    );
    ocean.rotation.x = -Math.PI / 2;
    ocean.position.set(0, -0.95, -2);
    scene.add(ocean);

    cloudGroup = new THREE.Group();
    [0, 1, 2].forEach((i) => {
      const c = new THREE.Mesh(
        new THREE.SphereGeometry(0.5 + i * 0.2, 8, 8),
        new THREE.MeshBasicMaterial({ color: 0xffffff })
      );
      c.position.set(i * 0.6 - 0.6, 1.5 + i * 0.2, 0);
      cloudGroup.add(c);
    });
    cloudGroup.position.x = 2;
    scene.add(cloudGroup);

    const label = document.createElement('div');
    label.style.cssText = 'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,0.95);padding:12px 20px;border-radius:16px;font-weight:700;color:#1a3c34;z-index:10;text-align:center;';
    label.textContent = '💧 Water evaporates → clouds → rain back to ocean';
    mount.style.position = 'relative';
    mount.appendChild(label);

    const btn = document.createElement('button');
    btn.textContent = "✓ I've learned!";
    btn.style.cssText = 'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);padding:14px 28px;border-radius:20px;font-weight:700;background:#0288d1;color:#fff;border:none;cursor:pointer;z-index:10;';
    btn.onclick = () => {
      showWinUI('💧', 'Great Job!', 'You learned how water moves around Earth!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    mount.appendChild(btn);

    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    phase += 0.02;
    if (cloudGroup) {
      cloudGroup.position.y = 1.5 + Math.sin(phase) * 0.2;
      cloudGroup.position.x = 2 + Math.sin(phase * 0.5) * 0.5;
    }
    renderer.render(scene, camera);
  }

  init();
})();
