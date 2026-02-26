/**
 * How a Lunar Eclipse Works – Earth blocks sunlight from the Moon.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, sunMesh, earthMesh, moonMesh;
  let t = 0;

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0a0a1a);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 0, 12);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0x222244, 0.2));

    sunMesh = new THREE.Mesh(
      new THREE.SphereGeometry(1, 32, 32),
      new THREE.MeshBasicMaterial({ color: 0xffeb3b })
    );
    sunMesh.position.set(-6, 0, 0);
    const sunLight = new THREE.DirectionalLight(0xffffff, 0.8);
    sunLight.position.copy(sunMesh.position);
    scene.add(sunLight);
    scene.add(sunMesh);

    earthMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.7, 32, 32),
      new THREE.MeshStandardMaterial({ color: 0x1565c0 })
    );
    earthMesh.position.set(0, 0, 0);
    scene.add(earthMesh);

    moonMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.4, 24, 24),
      new THREE.MeshStandardMaterial({ color: 0xb0bec5 })
    );
    moonMesh.position.set(3, 0, 0);
    scene.add(moonMesh);

    const label = document.createElement('div');
    label.style.cssText = 'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.8);color:#fff;padding:12px 24px;border-radius:16px;font-weight:700;z-index:10;text-align:center;';
    label.textContent = '🌒 Lunar eclipse: Earth blocks sunlight from the Moon';
    mount.style.position = 'relative';
    mount.appendChild(label);

    const btn = document.createElement('button');
    btn.textContent = "✓ I've learned!";
    btn.style.cssText = 'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);padding:14px 28px;border-radius:20px;font-weight:700;background:#37474f;color:#fff;border:none;cursor:pointer;z-index:10;';
    btn.onclick = () => {
      showWinUI('🌒', 'Great Job!', 'You learned how a lunar eclipse works!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    mount.appendChild(btn);

    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    t += 0.006;
    if (moonMesh) {
      moonMesh.position.x = Math.cos(t) * 3;
      moonMesh.position.z = Math.sin(t) * 3;
    }
    renderer.render(scene, camera);
  }

  init();
})();
