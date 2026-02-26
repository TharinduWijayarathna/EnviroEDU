/**
 * How a Solar Eclipse Works – Moon passes between Sun and Earth.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, sunMesh, moonMesh, earthMesh;
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

    scene.add(new THREE.AmbientLight(0x222244, 0.3));

    sunMesh = new THREE.Mesh(
      new THREE.SphereGeometry(1.2, 32, 32),
      new THREE.MeshBasicMaterial({ color: 0xffeb3b })
    );
    sunMesh.position.set(-5, 0, 0);
    scene.add(sunMesh);

    moonMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.35, 24, 24),
      new THREE.MeshStandardMaterial({ color: 0x78909c })
    );
    moonMesh.position.set(0, 0, 0);
    scene.add(moonMesh);

    earthMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.5, 24, 24),
      new THREE.MeshStandardMaterial({ color: 0x2196f3 })
    );
    earthMesh.position.set(4, 0, 0);
    scene.add(earthMesh);

    const label = document.createElement('div');
    label.style.cssText = 'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.8);color:#fff;padding:12px 24px;border-radius:16px;font-weight:700;z-index:10;text-align:center;';
    label.textContent = '🌑 Solar eclipse: Moon moves between Sun and Earth';
    mount.style.position = 'relative';
    mount.appendChild(label);

    const btn = document.createElement('button');
    btn.textContent = "✓ I've learned!";
    btn.style.cssText = 'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);padding:14px 28px;border-radius:20px;font-weight:700;background:#5c6bc0;color:#fff;border:none;cursor:pointer;z-index:10;';
    btn.onclick = () => {
      showWinUI('🌑', 'Great Job!', 'You learned how a solar eclipse works!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    mount.appendChild(btn);

    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    t += 0.008;
    if (moonMesh) {
      moonMesh.position.x = Math.cos(t) * 2.5;
      moonMesh.position.z = Math.sin(t) * 2.5;
    }
    renderer.render(scene, camera);
  }

  init();
})();
